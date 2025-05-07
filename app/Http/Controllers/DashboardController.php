<?php

namespace App\Http\Controllers;


use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard(Request $request)
    {
        $userRow = Timelog::where('UserId', auth()->user()->id)->first();
        $user = User::find(auth()->user()->id);
        $dayTotal = $user->dayTotals()->whereDate('Month',  $userRow->StartWork)->first();


        $userNoteInput = $request->input('userNote');
        if ($userNoteInput !== null || $userNoteInput === '') {
            $userNoteInput === '' ? $userRow->userNote = null : null;
            $userRow->userNote = $userNoteInput;
            $userRow->save();
        }
        $userNote = $userRow->userNote;
        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;
        $start = $userRow->EndBreak ? $userRow->EndBreak : $userRow->StartWork;
        $startBreak = $userRow->StartBreak;
        $breakHours =   $dayTotal->BreakHours ?? 0;
        $workedHours =   $dayTotal->RegularHours ?? 0;
        // $lastWorkedDate = Carbon::parse($userRow->StartWork, "Europe/Brussels");

        return view('dashboard', ['user' => auth()->user(), 'workedHours' => $workedHours, 'breakHours' => $breakHours, 'startBreak' => $startBreak, 'start' => $start, 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus, 'userNote' => $userNote]);
    }
}
