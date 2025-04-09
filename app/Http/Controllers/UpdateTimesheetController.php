<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;
use App\Models\Daytotal;
use Illuminate\Support\Carbon;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;

class UpdateTimesheetController extends Controller
{
   
    public function updateForm($id, $timesheet, $type = null)
    {

        $worker = User::find($id);
        $timesheet = $type == 'timesheet' ? Timesheet::find($timesheet) : Daytotal::find($timesheet);
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
        $companyDayHours = User::find($id)->company->day_hours;
        $timesheet = $request->type == 'workday' ? Timesheet::find($request->timesheet) : Daytotal::find($request->timesheet);
     
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
                'accountableHours' => $companyDayHours,
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
           
            if ($addTimesheet) return redirect()->back()->with('success', 'Dag is aangepast');
        
            // $timesheet->save();
            // $userTotal = CalculateUtility::calculateUserTotal($date, $id);
            // if ( $userTotal == true) return redirect()->route('myWorkers');
        }
    }
}
