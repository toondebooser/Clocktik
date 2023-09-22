<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    // public function newUserTotal()
    // {
    //     $newUserTotal = new Usertotal;
    //     $now = now('Europe/Brussels');
    //     $newUserTotal->UserId = auth()->user()->id;
    //     $newUserTotal->Month = $now;
    //     $newUserTotal->RegularHours = 0;
    //     $newUserTotal->BreakHours = 0;
    //     $newUserTotal->OverTime = 0;
    //     $newUserTotal->save();
    // }
    public function fetchUserTotal()
    {
        $newUserTotal = new Usertotal;
        $now = now('Europe/Brussels');
        $userTotal = $newUserTotal
            ->where('UserID', '=', auth()->user()->id)
            ->whereMonth('Month', '=', $now)
            ->whereYear('Month', '=', $now)
            ->first();
        if($userTotal == null){
            $newUserTotal->UserId = auth()->user()->id;
            $newUserTotal->Month = $now;
            $newUserTotal->RegularHours = 0;
            $newUserTotal->BreakHours = 0;
            $newUserTotal->OverTime = 0;
            $newUserTotal->save();
            $userTotal = $newUserTotal
            ->where('UserID', '=', auth()->user()->id)
            ->whereMonth('Month', '=', $now)
            ->whereYear('Month', '=', $now)
            ->first();
        }
        return $userTotal;
    }
    public function makeTimesheet()
    {
        $userRow = Timelog::find(auth()->user()->id);
        $newTimeSheet = new Timesheet;
        // $userTotal = $this->fetchUserTotal();
        // if ($userTotal == null) {
        //     $this->newUserTotal();
        //     $userTotal = $this->fetchUserTotal();
        // }

        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $breakHours = $this->calculateBreakHours($userRow->StartBreak, $userRow->EndBreak);
        $clockedTime = $this->calculateClockedHours($userRow->StartWork, $userRow->StopWork);

        $regularHours = $clockedTime - $breakHours;
        $newTimeSheet->BreakHours = $breakHours;

        $result = $this->calculateHourBalance($regularHours, $userRow, $newTimeSheet, 'new');
        // switch (true) {
        //     case ($regularHours > 7.60 && $userRow->Weekend == false):
        //         $difference = $regularHours - 7.60;
        //         $newTimeSheet->OverTime = $difference;
        //         $newTimeSheet->RegularHours = $regularHours - $difference;
        //         $newTimeSheet->accountableHours = 7.60;
        //         $newTimeSheet->Weekend = false;
        //         // $userTotal->OverTime += $difference;
        //         // $userTotal->RegularHours += ($regularHours - $difference);
        //         // $userTotal->BreakHours += $breakHours;
        //         break;

        //     case ($regularHours < 7.60 && $userRow->Weekend == false):
        //         $missingHours = 7.60 - $regularHours;
        //         $newTimeSheet->RegularHours = $regularHours;
        //         $newTimeSheet->accountableHours = 7.60;
        //         $newTimeSheet->OverTime = -$missingHours;
        //         $newTimeSheet->Weekend = false;

        //         // $userTotal->OverTime -= $missingHours;
        //         // $userTotal->RegularHours += 7.6;
        //         // $userTotal->BreakHours += $breakHours;
        //         break;

        //     case ($userRow->Weekend == true):
        //         $newTimeSheet->Weekend = true;
        //         $newTimeSheet->RegularHours = $regularHours;
        //         // $userTotal->BreakHours += $breakHours;
        //         // $userTotal->OverTime += $regularHours;

        //         break;
        //     default:
        //         $newTimeSheet->RegularHours = 7.60;
        //         $newTimeSheet->accountableHours = 7.60;
        //         $newTimeSheet->OverTime = 0;
        //         // $userTotal->RegularHours += 7.60;
        //         // $userTotal->BreakHours += $breakHours;
        //         break;
        // }

        // $newTimeSheet->Month = $now;
        // $newTimeSheet->save();
        // $userTotal->save();
        $total = $this->calculateUserTotal();
       if($result == true && $total == true) return redirect('/dashboard');
       
    //    return redirect('/dashboard');
    }

    public function updateTimesheet()
    {
        
    }
    public function calculateBreakHours($start, $end)
    {
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

    public function calculateClockedHours($start, $end)
    {

        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;


        $diffInMin = $end->diffInMinutes($start);

        $decimalTime = round($diffInMin / 60, 2);

        return $decimalTime;
    }
    
    public function calculateHourBalance ($regularHours, $userRow, $timesheet,$type)
    {
        $now = now('Europe/Brussels');

        switch (true) {
            case ($regularHours > 7.60 && $userRow->Weekend == false):
                $difference = $regularHours - 7.60;
                $timesheet->OverTime = $difference;
                $timesheet->RegularHours = $regularHours - $difference;
                $timesheet->accountableHours = 7.60;
                $timesheet->Weekend = false;
                // $userTotal->OverTime += $difference;
                // $userTotal->RegularHours += ($regularHours - $difference);
                // $userTotal->BreakHours += $breakHours;
                break;

            case ($regularHours < 7.60 && $userRow->Weekend == false):
                $missingHours = 7.60 - $regularHours;
                $timesheet->RegularHours = $regularHours;
                $timesheet->accountableHours = 7.60;
                $timesheet->OverTime = -$missingHours;
                $timesheet->Weekend = false;

                // $userTotal->OverTime -= $missingHours;
                // $userTotal->RegularHours += 7.6;
                // $userTotal->BreakHours += $breakHours;
                break;

            case ($userRow->Weekend == true):
                $timesheet->Weekend = true;
                $timesheet->RegularHours = $regularHours;
                // $userTotal->BreakHours += $breakHours;
                // $userTotal->OverTime += $regularHours;

                break;
            default:
                $timesheet->RegularHours = 7.60;
                $timesheet->accountableHours = 7.60;
                $timesheet->OverTime = 0;
                // $userTotal->RegularHours += 7.60;
                // $userTotal->BreakHours += $breakHours;
                break;
        }
        if($type == 'new')$timesheet->Month = $now;
        $timesheet->save();
        return true;

    }
    public function calculateUserTotal()
    {
        $userTotal = $this->fetchUserTotal();
        $userId = auth()->user()->id;
        if($userTotal != null){
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->sum('OverTime');
        }
        $userTotal->save();
        return true;
    }

}
  