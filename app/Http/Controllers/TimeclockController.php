<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\JsonController;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{

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
    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $userRow = auth()->user()->timelogs;
        $timestamp = now('Europe/Brussels');
        // $day = date('d', strtotime($timestamp));
        // $month = date('m', strtotime($timestamp));
        // $year = date('Y', strtotime($timestamp));

        //TODO: rewrite start logic when a user has already logged this day
        $dayCheck = Timesheet::where('UserId', $currentUser->id)
        ->where('Month', '=', $timestamp->format('Y-m-d'))
        ->exists();
        if(!$dayCheck) {
            $userRow->fill([
                'BreakHours' => 0,
                'BreaksTaken' => 0,
                'RegularHours' => 0,
            ]);
        }

        //TODO: use in_array()to check if current day is present in defined weekenddays array from the weektypes table
        $weekDay = Carbon::parse($timestamp)->weekday();
        $weekDay == 0 || $weekDay == 6 ? $userRow->Weekend = true : $userRow->Weekend = false;
        //TODO: check if userRow exists
        $userRow->fill([
            'StartWork' => $timestamp,
            'StartBreak' => null,
            'EndBreak' => null,
            'StopWork' => null,
            'userNote' => null,
            'ShiftStatus' => true
        ]);
        $userRow->save();

        // $dayCheck? $dayCheck->delete(): null;
        return redirect('/dashboard');
    }


    public function break()
    {
        // $timeController = new TimesheetController();
        // $jsonsMission = new JsonController;
        $timeStamp = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $userRow->RegularHours += $this->calculateDecimal($userRow->EndBreak ? $userRow->EndBreak : $userRow->StartWork, $timeStamp);
        $userRow->BreakStatus = true;
        if ($userRow->BreaksTaken >= 1) {
            $startBreakDate = date('Y-m-d', strtotime($userRow->StartBreak));
            $today = date('Y-m-d');
            if ($startBreakDate == $today) {
                //TODO: increment the number of brakes in timelog today
                $userRow->BreakHours += $this->calculateDecimal($userRow->StartBreak, $userRow->EndBreak);
            }
        }
        //     $json = $jsonsMission->callJson($userRow);
        //     $json[]=[
        //         'ClockedIn' => null,
        //         'ClockedOut' => null,
        //         'BreakIn'=>$userRow->StartBreak,
        //         'BreakOut'=>$userRow->EndBreak
        //     ];
        //     $userRow->AdditionalTimestamps = json_encode($json);
        // }
        $userRow->fill([
            'StartBreak' => $timeStamp,
            'BreaksTaken' => $userRow->BreaksTaken + 1
        ]);
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stopBreak()
    {
        // $timesheetController = new TimesheetController();
        $timeStamp = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $userRow->fill([
            'BreakStatus' => false,
            'EndBreak' => $timeStamp,
            'BreakHours' => $userRow->BreakHours + $this->calculateDecimal($userRow->StartBreak, $timeStamp)
        ]);
        // $userRow->StartWork = $timeStamp;!!!
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        // $timesheetController = new TimesheetController;
        $timeStamp = now('Europe/Brussels');
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $start = Carbon::parse($userRow->StartBreak, 'Europe/Brussels');
            $end = Carbon::parse($timeStamp, 'Europe/Brussels');
            $userRow->fill([
                'BreakStatus' => false,
                'EndBreak' => $timeStamp,
                'BreakHours' => $userRow->BreakHours + $this->calculateDecimal($start, $end)
            ]);
        }
        // if($userRow->EndBreak){
        //     $userRow->StartWork = $userRow->EndBreak;
        //     $userRow->save();
        // }

        $userRow->fill([
            'StopWork' => $timeStamp,
            'RegularHours' => $userRow->RegularHours + $this->calculateDecimal(
                $userRow->EndBreak ?? $userRow->StartWork, 
                $timeStamp
            )
        ]);
        
        $userRow->save();
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
