<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\TimeloggingService;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;

class TimesheetController extends Controller
{
    protected $timeloggingService;

    public function __construct(TimeloggingService $timeloggingService)
    {
        $this->timeloggingService = $timeloggingService;
    }





    public function makeTimesheet($id)
    {

        $userRow = auth()->user()->timelogs;

        $userDayTotalCheck = UserUtility::userDayTotalCheck(now('Europe/Brussels'), $id);
        if (!$userDayTotalCheck->isEmpty() && $userDayTotalCheck->first()->type !== 'workday') {
            return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, kijk je profiel na.');
        }
        $buildTimesheet = new TimeloggingUtility;
        $buildTimesheet->logTimeEntry($userRow, $id, null);

        // $total = CalculateUtility::calculateUserTotal(now('Europe/Brussels'), $id);
        if ($buildTimesheet) {
            return redirect('/dashboard');
        }
    }
    public function addNewTimesheet(Request $request)
    {
        // $newTimesheet = new Timesheet;
        $timeloggingUtility = new TimeloggingUtility;
        $date = $request->input('newTimesheetDate');
        $id = $request->input('workerId');
        $timesheetCheck = UserUtility::userDayTotalCheck($date, $id);
        if (!$timesheetCheck->isEmpty()) {
            return redirect()->route('timesheetForm', ['worker' => $id])->with('error', 'Datum al in gebruik: ' . $date);
        }
        if (Carbon::parse($date, 'Europe/Brussels')->isWeekend()) $weekend = true;
        
        $userRow = (object) [
            'UserId' => $id,
            'StartWork' => Carbon::parse($date . ' ' . $request->input('startTime'), 'Europe/Brussels'),
            'StopWork' => Carbon::parse($date . ' ' . $request->input('endTime'), 'Europe/Brussels'),
            'StartBreak' => Carbon::parse($date . ' ' . $request->input('endTime'), 'Europe/Brussels')->subMinutes(30),
            'EndBreak' => Carbon::parse($date . ' ' . $request->input('endTime'), 'Europe/Brussels'),
            'Weekend' => $weekend ?? false,
            'userNote' => $userNote ?? null,
        ];
        $addTimesheet = $timeloggingUtility->logTimeEntry($userRow, $id, null);
        // $newTimesheet->save();
        // $total = CalculateUtility::calculateUserTotal($date, $id);
        if ($addTimesheet) return redirect('/my-workers');
    }

    public function setDay($dayLabel, $dayType, $worker, $singleDay)
    {
        $dayTotal = Daytotal::firstOrCreate([
            'UserId'=>$worker,
            'Month' => $singleDay
        ],[
            'type' => $dayLabel,
            'company_code' => '1234567890',
            'ClockedIn' => $singleDay,
            'Month' => $singleDay,
            'UserId' => $worker,
            'accountableHours' => $dayType == 'onbetaald' ? 0 : 7.6,
        ]);
        if ($dayTotal->wasRecentlyCreated) {
            // $newSpecialTimesheet->fill([
            //     'type' => $dayLabel,
            //     'ClockedIn' => $singleDay,
            //     'Month' => $singleDay,
            //     'UserId' => $worker,
            //     'accountableHours' => $dayType == 'onbetaald' ? 0 : 7.6,
            // ]);
            // $newSpecialTimesheet->save();
            // $userTotal = UserUtility::fetchUserTotal($singleDay, $worker);
           $calculateUserTotal =  CalculateUtility::calculateUserTotal($singleDay, $worker);
            // $userTotal->save();
           if ($calculateUserTotal) return true;
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
                $addDay =  $this->setDay($dayLabel, $dayType, $worker, $currentDate);
                if ($addDay !== true) {
                    //TODO: push $addDay directly in error?
                    array_push($errors, $addDay);
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

        if ($submitType ==  'Periode Toevoegen') {
            $validator = Validator::make(
                $request->all(),
                [
                    'startDate' => 'required|date',
                    'endDate' => 'required|date|after:startDate',
                ]
            );

            //TODO REFACTOR THIS MESS!!!
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
                        // $newSpecialTimesheetForEveryone = new Timesheet;
                        // if ($user['admin'] == true) {
                        //     continue;
                        // }
                        $result = $this->setday($dayLabel, $dayType, $user['id'], $singleDay);
                        if ($result !== true) {
                            array_push($results, ['id' => $user['id'], 'errorList' => $result]);
                        }
                    }
                    if (!empty($results)) {
                        return redirect()->route('specials', ['worker' => $worker])->with('error', $results);
                    }
                } else {

                    $addDay = $this->setDay($dayLabel, $dayType, $worker, $singleDay);
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

                    if ($user['admin'] == true) {
                        continue;
                    }
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
}
