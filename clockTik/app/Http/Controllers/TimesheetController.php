<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Hamcrest\Type\IsString;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{

    public function fetchUserTotal($date, $id)
    {
        $newUserTotal = new Usertotal;
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $userId = $id;
        $userTotal = $newUserTotal
            ->where('UserID', '=', $userId)
            ->whereMonth('Month', '=', $date)
            ->whereYear('Month', '=', $date)
            ->first();
        if ($userTotal == null) {
            $newUserTotal->UserId = $userId;
            $newUserTotal->Month = $date;
            $newUserTotal->RegularHours = 0;
            $newUserTotal->BreakHours = 0;
            $newUserTotal->OverTime = 0;
            $newUserTotal->save();
            $userTotal = $newUserTotal
                ->where('UserID', '=', $userId)
                ->whereMonth('Month', '=', $date)
                ->whereYear('Month', '=', $date)
                ->first();
        }
        return $userTotal;
    }

    public function timesheetCheck($date, $id)
    {
        $timesheetCheck = Timesheet::where('UserId', $id)
            ->where('Month', $date)
            ->first();
        return $timesheetCheck;

    }

    public function makeTimesheet($id)
    {
        $userRow = Timelog::find($id);
        $newTimeSheet = new Timesheet;

        $timesheetCheck = $this->timesheetCheck(now('Europe/Brussels'), $id);
        if($timesheetCheck == null) return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, Controleer je profiel of belt de wim');
        $newTimeSheet->UserId = $id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $breakHours = $this->calculateBreakHours($userRow->StartBreak, $userRow->EndBreak);
        $clockedTime = $this->calculateClockedHours($userRow->StartWork, $userRow->StopWork);

        $regularHours = $clockedTime - $breakHours;
        $newTimeSheet->BreakHours = $breakHours;

        $result = $this->calculateHourBalance($regularHours, $userRow, $userRow->weekend,  $newTimeSheet, 'new');

        $total = $this->calculateUserTotal(now('Europe/Brussels'), $id);
        if ($result == true && $total == true) return redirect('/dashboard');
    }

    public function updateTimesheet(Request $request)
    {
    }
    public function setDay($newSpecialTimesheet, $dayType, $worker, $singleDay)
    {
        $timesheetCheck = $this->timesheetCheck($singleDay, $worker);
        if (!$timesheetCheck) {
            $newSpecialTimesheet->type = $dayType;
            $newSpecialTimesheet->ClockedIn = $singleDay;
            $newSpecialTimesheet->Month = $singleDay;
            $newSpecialTimesheet->UserId = $worker;
            if ($dayType == 'Onbetaald verlof') {
                $newSpecialTimesheet->save();
                return true;
            }
            $newSpecialTimesheet->accountableHours = 7.6;
            $newSpecialTimesheet->save();
            $userTotal = $this->fetchUserTotal($singleDay, $worker);
            $userTotal->$dayType += 1;
            $userTotal->save();
            $this->calculateUserTotal($singleDay, $worker);
            return true;
        } else {
            return false;
        }
    }

    public function setPeriod($dayType, $worker, $startDate, $endDate)
    {
        $errors = [];
        while ($startDate <= $endDate) {
            $newSpecialTimesheet = new Timesheet;
              if (!$startDate->isWeekend()) {
                  $addDay =  $this->setDay($newSpecialTimesheet, $dayType, $worker, $startDate);
                  if (!$addDay) {
                      array_push($errors, $startDate->toDateString());
                  }
              }
              $startDate->addDay();
          }

        if (!empty($errors)) {
            
            return redirect()->route('specials', ['worker' => $worker])->with('errors', $errors);
        }else 
        {
            return true;
        }
    }

    public function setSpecial(Request $request)
    {
        $dayType = $request->input('specialDay');
        $worker = $request->input('worker');
        $singleDay = Carbon::parse($request->input('singleDay'));
        $startDate = Carbon::parse($request->input('startDate'));
        $endDate = Carbon::parse($request->input('endDate'));
        $submitType = $request->input('submitType');
        $workerArray = json_decode($worker, true);
        if (is_array($workerArray) && count($workerArray) > 1) {
            //if setSpecial is for multiple worker

        } else {
            if ($submitType == "Dag Toevoegen") {
                $newSpecialTimesheet = new Timesheet;
                if (!$singleDay->isWeekend()) {
                    $addDay = $this->setDay($newSpecialTimesheet, $dayType, $worker, $singleDay);
                    if (!$addDay) {
                        return redirect()->route('specials', ['worker' => $worker])->with('error', 'Er is al een dag toegevoegd op: ' . $singleDay->toDateString());
                    }
                }
            } elseif ($submitType == 'Periode Toevoegen') {
                $result = $this->setPeriod($dayType,$worker,$startDate,$endDate);
              if($result !== true) return $result;
            }
        }
        return redirect('/my-workers');
    }


    public function calculateBreakHours($start, $end)
    {
        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;
        if ($start === null) {
            return 0;
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


    public function calculateUserTotal($date, $id)
    {
        $userTotal = $this->fetchUserTotal($date, $id);
        is_string($date) ? $date = Carbon::parse($date) : $date = $date;
        $userId = $id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->sum('OverTime');
        }
        $userTotal->save();
        return true;
    }
}
