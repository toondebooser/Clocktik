<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\TimeloggingService;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;

class TimesheetController extends Controller
{
    // protected $timeloggingService;

    // public function __construct(TimeloggingService $timeloggingService)
    // {
    //     $this->timeloggingService = $timeloggingService;
    // }





    public function makeTimesheet($id)
    {

        $userRow = auth()->user()->timelogs;
        $userDayTotalCheck = UserUtility::userDayTotalFetch(now('Europe/Brussels'), $id);
        if ($userDayTotalCheck && $userDayTotalCheck->type !== 'workday') {
            return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, kijk je profiel na.');
        }
        $buildTimesheet = new TimeloggingUtility;
        $buildTimesheet->logTimeEntry($userRow, $id, null);
        if($buildTimesheet) return redirect()->back()->with('Succes', 'Uren succesvol toegevoegd');
    }


    public function addNewTimesheet(Request $request)
    {
        $timeloggingUtility = new TimeloggingUtility;
        $date = $request->input('newTimesheetDate');
        $id = $request->input('workerId');
        $dayTotalCheck = UserUtility::findOrCreateUserDayTotal($date, $id);
        if (!$dayTotalCheck->wasRecentlyCreated) {
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
        $total = CalculateUtility::calculateUserTotal( $id);
        if ($addTimesheet) return redirect()->route('timesheetForm', ['worker' => $id])->with('success', 'Uurrooster toegevoegd');
    }

    public function setDay($dayLabel, $dayType, $worker, $singleDay)
    {
        $dayTotal = Daytotal::firstOrCreate([
            'UserId' => $worker,
            'Month' => $singleDay
        ], [
            'type' => $dayLabel,
            'ClockedIn' => $singleDay,
            'Month' => $singleDay,
            'UserId' => $worker,
            'Completed' => true,
            'accountableHours' => $dayType == 'onbetaald' ? 0 : User::find($worker)->company->day_hours,
        ]);
        if ($dayTotal->wasRecentlyCreated) {
            
           $calculateUserTotal = CalculateUtility::calculateUserTotal($worker);
            if ($calculateUserTotal) return true;
        } else {
            return  'Datum al in gebruik: ' . $singleDay->toDateString();
        }
    }

    public function setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate)
    {
        $errors = [];
        $currentDate = clone $startDate;
        $company_weekend_day_1= User::find($worker)->company->weekend_day_1 ;
        $company_weekend_day_2= User::find($worker)->company->weekend_day_2;
        while ($currentDate <= $endDate) {
            $weekDay = Carbon::parse($currentDate)->weekday();
            if ($weekDay != $company_weekend_day_1 && $weekDay != $company_weekend_day_2) {
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
        $workerObjectArray = json_decode($worker, true);
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
                if (is_array($workerObjectArray) && count($workerObjectArray) > 1) {
                    return redirect()
                        ->route('specials', ['worker' => $worker])
                        ->with('errList', ['result' => ['id' => 0, 'errorList' => ['Geen geldige datum doorgegeven.']]]);
                }
                return redirect()
                    ->route('specials', ['worker' => $worker])
                    ->with('errList', ['result' => ['id' => $worker, 'errorList' => ['Geen geldige datum doorgegeven.']]]);
            }
        } else {
            $validator = Validator::make(
                $request->all(),
                [
                    'singleDay' => 'required|date',
                ]
            );

            if ($validator->fails()) {
                if (is_array($workerObjectArray) && count($workerObjectArray) > 1) {
                    return redirect()
                        ->route('specials', ['worker' => $worker])
                        ->with('err', ['result' => ['id' => 0, 'errorList' => 'Geen geldige datum doorgegeven.']]);
                }
                return redirect()
                    ->route('specials', ['worker' => $worker])
                    ->with('err', ['result' => ['id' => $worker, 'errorList' => 'Geen geldige datum doorgegeven.']]);
            }
        }
        if ($submitType == "Dag Toevoegen") {
            $singleDay = Carbon::parse($request->input('singleDay'));
            $newSpecialTimesheet = new Timesheet;
            if (!$singleDay->isWeekend()) {
                if (is_array($workerObjectArray) && count($workerObjectArray) > 1) {
                    foreach ($workerObjectArray as $userObject) {
                        $user = User::find($userObject['id']);
                        // $newSpecialTimesheetForEveryone = new Timesheet;
                        if ($user->admin && !$user->company->admin_timeclock) {
                            continue;
                        }
                        $result = $this->setday($dayLabel, $dayType, $userObject['id'], $singleDay);
                        if ($result !== true) {
                            array_push($results, ['id' => $userObject['id'], 'errorList' => $result]);
                        }
                    }
                    if (!empty($results)) {
                        return redirect()->route('specials', ['worker' => $worker])->with('err', $results);
                    }
                } else {

                    $addDay = $this->setDay($dayLabel, $dayType, $worker, $singleDay);
                    if ($addDay !== true) {
                        array_push($results, ['id' => $worker, 'errorList' => $addDay]);
                        return redirect()->route('specials', ['worker' => $worker])->with('err', $results);
                    }
                }
            }
        } elseif ($submitType == 'Periode Toevoegen') {
            $startDate = Carbon::parse($request->input('startDate'));
            $endDate = Carbon::parse($request->input('endDate'));
            if (is_array($workerObjectArray) && count($workerObjectArray) > 1) {
                foreach ($workerObjectArray as $userObject) {

                    $user = User::find($userObject['id']);
                    if ($user->admin && !$user->company->admin_timeclock) {
                        continue;
                    }
                    $result = $this->setPeriod($dayLabel, $dayType, $userObject['id'], $startDate, $endDate);
                    if ($result !== true) {
                        array_push($results, ['id' => $userObject['id'], 'errorList' => $result]);
                    }
                }
                if (!empty($results)) {
                    return redirect()->route('specials', ['worker' => $worker])->with('errList', $results);
                }
            } else {
                $result = $this->setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate);
                if ($result !== true) {
                    array_push($results, ['id' => $worker, 'errorList' => $result]);
                    return redirect()->route('specials', ['worker' => $worker])->with('errList',  $results);
                }
            }
        }

        return redirect('/')->with('error', 'Something went wrong try again or call for support');
    }
}
