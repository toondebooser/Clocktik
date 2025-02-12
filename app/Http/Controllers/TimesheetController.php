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
use App\Services\TimeloggingService;

class TimesheetController extends Controller
{
    protected $timeloggingService;

    public function __construct(TimeloggingService $timeloggingService)
    {
        $this->timeloggingService = $timeloggingService;
    }

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
        $timesheetCheck = Timesheet::where('UserId', $id)
            ->whereDate('Month', $date)
            ->orderBy('ClockedIn', 'asc')
            ->get();
        return $timesheetCheck;
    }

    public function makeTimesheet($id)
    {
        // $newTimeSheet = new Timesheet;

        $userRow = auth()->user()->timelogs;

        $timesheetCheck = $this->timesheetCheck(now('Europe/Brussels'), $id);
        if (!$timesheetCheck->isEmpty() && $timesheetCheck->first()->type !== 'workday') {
            return redirect()->route('dashboard')->with('error', 'Vandaag kan jij geen werkuren ingeven, kijk je profiel na.');
        }
        $buildTimesheet = $this->timeloggingService->logTimeEntry($userRow, $id, null);
        //         //TODO: 
        //         //- daytimelog of new timesheet += count of data retrieved'
        //         $count = count($timesheetCheck);
        //         $newTimeSheet->fill([
        //             'UserId' => $id,
        //             'ClockedIn' => $userRow->StartWork,
        //             'ClockedOut' => $userRow->StopWork,
        //             'BreakStart' => $userRow->StartBreak,
        //             'BreakStop' => $userRow->EndBreak,
        //             'DaytimeCount' => ($newTimeSheet->DaytimeCount ?? 1) + $count,
        //             'Month' => Carbon::parse($userRow->StartWork)->format('Y-m-d'),
        //             'userNote' => $userRow->userNote ?? null,

        //         ]);
        //         $newTimeSheet->save();
        //         $timesheetCheck->first()->fill([
        //             'RegularHours' => $userRow->RegularHours > 7.6 ? 7.6 : $userRow->RegularHours,
        //             'BreakHours' => $userRow->BreakHours,
        //             'OverTime' => $userRow->RegularHours - 7.6,
        //         ]);
        // //         $timesheetCheck->first()->save();
        //         $total = $this->calculateUserTotal(now('Europe/Brussels'), $id);
        //         // if ($result == true && $total == true) 
        //         if ($total) {
        //             return redirect('/dashboard');
        //         }
        //     }

        //     $userRow->userNote !== null ? $newTimeSheet->userNote = $userRow->userNote : null;

        //     $newTimeSheet->fill([
        //         'UserId' => $id,
        //         'ClockedIn' => $userRow->StartWork,
        //         'ClockedOut' => $userRow->StopWork,
        //         'BreakStart' => $userRow->StartBreak,
        //         'BreakStop' => $userRow->EndBreak,
        //         'BreakHours' => $userRow->BreakHours,
        //         'RegularHours' => $userRow->RegularHours > 7.6 ? 7.6 : $userRow->RegularHours,
        //         'OverTime' => $userRow->RegularHours - 7.6,
        //         'Weekend' => $userRow->Weekend,
        //         'userNote' => $userRow->userNote ?? null,
        //         'Month' => $userRow->StartWork,
        //         'accountableHours' => $userRow->Weekend ? 0 : 7.6,

        //     ]);
        //     $newTimeSheet->save();


        $total = $this->calculateUserTotal(now('Europe/Brussels'), $id);
        if ($total && $buildTimesheet) {
            return redirect('/dashboard');
        }
        // }
    }
    public function addNewTimesheet(Request $request)
    {
        $newTimesheet = new Timesheet;
        $weekend = false;
        $date = $request->input('newTimesheetDate');
        $id = $request->input('workerId');

        // $carbonDate = Carbon::parse($date, 'Europe/Brussels');
        $timesheetCheck = $this->timesheetCheck($date, $id);

        if (!$timesheetCheck->isEmpty()) {
            return redirect()->route('timesheetForm', ['worker' => $id])->with('error', 'Datum al in gebruik: ' . $date);
        }
        if (Carbon::parse($date, 'Europe/Brussels')->isWeekend()) $weekend = true;

        $startWork = Carbon::parse($date . ' ' . $request->input('startTime'), 'Europe/Brussels');
        $stopWork = Carbon::parse($date . ' ' . $request->input('endTime'), 'Europe/Brussels');
        $stopWorkClone = clone $stopWork;
        $startBreak = $stopWorkClone->subMinutes(30);
        $break = $this->calculateDecimal($startBreak, $stopWork);
        $regularHours = $this->calculateDecimal($startWork, $stopWork) - $break;
        $newTimesheet->fill([
            'UserId' => $id,
            'ClockedIn' => $startWork,
            'ClockedOut' => $stopWork,
            'BreakStart' => $startBreak,
            'BreakStop' => $stopWork,
            'BreakHours' => $break,
            'RegularHours' => $regularHours > 7.6 ? 7.6 : $regularHours,
            'OverTime' => $regularHours - 7.6,
            'accountableHours' => 7.6,
            'Month' => $startWork,

        ]);
        $newTimesheet->save();
        $total = $this->calculateUserTotal($date, $id);
        if ($total == true) return redirect('/my-workers');
    }

    public function setDay($dayLabel, $newSpecialTimesheet, $dayType, $worker, $singleDay)
    {
        $timesheetCheck = $this->timesheetCheck($singleDay, $worker);

        if (!$timesheetCheck->isEmpty()) {
            //TODO:"simplify
            $newSpecialTimesheet->fill([
                'type' => $dayLabel,
                'ClockedIn' => $singleDay,
                'Month' => $singleDay,
                'UserId' => $worker,
                'accountableHours' => $dayType == 'onbetaald' ? 0 : 7.6,
            ]);
            // if ($dayType == "onbetaald") {
            //     $newSpecialTimesheet->save();
            //     $userTotal = $this->fetchUserTotal($singleDay, $worker);
            //     $this->calculateUserTotal($singleDay, $worker);
            //     $userTotal->save();
            //     return true;
            // }
            $newSpecialTimesheet->accountableHours = 7.6;
            $newSpecialTimesheet->save();
            $userTotal = $this->fetchUserTotal($singleDay, $worker);
            $this->calculateUserTotal($singleDay, $worker);
            $userTotal->save();
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
                        if ($user['admin'] == true) {
                            continue;
                        }
                        $result = $this->setday($dayLabel, $newSpecialTimesheetForEveryone, $dayType, $user['id'], $singleDay);
                        if ($result !== true) {
                            array_push($results, ['id' => $user['id'], 'errorList' => $result]);
                        }
                    }
                    if (!empty($results)) {
                        return redirect()->route('specials', ['worker' => $worker])->with('error', $results);
                    }
                } else {

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


    public function calculateDecimal($start, $end)
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


    public function calculateUserTotal($date, $id)
    {
        $userTotal = $this->fetchUserTotal($date, $id);
        is_string($date) ? $date = Carbon::parse($date) : null;
        $userId = $id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('OverTime');
        }

        return $userTotal->save();
    }
}
