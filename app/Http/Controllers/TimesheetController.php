<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
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
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $timesheetCheck = Timesheet::where('UserId', '=', $id)
            ->whereMonth('Month', '=', $date)
            ->whereDay('Month', $date)
            ->first();
        return $timesheetCheck;
    }

    public function makeTimesheet($id)
    {
        $userRow = Timelog::where('UserId', $id)->first();
        $newTimeSheet = new Timesheet;

        $timesheetCheck = $this->timesheetCheck(now('Europe/Brussels'), $id);
        if ($timesheetCheck !== null) return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, kijk je profiel na.');
        $newTimeSheet->UserId = $id;
        $newTimeSheet->ClockedIn = $userRow->StartWork;
        $newTimeSheet->ClockedOut = $userRow->StopWork;
        $userRow->userNote !== null ? $newTimeSheet->userNote = $userRow->userNote : null;
        $userRow->save();

        $newTimeSheet->BreakStart = $userRow->StartBreak;
        $newTimeSheet->BreakStop = $userRow->EndBreak;
        $breakHours = $userRow->BreakHours;
        $clockedTime = $this->calculateClockedHours($userRow->StartWork, $userRow->StopWork);

        $regularHours = ($clockedTime - $breakHours) + $userRow->RegularHours;
        $newTimeSheet->BreakHours = $breakHours;
        $result = $this->calculateHourBalance($regularHours, $userRow->StartWork, $userRow->Weekend,  $newTimeSheet, 'new');


<<<<<<< HEAD
        $result = $this->calculateHourBalance($regularHours, $userRow->StartWork, $userRow->Weekend,  $newTimeSheet, 'new');
=======
>>>>>>> main

        $total = $this->calculateUserTotal(now('Europe/Brussels'), $id);
        if ($result == true && $total == true) return redirect('/dashboard');
    }

    public function addNewTimesheet(Request $request)
    {
        $newTimesheet = new Timesheet;
        $weekend = false;
        $date = $request->input('newTimesheetDate');
        $id = $request->input('workerId');

        // $carbonDate = Carbon::parse($date, 'Europe/Brussels');
        $timesheetCheck = $this->timesheetCheck($date, $id);

        if ($timesheetCheck !== null) {
            return redirect()->route('timesheetForm', ['worker' => $id])->with('error', 'Datum al in gebruik');
        }
        if (Carbon::parse($date, 'Europe/Brussels')->isWeekend()) $weekend = true;

        $start = $request->input('startTime');
        $end = $request->input('endTime');
        $dateTimeStart = Carbon::parse($date . ' ' . $start, 'Europe/Brussels');
        $dateTimeEnd = Carbon::parse($date . ' ' . $end, 'Europe/Brussels');
        $dateTimeEndClone = clone $dateTimeEnd;
        $startBreak = $dateTimeEndClone->subMinutes(30);
        $break = $this->calculateBreakHours($startBreak, $dateTimeEnd);
        $newTimesheet->UserId = $id;
        $newTimesheet->ClockedIn = $dateTimeStart;
        $newTimesheet->ClockedOut = $dateTimeEnd;
        $newTimesheet->BreakStart = $startBreak;
        $newTimesheet->BreakStop = $dateTimeEnd;
        $newTimesheet->BreakHours = $break;
        $clockedTime = $this->calculateClockedHours($start, $end);
        $regularHours = $clockedTime - $break;
        $balance = $this->calculateHourBalance($regularHours, $date, $weekend, $newTimesheet, 'new');
        $total = $this->calculateUserTotal($date, $id);
        if ($balance == true && $total == true) return redirect('/my-workers');
    }

    public function setDay($dayLabel, $newSpecialTimesheet, $dayType, $worker, $singleDay)
    {
        $timesheetCheck = $this->timesheetCheck($singleDay, $worker);

        if (!$timesheetCheck) {
            $newSpecialTimesheet->type = $dayLabel;
            $newSpecialTimesheet->ClockedIn = $singleDay;
            $newSpecialTimesheet->Month = $singleDay;
            $newSpecialTimesheet->UserId = $worker;
            if ($dayType == "onbetaald") {
                $newSpecialTimesheet->save();
                $userTotal = $this->fetchUserTotal($singleDay, $worker);
                $this->calculateUserTotal($singleDay, $worker);
                $userTotal->save();
                return true;
            }
            $newSpecialTimesheet->accountableHours = 7.6;
            $newSpecialTimesheet->save();
            $userTotal = $this->fetchUserTotal($singleDay, $worker);
            $userTotal->save();
            $this->calculateUserTotal($singleDay, $worker);
            return true;
        } else {
            return  'Datum al in gebruik: ' . $singleDay->toDateString();
        }
    }

    public function setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate)
    {
        $errors = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $newSpecialTimesheet = new Timesheet;
            if (!$currentDate->isWeekend()) {
                $addDay =  $this->setDay($dayLabel, $newSpecialTimesheet, $dayType, $worker, $currentDate);
                if ($addDay !== true) {
                    array_push($errors, 'Datum al in gebruik: ' . $currentDate->toDateString());
                }
            }
            $currentDate->addDay();
        }

        if (!empty($errors)) {

            return $errors;
        } else {
            return true;
        }
    }

    public function setSpecial(Request $request)
    {

        $dayType = $request->input('dayType');
        $dayLabel = $request->input($dayType);
        $worker = $request->input('worker');
        $submitType = $request->input('submitType');
        $workerArray = json_decode($worker, true);
        $results = [];
        // $note = $request->input('hollidayNote');
        // $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald verlof', 'Betaald verlof', 'Feestdag', 'Solicitatie verlof'];
        // if ($dayType == null ) {
        //     $dayType = $request->input('customInput');
        //     $custom = true;
        // }
        if ($submitType ==  'Periode Toevoegen') {
            $validator = Validator::make(
                $request->all(),
                [
                    'startDate' => 'required|date',
                    'endDate' => 'required|date|after:startDate',
                ]
            );


            if ($validator->fails()) {
                if (is_array($workerArray) && count($workerArray) > 1) {
                    return redirect()
                        ->route('specials', ['worker' => $worker])
                        ->with('errors', ['result' => ['id' => 0, 'errorList' => ['Geen geldige datum doorgegeven.']]]);
                }
                return redirect()
                    ->route('specials', ['worker' => $worker])
                    ->with('errors', ['result' => ['id' => $worker, 'errorList' => ['Geen geldige datum doorgegeven.']]]);
            }
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'singleDay' => 'required|date',
                ]
            );

            if ($validator->fails()) {
                if (is_array($workerArray) && count($workerArray) > 1) {
                    return redirect()
                        ->route('specials', ['worker' => $worker])
                        ->with('error', ['result' => ['id' => 0, 'errorList' => 'Geen geldige datum doorgegeven.']]);
                }
                return redirect()
                    ->route('specials', ['worker' => $worker])
                    ->with('error', ['result' => ['id' => $worker, 'errorList' => 'Geen geldige datum doorgegeven.']]);
            }
        }
        if ($submitType == "Dag Toevoegen") {
            $singleDay = Carbon::parse($request->input('singleDay'));
            $newSpecialTimesheet = new Timesheet;
            if (!$singleDay->isWeekend()) {
                if (is_array($workerArray) && count($workerArray) > 1) {
                    foreach ($workerArray as $user) {
                        $newSpecialTimesheetForEveryone = new Timesheet;
                        // if ($user['admin'] == true) {
                        //     continue;
                        // }
                        $result = $this->setday($dayLabel, $newSpecialTimesheetForEveryone, $dayType, $user['id'], $singleDay);
                        if ($result !== true) {
                            array_push($results, ['id' => $user['id'], 'errorList' => $result]);
                        }
                    }
                    if (!empty($results)) {
                        return redirect()->route('specials', ['worker' => $worker])->with('error', $results);
                    }
                } 
                else {

                    $addDay = $this->setDay($dayLabel, $newSpecialTimesheet, $dayType, $worker, $singleDay);
                    if ($addDay !== true) {
                        array_push($results, ['id' => $worker, 'errorList' => $addDay]);
                        return redirect()->route('specials', ['worker' => $worker])->with('error', $results);
                    }
                }
            }
        } elseif ($submitType == 'Periode Toevoegen') {
            $startDate = Carbon::parse($request->input('startDate'));
            $endDate = Carbon::parse($request->input('endDate'));
            if (is_array($workerArray) && count($workerArray) > 1) {
                foreach ($workerArray as $user) {

                    // if ($user['admin'] == true) {
                    //     continue;
                    // }
                    $result = $this->setPeriod($dayLabel, $dayType, $user['id'], $startDate, $endDate);
                    if ($result !== true) {
                        array_push($results, ['id' => $user['id'], 'errorList' => $result]);
                    }
                }
                if (!empty($results)) {
                    return redirect()->route('specials', ['worker' => $worker])->with('errors', $results);
                }
            } else {
                $result = $this->setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate);
                if ($result !== true) {
                    array_push($results, ['id' => $worker, 'errorList' => $result]);
                    return redirect()->route('specials', ['worker' => $worker])->with('errors',  $results);
                }
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
        $decimalTime = round($diffInMin / 60, 2, PHP_ROUND_HALF_UP);

        return $decimalTime;
    }

    public function calculateClockedHours($start, $end)
    {
        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;


        $diffInMin = $end->diffInMinutes($start);

        $decimalTime = round($diffInMin / 60, 2, PHP_ROUND_HALF_UP);


        return $decimalTime;
    }

    public function calculateHourBalance($regularHours, $date, $weekend, $timesheet, $type)
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
                $timesheet->OverTime = $regularHours;

                break;
            default:
                $timesheet->RegularHours = 7.60;
                $timesheet->accountableHours = 7.60;
                $timesheet->OverTime = 0;
                break;
        }
        if ($type == 'new') $timesheet->Month = $date;


        return $timesheet->save();
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

        return $userTotal->save();
    }
}
