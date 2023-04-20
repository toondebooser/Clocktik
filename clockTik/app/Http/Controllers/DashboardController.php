<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use Carbon\Carbon;
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
        $timestamp = now('Europe/Brussels');
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
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = true;
        $userRow->StartBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function stopBreak()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = false;

        $userRow->EndBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $userRow->BreakStatus = false;
            $userRow->EndBreak = $timeStamp;
            $userRow->save();
        }
        $userRow->StopWork = $timeStamp;
        $userRow->save();
        $this->makeTimeSheet($userRow, $timeStamp);
        return redirect('/dashboard');
    }

    public function makeTimeSheet($userRow, $timeStamp)
    {
        $newTimeSheet = new Timesheet;
        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $newTimeSheet->BreakHours = $this->calculateBreakHours($userRow);


        $newTimeSheet->RegularHours = $this->calculateRegularHours($newTimeSheet);
        $newTimeSheet->OverTime = 0;
        $newTimeSheet->Month = $timeStamp;
        $newTimeSheet->save();
    }

    public function calculateBreakHours($userRow)
    {
        $start = $userRow->StartBreak;
        $end = $userRow->EndBreak;
        $startParse = Carbon::createFromTimestamp($start)->setTimezone('Europe/Brussels');
        $endParse = Carbon::createFromTimestamp($end)->setTimezone('Europe/Brussels');

        $diffInMin = $endParse->diffInMinutes($startParse);
        $decimalTime = round($diffInMin / 60, 2);
        return $decimalTime;
    }
    public function calculateRegularHours($newTimeSheet)
    {
        $start = $newTimeSheet->ClockedIn;
        $end = $newTimeSheet->ClockedOut;
        $startParse = Carbon::createFromTimestamp($start)->setTimezone('Europe/Brussels');
        $endParse = Carbon::createFromTimestamp($end)->setTimezone('Europe/Brussels');


        $diffInMin = $endParse->diffInMinutes($startParse) ;
        $decimalTime = round($diffInMin / 60, 2);
        return $decimalTime;
    }
    // public function calculateOverTime($newTimeSheet)
    // {

    // }

    public function myProfile()
    {
        $userProfile = new Timesheet;
        $currentUser = auth()->user();
        $now = now('Europe/Brussels');

        //temporary month data.
        $monthString = date('F', strtotime($now));
        $month = date('m', strtotime($now));
        $monthData = $userProfile
            ->where('userId', '=', $currentUser->id)
            ->whereMonth('Month', '=', $month)
            ->get();


        return view('profile', ['timesheet' => $monthData, 'month' => $monthString]);
    }
}
