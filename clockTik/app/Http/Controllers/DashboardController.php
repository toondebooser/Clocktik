<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
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
        $newUserTotal = new Usertotal;
        $userTotal = $newUserTotal->where('UserID', auth()->user()->id)->whereMonth('Month', '=', $timeStamp)->whereYear('Month', '=', $timeStamp)->first();



        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $breakHours = $this->calculateBreakHours($userRow);
        $clockedTime = $this->calculateClockedHours($userRow);
        $regularHours = $clockedTime - $breakHours;
        $newTimeSheet->BreakHours = $breakHours;


        switch (true) {
            case ($regularHours > 7.6):
                $difference = $regularHours - 7.6;
                $newTimeSheet->OverTime = $difference;
                $newTimeSheet->RegularHours = $regularHours - $difference;
        
                if ($userTotal == null) {

                    $newUserTotal->UserId = auth()->user()->id;
                    $newUserTotal->Month = $timeStamp;
                    $newUserTotal->RegularHours = 0;
                    $newUserTotal->BreakHours = 0;
                    $newUserTotal->OverTime = 0;
                    $newUserTotal->save();

                    $newUserTotal->OverTime += $difference;
                    $newUserTotal->RegularHours += ($regularHours - $difference);
                    $newUserTotal->BreakHours += $breakHours;
                    $newUserTotal->save();
        
                } else {
                    $userTotal->OverTime += $difference;
                    $userTotal->RegularHours += ($regularHours - $difference);
                    $userTotal->BreakHours += $breakHours;
                    $userTotal->save();
                }
                break;
        
            case ($regularHours < 7.6):

                $missingHours = 7.6 - $regularHours;
                $newTimeSheet->RegularHours = 7.6;
                $newTimeSheet->OverTime = 0;
        
                if ($userTotal == null) {
                    $newUserTotal->UserId = auth()->user()->id;
                    $newUserTotal->Month = $timeStamp;
                    $newUserTotal->RegularHours = 0;
                    $newUserTotal->BreakHours = 0;
                    $newUserTotal->OverTime = 0;
                    $newUserTotal->save();
                    $newUserTotal->OverTime -= $missingHours;
                    $newUserTotal->RegularHours += 7.6;
                    $newUserTotal->BreakHours += $breakHours;
                    $newUserTotal->save();
                } else {
                    $userTotal->OverTime -= $missingHours;
                    $userTotal->save();
                }
                break;
        
            default:
                $newTimeSheet->RegularHours = 7.6;
                $newTimeSheet->OverTime = 0;
                break;
        
                
                
                
            }
            
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
    public function calculateClockedHours($userRow)
    {
        $start = $userRow->StartWork;
        $end = $userRow->StopWork;
        $startParse = Carbon::createFromTimestamp($start)->setTimezone('Europe/Brussels');
        $endParse = Carbon::createFromTimestamp($end)->setTimezone('Europe/Brussels');


        $diffInMin = $endParse->diffInMinutes($startParse) + 70;
        $decimalTime = round($diffInMin / 60, 2);

        return $decimalTime;
    }
    public function calculateOverTime($userRow)
    {
    }

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
