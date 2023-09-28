<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
   
    public function fetchUserTotal()
    {
        $newUserTotal = new Usertotal;
        $now = now('Europe/Brussels');
        $userTotal = $newUserTotal
            ->where('UserID', '=', auth()->user()->id)
            ->whereMonth('Month', '=', $now)
            ->whereYear('Month', '=', $now)
            ->first();
        if ($userTotal == null) {
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
        $weekend = $userRow->weekend;
   

        $newTimeSheet->UserId = auth()->user()->id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $breakHours = $this->calculateBreakHours($userRow->StartBreak, $userRow->EndBreak);
        $clockedTime = $this->calculateClockedHours($userRow->StartWork, $userRow->StopWork);

        $regularHours = $clockedTime - $breakHours;
        $newTimeSheet->BreakHours = $breakHours;

        $result = $this->calculateHourBalance($regularHours, $userRow, $weekend,  $newTimeSheet, 'new');
       
        $total = $this->calculateUserTotal();
        if ($result == true && $total == true) return redirect('/dashboard');

    }

    public function updateTimesheet(Request $request)
    {

    }

    public function setSpecial(Request $request)
    {
        dd($request);
        return redirect('/dashboard');
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

    public function calculateHourBalance($regularHours, $userRow, $weekend, $timesheet, $type)
    {
        $now = now('Europe/Brussels');

        switch (true) {
            case ($regularHours > 7.60 && $weekend == false):
                $difference = $regularHours - 7.60;
                $timesheet->OverTime = $difference;
                $timesheet->RegularHours = $regularHours - $difference;
                $timesheet->accountableHours = 7.60;
                $timesheet->Weekend = false;
               
                break;

            case ($regularHours < 7.60 && $weekend == false):
                $missingHours = 7.60 - $regularHours;
                $timesheet->RegularHours = $regularHours;
                $timesheet->accountableHours = 7.60;
                $timesheet->OverTime = -$missingHours;
                $timesheet->Weekend = false;
                break;

            case ($weekend == true):
                $timesheet->Weekend = true;
                $timesheet->RegularHours = $regularHours;
                $timesheet->OverTime += $regularHours;

                break;
            default:
                $timesheet->RegularHours = 7.60;
                $timesheet->accountableHours = 7.60;
                $timesheet->OverTime = 0;
                break;
        }

        if ($type == 'new') $timesheet->Month = $userRow->StartWork;

        $timesheet->save();
        return true;
    }


    public function calculateUserTotal()
    {
        $userTotal = $this->fetchUserTotal();
        $userId = auth()->user()->id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->sum('OverTime');
        }
        $userTotal->save();
        return true;
    }
}
