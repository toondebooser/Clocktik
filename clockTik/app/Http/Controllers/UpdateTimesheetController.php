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
        // if ($timesheet == null || $timesheet->type !== 'workday') {
        //     $postData = [
        //         'worker' => $id,
        //     ];

        //     return redirect()->route('getData', $postData)->with('error', 'Dit is geen werkdag.');
        // }
        return view('updateTimesheet', ['worker' => $worker, 'timesheet' => $timesheet]);
    }

    public function updateTimesheet(Request $request)
    {

        $timesheetController = new TimesheetController();
        $timesheet = Timesheet::find($request->timesheet);
        $type = $request->updateSpecial;
        $type == null? $type = $timesheet->type: null;
        $date = $timesheet->Month;
        $id = $request->id;
        if ($type == "Onbetaald verlof") {
            $timesheet->accountableHours = 0;
            $timesheet->type = $type;
            $save = $timesheet->save();
            $fetchTotal = $timesheetController->calculateUserTotal($date, $id);
            if ($save == true && $fetchTotal == true) 
            {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData);
            }
            else 
            {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }
        }elseif($type !== 'workday')
        {
            dd($type);
            $timesheet->accountableHours = 7.6;
            $timesheet->type = $type;
            $save = $timesheet->save();
            $fetchTotal = $timesheetController->calculateUserTotal($date, $id);
            if ($save == true && $fetchTotal == true) 
            {
                {
                    $postData = [
                        'worker' => $id,
                    ];
    
                    return redirect()->route('getData', $postData);
                }
            }
            else 
            {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na of de dag in het uurrooster is aangepast.');
            }

        }
        else
        {
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
