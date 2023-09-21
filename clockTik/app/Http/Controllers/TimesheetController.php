<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function newUserTotal()
    {
        $newUserTotal = new Usertotal;
        $now = now('Europe/Brussels');
        $newUserTotal->UserId = auth()->user()->id;
        $newUserTotal->Month = $now;
        $newUserTotal->RegularHours = 0;
        $newUserTotal->BreakHours = 0;
        $newUserTotal->OverTime = 0;
        $newUserTotal->save();
    }
    public function fetchUserTotal()
    {        
        $newUserTotal = new Usertotal;
        $now = now('Europe/Brussels');
        $userTotal = $newUserTotal
        ->where('UserID', '=', auth()->user()->id)
        ->whereMonth('Month', '=', $now)
            ->whereYear('Month', '=', $now)
            ->first();
            return $userTotal;
        }
        public function makeTimesheet()
        {
            $userRow = Timelog::find(auth()->user()->id);
            $now = now('Europe/Brussels');
            $newTimeSheet = new Timesheet;
            $userTotal = $this->fetchUserTotal();
            if ($userTotal == null)  
            {
            $this->newUserTotal();
            $userTotal = $this->fetchUserTotal();
            
        }
        
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
            case ($regularHours > 7.60 && $userRow->Weekend == false):
                $difference = $regularHours - 7.60;
                $newTimeSheet->OverTime = $difference;
                $newTimeSheet->RegularHours = $regularHours - $difference;
                $newTimeSheet->Weekend = false;
                $userTotal->OverTime += $difference;
                $userTotal->RegularHours += ($regularHours - $difference);
                $userTotal->BreakHours += $breakHours;
                break;

                case ($regularHours < 7.60 && $userRow->Weekend == false):
                $missingHours = 7.60 - $regularHours;
                $newTimeSheet->RegularHours = $regularHours;
                $newTimeSheet->OverTime = -$missingHours;
                $newTimeSheet->Weekend = false;

                $userTotal->OverTime -= $missingHours;
                $userTotal->RegularHours += 7.6;
                $userTotal->BreakHours += $breakHours;
                break;

                case ($userRow->Weekend == true):
                $newTimeSheet->Weekend = true;
                $newTimeSheet->RegularHours = $regularHours;
                $userTotal->BreakHours += $breakHours;
                $userTotal->OverTime += $regularHours;

                break;
            default:
                $newTimeSheet->RegularHours = 7.60;
                $newTimeSheet->OverTime = 0;
                $userTotal->RegularHours += 7.60;
                $userTotal->BreakHours += $breakHours;
                break;
            }
            
        $newTimeSheet->Month = $now;
        $newTimeSheet->save();
        $userTotal->save();
        return redirect('/dashboard');
    }
    public function calculateBreakHours($userRow)
    {
        $start = $userRow->StartBreak;
        $end = $userRow->EndBreak;
        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;
        if ($start === null) {
            $start = now('Europe/Brussels');
            $end = now('Europe/Brussels');
        }


        $diffInMin = $end->diffInMinutes($start);
        $decimalTime = round($diffInMin / 60, 2);
        return $decimalTime;
    }

    public function calculateClockedHours($userRow)
    {
        $start = $userRow->StartWork;
        $end = $userRow->StopWork;

        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;


        $diffInMin = $end->diffInMinutes($start);

        $decimalTime = round($diffInMin / 60, 2);

        return $decimalTime;
    }

}
