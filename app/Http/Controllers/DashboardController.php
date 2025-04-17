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
        $dayTotal = auth()->user()->dayTotals->where('Month',  Carbon::parse(now('Europe/Brussels'))->format('Y-m-d'))->first();
     
   

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
        $breakHours =   $dayTotal->BreakHours ?? 0;
        $workedHours =   $dayTotal->RegularHours ?? 0;
        // $lastWorkedDate = Carbon::parse($userRow->StartWork, "Europe/Brussels");

        return view('dashboard', ['user' => auth()->user(), 'workedHours' => $workedHours ,'breakHours' => $breakHours ,'startBreak'=> $startBreak, 'start' => $start , 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus, 'userNote' => $userNote]);
    }

    
  
}
