<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;
use Illuminate\Support\Carbon;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;

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
        
        return view('updateTimesheet', [ 'worker' => $worker, 'timesheet' => $timesheet, 'startShift' => $startShift, 'endShift' => $endShift, 'startBreak' => $startBreak, 'endBreak' => $endBreak, 'monthString' =>$monthString]);
    }
    
    public function updateTimesheet(Request $request)
    {
        $dayType = $request->input('dayType');
        $id = $request->id;
        // $worker = User::find($id);
        $timesheet = Timesheet::find($request->timesheet);
     
        $type = $request->updateSpecial;
        $type == null ? $type = $timesheet->type : null;
        $date = $timesheet->Month;
        if ($dayType == "onbetaald" && $type !== 'workday' ) {
            $timesheet->fill([
                'accountableHours' => 0,
                'type' => $type,
            ]);
            $save = $timesheet->save();
            $fetchTotal = CalculateUtility::calculateUserTotal($date, $id);
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
            $fetchTotal = CalculateUtility::calculateUserTotal($date, $id);
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
            // $startWork = Carbon::parse($date . ' ' . $request->startTime, 'Europe/Brussels');
            // $stopWork = Carbon::parse($date . ' ' . $request->endTime, 'Europe/Brussels');
            // $startBreak = Carbon::parse($date . ' ' . $request->startBreak, 'Europe/Brussels');
            // $endBreak = Carbon::parse($date . ' ' . $request->endBreak, 'Europe/Brussels');
            // $breakHours = CalculateUtility::calculateDecimal($startBreak, $endBreak);
            // $regularHours = CalculateUtility::calculateDecimal($startWork, $stopWork) - $breakHours;
            // $timesheet->fill([
            //     'ClockedIn' => $startWork,
            //     'ClockedOut' => $stopWork,
            //     'BreakStart' => $startBreak,
            //     'BreakStop' => $endBreak,
            //     'BreakHours' => $breakHours,
            //     'RegularHours' => $regularHours > 7.6 ? 7.6 : $regularHours,
            //     'OverTime' => $regularHours - 7.6,
                
            // ]);

            $userRow = (object) [
                'UserId' => $id,
                'StartWork' => Carbon::parse($date . ' ' . $request->input('startTime'), 'Europe/Brussels'),
                'StopWork' => Carbon::parse($date . ' ' . $request->input('endTime'), 'Europe/Brussels'),
                'StartBreak' => Carbon::parse($date . ' ' . $request->input('startBreak'), 'Europe/Brussels'),
                'EndBreak' => Carbon::parse($date . ' ' . $request->input('endBreak'), 'Europe/Brussels'),
                'Weekend' => $weekend ?? false,
                'userNote' => $userNote ?? null,
            ];
            $timeloggingUtility = new TimeloggingUtility;
            $addTimesheet = $timeloggingUtility->logTimeEntry($userRow, $id, $timesheet);
           
            if ($addTimesheet) return redirect()->route('myWorkers');
        
            // $timesheet->save();
            // $userTotal = CalculateUtility::calculateUserTotal($date, $id);
            // if ( $userTotal == true) return redirect()->route('myWorkers');
        }
    }
}
