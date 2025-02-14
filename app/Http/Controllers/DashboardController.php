<?php

namespace App\Http\Controllers;


use App\Models\Timelog;
use App\Models\Timesheet;
use App\Utilities\UserUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        $userRow = Timelog::where('UserId',auth()->user()->id)->first();
        $today = now('Europe/Brussels')->format('Y-m-d');
        // $displayWorkedHours = 0.00;
        // $displayBreakHours = 0.00;
        $getLastWorkedDay = UserUtility::userTimesheetCheck($userRow->StartWork,auth()->user()->id)->first();
        if($getLastWorkedDay && $getLastWorkedDay->Month == $today && !$userRow->ShiftStatus){
            $userRow->fill([
                'BreakHours' => $getLastWorkedDay->BreakHours,
                'RegularHours' => $getLastWorkedDay->RegularHours
            ]);
            $userRow->save();
        }
   

        $userNoteInput = $request->input('userNote');
        if($userNoteInput !== null || $userNoteInput === '')
        {
            $userNoteInput === ''? $userRow->userNote = null : null;
            $userRow->userNote = $userNoteInput;
            $userRow->save();
        }
        $userNote = $userRow->userNote;
        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;
        $start = $userRow->EndBreak? $userRow->EndBreak : $userRow->StartWork;
        $startBreak = $userRow->StartBreak;
        $breakHours =   $getLastWorkedDay->BreakHours ?? $userRow->BreakHours;
        $workedHours =  $getLastWorkedDay->RegularHours ?? $userRow->RegularHours;
        $lastWorkedDate = Carbon::parse($userRow->StartWork, "Europe/Brussels");

        return view('dashboard', ['user' => auth()->user(),'lastWorkedDate' => $lastWorkedDate, 'workedHours' => $workedHours ,'breakHours' => $breakHours ,'startBreak'=> $startBreak, 'start' => $start , 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus, 'userNote' => $userNote]);
    }

    
  
}
