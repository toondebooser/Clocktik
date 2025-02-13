<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\JsonController;
use App\Models\Timesheet;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{

    
    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $userRow = auth()->user()->timelogs;
        $timestamp = now('Europe/Brussels');
     

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

        return redirect('/dashboard');
    }


    public function break()
    {
   
        $timeStamp = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $userRow->RegularHours += CalculateUtility::calculateDecimal($userRow->EndBreak ? $userRow->EndBreak : $userRow->StartWork, $timeStamp);
        $userRow->BreakStatus = true;
        if ($userRow->BreaksTaken >= 1) {
            $startBreakDate = date('Y-m-d', strtotime($userRow->StartBreak));
            $today = date('Y-m-d');
            if ($startBreakDate == $today) {
                //TODO: increment the number of brakes in timelog today
                $userRow->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $userRow->EndBreak);
            }
        }
  
        $userRow->fill([
            'StartBreak' => $timeStamp,
            'BreaksTaken' => $userRow->BreaksTaken + 1
        ]);
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stopBreak()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $userRow->fill([
            'BreakStatus' => false,
            'EndBreak' => $timeStamp,
            'BreakHours' => $userRow->BreakHours + CalculateUtility::calculateDecimal($userRow->StartBreak, $timeStamp)
        ]);
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $timeStamp = now('Europe/Brussels');
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $start = Carbon::parse($userRow->StartBreak, 'Europe/Brussels');
            $end = Carbon::parse($timeStamp, 'Europe/Brussels');
            $userRow->fill([
                'BreakStatus' => false,
                'EndBreak' => $timeStamp,
                'BreakHours' => $userRow->BreakHours + CalculateUtility::calculateDecimal($start, $end)
            ]);
        }
      

        $userRow->fill([
            'StopWork' => $timeStamp,
            'RegularHours' => $userRow->RegularHours + CalculateUtility::calculateDecimal(
                $userRow->EndBreak ?? $userRow->StartWork, 
                $timeStamp
            )
        ]);
        
        $userRow->save();
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
