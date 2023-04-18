<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\TimeSheet;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $userRow = Timelog::find(auth()->user()->id);
        $shiftStatus = $userRow->ShiftStatus;
        $breakStatus = $userRow->BreakStatus;
        return view('dashboard', ['user' => auth()->user(), 'shiftStatus' => $shiftStatus, 'breakStatus' => $breakStatus]);
    }

    public function startWorking(Request $request)
    {
        $timestamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->StartBreak = null;
        $userRow->EndBreak = null;
        $userRow->StopWork = null;
        $userRow->StartWork = $timestamp;
        $userRow->ShiftStatus = true;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function break()
    {
        $timeStamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = true;
        $userRow->StartBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function stopBreak()
    {
        $timeStamp = now();
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = false;

        $userRow->EndBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $timeStamp = now();
        $breakStatus = false;
        $userRow = Timelog::find(auth()->user()->id);
        if ($userRow->BreakStatus == true) {
            $breakStatus = true;
            $userRow->BreakStatus = false;
        }
        $userRow->ShiftStatus = false;
        $userRow->StopWork = $timeStamp;
        $userRow->save();

        $this->makeTimeSheet($userRow, $breakStatus, $timeStamp);

        return redirect('/dashboard');
    }

    public function makeTimeSheet($userRow, $breakStatus, $timeStamp)
    {
        $newTimeSheet = new TimeSheet;
        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;
        if ($breakStatus == true) {
            $newTimeSheet->BreakStart = $userRow->StartBreak;
            $newTimeSheet->BreakStop = $userRow->EndBreak;
            $newTimeSheet->BreakHours = $this->calculateBreakHours($newTimeSheet, $userRow);
        }
        $newTimeSheet->RegularHours = $this->calculateRegularHours($newTimeSheet);
        $newTimeSheet->OverTime = $this->calculateOverTime($newTimeSheet);
        $newTimeSheet->Month = $timeStamp->format('F Y');
        $newTimeSheet->save();
    }

    public function calculateBreakHours($newTimeSheet, $userRow)
    {
        $start = $userRow->StartBreak;
        $end = $userRow->EndBreak;
    }
    public function calculateRegularHours($newTimeSheet)
    {
    }
    public function calculateOverTime($newTimeSheet)
    {
    }
}
