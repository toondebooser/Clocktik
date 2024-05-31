<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Carbon;

class UpdateTimesheetController extends Controller
{
    public function updateForm($id, $timesheet)
    {

        $worker = User::find($id);
        $timesheet = Timesheet::find($timesheet);
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
        return view('updateTimesheet', ['worker' => $worker, 'timesheet' => $timesheet, 'startShift' => $startShift, 'endShift' => $endShift, 'startBreak' => $startBreak, 'endBreak' => $endBreak, 'monthString' =>$monthString]);
    }
    
    public function updateTimesheet(Request $request)
    {
        $dayType = $request->input('dayType');
        $id = $request->id;
        $worker = User::find($id);
        $timesheetController = new TimesheetController();
        $timesheet = Timesheet::find($request->timesheet);
        if ($timesheet === null) {
            $postData = [
                'worker' => $id,
            ];

            return redirect()->route('getData', $postData)->with('error', $worker->name.' heeft juist ingeklokt. ');
        }
        $type = $request->updateSpecial;
        $type == null ? $type = $timesheet->type : null;
        $date = $timesheet->Month;
        if ($dayType == "onbetaald" && $type !== 'workday' ) {
            $timesheet->accountableHours = 0;
            $timesheet->type = $type;
            $save = $timesheet->save();
            $fetchTotal = $timesheetController->calculateUserTotal($date, $id);
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
            $timesheet->accountableHours = 7.6;
            $timesheet->type = $type;
            $save = $timesheet->save();
            $fetchTotal = $timesheetController->calculateUserTotal($date, $id);
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
            $clockedHours = $timesheetController->calculateClockedHours($startWork, $stopWork);
            $breakHours = $timesheetController->calculateBreakHours($startBreak, $endBreak);
            $regularHours = $clockedHours - $breakHours;
            $timesheet->ClockedIn = $startWork;
            $timesheet->ClockedOut = $stopWork;
            $timesheet->BreakStart = $startBreak;
            $timesheet->BreakStop = $endBreak;
            $timesheet->BreakHours = $breakHours;
            $balanceResult = $timesheetController->calculateHourBalance($regularHours, $date, $timesheet->Weekend, $timesheet, 'update');
            $userTotal = $timesheetController->calculateUserTotal($date, $id);
            if ($balanceResult && $userTotal == true) return redirect()->route('myWorkers');
        }
    }
}
