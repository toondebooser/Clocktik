<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\Daytotal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Hamcrest\Type\IsString;

class TimesheetController extends Controller
{


    // MAKE TIMESHEET FROM LIVE LOGS

    public function makeTimesheet($id)
    {

        $userRow = auth()->user()->timelogs;
        $timesheet = null;
        $userDayTotalCheck = $userRow->dayTotal;
        if ($userDayTotalCheck && $userDayTotalCheck->type !== 'workday') {
            return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, kijk je profiel na.');
        }
        if ($userRow->timesheet_id !== null) {
            $timesheet = $userRow->timesheet_id;
            $userRow = (object) [
                'UserId' => $userRow->userId,
                'daytotal_id' => $userRow->dayTotal_id,
                'StartWork' => $userRow->StartWork,
                'StopWork' => $userRow->StopWork,
                'userNote' => $userRow->userNote,
                'Month' => $userRow->StartWork->format('Y-m-d'),
            ];
        }
        TimeloggingUtility::logTimeEntry($userRow, $id, $timesheet);
        UserActivityLogger::log('Timesheet made:', $userRow);
        return redirect()->back()->with('Succes', 'Uren succesvol toegevoegd');
    }

    //ADDING CUSTOM TIMESHEET LOGIC

    public function addNewTimesheet(Request $request)
    {

        $date = $request->newTimesheetDate;
        $id = $request->workerId;
        $validator = Validator::make(
            $request->all(),
            [
                'startTime'   => 'required|date_format:H:i',
                'endTime'     => 'required|date_format:H:i|after:startTime',
                'StartBreak' => 'nullable|date_format:H:i',
                'EndBreak'  => 'nullable|date_format:H:i|after:StartBreak',

                'newTimesheetDate' => 'required|date'
            ]
        );
        
        if ($validator->fails()) {
            return redirect()->route('timesheetForm', ['worker' => $id])->withErrors($validator)->withInput();
        }
        $dayTotalCheck = UserUtility::findOrCreateUserDayTotal($date, $id);
        if (!$dayTotalCheck->wasRecentlyCreated) {
            return redirect()->route('timesheetForm', ['worker' => $id])->withErrors(['error', 'Datum al in gebruik: ' . $date]);
        }
        $userRow = (object) [
            'UserId' => $id,
            'StartWork' => Carbon::parse($date . ' ' . $request->startTime, 'Europe/Brussels'),
            'StopWork' => Carbon::parse($date . ' ' . $request->endTime, 'Europe/Brussels'),
            'StartBreak' => Carbon::parse($date . ' ' . $request->StartBreak, 'Europe/Brussels'),
            'EndBreak' => Carbon::parse($date . ' ' . $request->EndBreak, 'Europe/Brussels'),
            'Weekend' => DateUtility::checkWeekend($date, User::find($id)->company->company_code),
            'userNote' => $userNote ?? null,
        ];
        if (isset($request->StartBreak) && $request->StartBreak !== null) {
            $userRow->StartBreak = $request->StartBreak;
        }
        if (isset($request->EndBreak) && $request->EndBreak !== null ) {
            $userRow->EndBreak = $request->EndBreak;
        }
        $addTimesheet = TimeloggingUtility::logTimeEntry($userRow, $id, null);
        $checkUserMonthTotal = UserUtility::CheckUserMonthTotal($date, $id);
        $calculateTotal = CalculateUtility::calculateUserTotal($id);
        if ($checkUserMonthTotal && $addTimesheet && $calculateTotal) return redirect()->route('timesheetForm', ['worker' => $id])->with('success', 'Uurrooster toegevoegd');
    }


    //ADDING VACATION LOGIC 

    public static function setDay($dayLabel, $dayType, $userId, $singleDay)
    {
        $singleDay = $singleDay instanceof Carbon ? $singleDay : Carbon::parse($singleDay);
        if (DateUtility::checkWeekend($singleDay, User::find($userId)->company->company_code)) {
            return  $singleDay->toDateString() . ' is een weekend dag';
        };
        $dayTotal = Daytotal::where([
            'UserId' => $userId,
            'Month' => $singleDay,
        ])->first();

        if (!$dayTotal) {
            $dayTotal = Daytotal::create([
                'UserId' => $userId,
                'Month' => $singleDay,
                'type' => $dayLabel,
                'ClockedIn' => $singleDay,
                'Completed' => true,
                'official_holiday' => DateUtility::isValidHolidayName($dayLabel),
                'accountableHours' => $dayType == 'onbetaald' ? 0 : User::find($userId)->company->day_hours,
            ]);
            return true; // New record created
        } else {
            return 'Datum al in gebruik: ' . $singleDay->toDateString() . ' (' . User::find($userId)->name . ')';
        }
    }

    public function setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate)
    {
        $errors = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            // $weekDay = Carbon::parse($currentDate)->weekday();

            $addDay =  $this->setDay($dayLabel, $dayType, $worker, $currentDate);
            if ($addDay !== true) {
                //TODO: push $addDay directly in error?
                array_push($errors, $addDay);
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
            if (is_array($workerObjectArray) && count($workerObjectArray) > 1) {
                foreach ($workerObjectArray as $userObject) {
                    $user = User::find($userObject['id']);
                    if ($user->admin && !$user->company->admin_timeclock) {
                        continue;
                    }

                    $result = $this->setday($dayLabel, $dayType, $userObject['id'], $singleDay);
                    UserUtility::CheckUserMonthTotal($singleDay->copy(), $worker);
                    CalculateUtility::calculateUserTotal($user->id);
                    if ($result !== true) {
                        array_push($results, ['id' => $userObject['id'], 'errorList' => $result]);
                    }
                }
                if (!empty($results)) {
                    return redirect()->route('specials', ['worker' => $worker])->with('err', $results);
                }
            } else {

                $addDay = $this->setDay($dayLabel, $dayType, $worker, $singleDay);
                UserUtility::CheckUserMonthTotal($singleDay, $worker);
                CalculateUtility::calculateUserTotal($worker);
                if ($addDay !== true) {
                    array_push($results, ['id' => $worker, 'errorList' => $addDay]);
                    return redirect()->route('specials', ['worker' => $worker])->with('err', $results);
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
                    UserUtility::CheckUserMonthTotal($startDate, $user->id);
                    CalculateUtility::calculateUserTotal($user->id);
                }
                if (!empty($results)) {
                    return redirect()->route('specials', ['worker' => $worker])->with('errList', $results);
                }
            } else {
                $result = $this->setPeriod($dayLabel, $dayType, $worker, $startDate, $endDate);
                UserUtility::CheckUserMonthTotal($startDate->copy(), $worker);
                CalculateUtility::calculateUserTotal($worker);
                if ($result !== true) {
                    array_push($results, ['id' => $worker, 'errorList' => $result]);
                    return redirect()->route('specials', ['worker' => $worker])->with('errList',  $results);
                }
                if ($result) return redirect()->route('specials', ['worker' => $worker])->with('success', "Periode succesvol toegevoegd");
            }
        }

        return redirect('/')->with('error', 'Something went wrong try again or call for support');
    }
    // public static function addHolidaysForMonth($holidays, $company_code)
    // {
    //     foreach ($holidays as $date => $name) {
    //         TimesheetController::setDay($name,'betaald',$userId,$date);
    //     };

    //     return true;
    // }   

}
