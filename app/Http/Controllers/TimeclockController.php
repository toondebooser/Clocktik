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


    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $timesheetController = new TimesheetController;
        $userTimesheet = new Timesheet;
        $jsonsMission = new JsonController;
        $userRow = Timelog::where('UserId', auth()->user()->id)->first();
        
        $timestamp = now('Europe/Brussels');
        $day = date('d', strtotime($timestamp));
        $month = date('m', strtotime($timestamp));
        $year = date('Y', strtotime($timestamp));
        $userRow->userNote = null;
        $userRow->BreakHours = 0;
        $userRow->RegularHours = 0;
        
        
        $dayCheck = $userTimesheet
        ->where('UserId', '=', $currentUser->id)
        ->whereDay('Month', '=', $day)
        ->whereMonth('Month', '=', $month)
        ->whereYear('Month', '=', $year)
        ->first();
        $json = $jsonsMission->callJson($dayCheck);
        $json ? $userRow->AdditionalTimestamps = json_encode($json) : $userRow->AdditionalTimestamps = null;
        
        if ($dayCheck !== null && $dayCheck->type == "workday") {
            
            $json[] = [
                'ClockedIn' => $dayCheck->ClockedIn,
                'ClockedOut' => $dayCheck->ClockedOut,
                'BreakIn' => $dayCheck->BreakStart,
                'BreakOut' => $dayCheck->BreakStop
            ];
            
            $userRow->AdditionalTimestamps = json_encode($json);
            $userRow->BreakHours += $dayCheck->BreakHours;
            $userRow->RegularHours += $dayCheck->RegularHours;
            $dayCheck->userNote !== null ? $userRow->userNote = $dayCheck->userNote : null;       
            $timesheetController->calculateUserTotal($timestamp, $currentUser->id);
        } elseif ($dayCheck !== null && $dayCheck !== "workday") {
            return redirect()->route('dashboard')->with('error', "Vandaag is " . $dayCheck->type . " geregistreerd");
        }
        
        $weekDay = Carbon::parse($timestamp)->weekday();
        $weekDay == 0 || $weekDay == 6 ? $userRow->Weekend = true : $userRow->Weekend = false;
        $userRow->StartWork = $timestamp;
        $userRow->StartBreak = null;
        $userRow->EndBreak = null;
        $userRow->StopWork = null;
        $userRow->ShiftStatus = true;
        $userRow->save();
        
        $dayCheck? $dayCheck->delete(): null;
        return redirect('/dashboard');
    }
    
    
    public function break()
    {
        $timeController = new TimesheetController();
        $jsonsMission = new JsonController;
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::where('UserId', auth()->user()->id)->first();
        $userRow->RegularHours += $timeController->calculateDecimal($userRow->EndBreak? $userRow->EndBreak: $userRow->StartWork, $timeStamp);
        
        $userRow->BreakStatus = true;
        if ($userRow->StartBreak){
            $json = $jsonsMission->callJson($userRow);
            $json[]=[
                'ClockedIn' => null,
                'ClockedOut' => null,
                'BreakIn'=>$userRow->StartBreak,
                'BreakOut'=>$userRow->EndBreak
            ];
            $userRow->AdditionalTimestamps = json_encode($json);
        }
        $userRow->StartBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stopBreak()
    {
        $timeController = new TimesheetController();
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::where('UserId', auth()->user()->id)->first();
        $userRow->BreakStatus = false;
        $start = $userRow->StartBreak;
        $userRow->EndBreak = $timeStamp;
        $userRow->BreakHours += $timeController->calculateDecimal($start, $timeStamp);
        // $userRow->StartWork = $timeStamp;!!!
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $userRow = Timelog::where('UserId', auth()->user()->id)->first();
        $timesheetController = new TimesheetController;
        $timeStamp = now('Europe/Brussels');
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $userRow->BreakStatus = false;
            $start = Carbon::parse($userRow->StartBreak, 'Europe/Brussels');
            $end = Carbon::parse($timeStamp, 'Europe/Brussels');
            $userRow->EndBreak = $timeStamp;
            $userRow->BreakHours += $timesheetController->calculateDecimal($start, $end);
            $userRow->save();
        }
        // if($userRow->EndBreak){
        //     $userRow->StartWork = $userRow->EndBreak;
        //     $userRow->save();
        // }

        $userRow->StopWork = $timeStamp;
        $userRow->RegularHours += $timesheetController->calculateDecimal($userRow->EndBreak? $userRow->EndBreak: $userRow->StartWork, $timeStamp);
        $userRow->save();
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
