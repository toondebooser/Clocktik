<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Carbon;
use App\Http\Controllers\JsonController;
use App\Models\Usertotal;

class UpdateTimesheetController extends Controller
{
    public function fetchUserTotal($date, $id)
    {
        $newUserTotal = new Usertotal;
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $userId = $id;
        $userTotal = $newUserTotal
            ->where('UserID', '=', $userId)
            ->whereMonth('Month', '=', $date)
            ->whereYear('Month', '=', $date)
            ->first();
        if ($userTotal == null) {
            $newUserTotal->UserId = $userId;
            $newUserTotal->Month = $date;
            $newUserTotal->RegularHours = 0;
            $newUserTotal->BreakHours = 0;
            $newUserTotal->OverTime = 0;
            $newUserTotal->save();
            $userTotal = $newUserTotal
                ->where('UserID', '=', $userId)
                ->whereMonth('Month', '=', $date)
                ->whereYear('Month', '=', $date)
                ->first();
        }
        return $userTotal;
    }
    public function calculateUserTotal($date, $id)
    {
        $userTotal = $this->fetchUserTotal($date, $id);
        is_string($date) ? $date = Carbon::parse($date) : null;
        $userId = $id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('OverTime');
        }

        return $userTotal->save();
    }
    public function calculateDecimal($start, $end)
    {
        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;
        if ($start === null) {
            return 0;
        }


        $diffInMin = $end->diffInMinutes($start);
        $decimalTime = round($diffInMin / 60, 2);

        return $decimalTime;
    }
    public function updateForm($id, $timesheet)
    {

        $worker = User::find($id);
        $timesheet = Timesheet::find($timesheet);
        $jsonsMission = new JsonController;
        $json = $jsonsMission->callJson($timesheet);
        if ($timesheet === null) {
            $postData = [
                'worker' => $id,
            ];

            return redirect()->route('getData', $postData)->with('error', $worker->name.' heeft juist ingeklokt. ');
        }
        $startShift = Carbon::parse($timesheet->ClockedIn)->format('H:i');
        $endShift = Carbon::parse($timesheet->ClockedOut)->format('H:i');
        $startBreak = $timesheet->BreakStart ? Carbon::parse($timesheet->BreakStart)->format('H:i') : null;
        $endBreak = $timesheet->BreakStop ? Carbon::parse($timesheet->BreakStop)->format('H:i') : null;
        $monthString = Carbon::parse($timesheet->Month)->format('d/m/Y');
        
        return view('updateTimesheet', ['json'=> $json, 'worker' => $worker, 'timesheet' => $timesheet, 'startShift' => $startShift, 'endShift' => $endShift, 'startBreak' => $startBreak, 'endBreak' => $endBreak, 'monthString' =>$monthString]);
    }
    
    public function updateTimesheet(Request $request)
    {
        $dayType = $request->input('dayType');
        $id = $request->id;
        $worker = User::find($id);
        // $timesheetController = new TimesheetController();
        $timesheet = Timesheet::find($request->timesheet);
        // if ($timesheet === null) {
        //     $postData = [
        //         'worker' => $id,
        //     ];

        //     return redirect()->route('getData', $postData)->with('error', $worker->name.' heeft juist ingeklokt. ');
        // }
        $type = $request->updateSpecial;
        $type == null ? $type = $timesheet->type : null;
        $date = $timesheet->Month;
        if ($dayType == "onbetaald" && $type !== 'workday' ) {
            $timesheet->fill([
                'accountableHours' => 0,
                'type' => $type,
            ]);
            $save = $timesheet->save();
            $fetchTotal = $this->calculateUserTotal($date, $id);
            if ($save == true && $fetchTotal == true) {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData);
            } else {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        } elseif ($type !== 'workday') {
            $timesheet->fill([
                'accountableHours' => 7.6,
                'type' => $type,
            ]);
            $save = $timesheet->save();
            $fetchTotal = $this->calculateUserTotal($date, $id);
            if ($save == true && $fetchTotal == true) { {
                    $postData = [
                        'worker' => $id,
                    ];

                    return redirect()->route('getData', $postData);
                }
            } else {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        } else {
            $startWork = Carbon::parse($date . ' ' . $request->startTime, 'Europe/Brussels');
            $stopWork = Carbon::parse($date . ' ' . $request->endTime, 'Europe/Brussels');
            $startBreak = Carbon::parse($date . ' ' . $request->startBreak, 'Europe/Brussels');
            $endBreak = Carbon::parse($date . ' ' . $request->endBreak, 'Europe/Brussels');
            $breakHours = $this->calculateDecimal($startBreak, $endBreak);
            $regularHours = $this->calculateDecimal($startWork, $stopWork) - $breakHours;
            $timesheet->fill([
                'ClockedIn' => $startWork,
                'ClockedOut' => $stopWork,
                'BreakStart' => $startBreak,
                'BreakStop' => $endBreak,
                'BreakHours' => $breakHours,
                'RegularHours' => $regularHours > 7.6 ? 7.6 : $regularHours,
                'OverTime' => $regularHours - 7.6,
                
            ]);
            $timesheet->save();
            $userTotal = $this->calculateUserTotal($date, $id);
            if ( $userTotal == true) return redirect()->route('myWorkers');
        }
    }
}
