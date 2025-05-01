<?php

namespace App\Http\Controllers;


use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{



    public function startWorking()
    {
        DB::transaction(function () {
            $currentUser = auth()->user();
            $userRow = $currentUser->timelogs;
            $now = now('Europe/Brussels');

            // Ensure UserDayTotal exists
            UserUtility::findOrCreateUserDayTotal($now, $currentUser->id);

            $weekDay = Carbon::parse($now)->weekday();

            $isWeekend = $weekDay == $currentUser->company->weekend_day_1 || $weekDay == $currentUser->company->weekend_day_2;

            $userRow->Weekend = $isWeekend;
            $userRow->update([
                'StartWork' => $now,
                'StartBreak' => null,
                'EndBreak' => null,
                'StopWork' => null,
                'userNote' => null,
                'ShiftStatus' => true
            ]);
        });

        return redirect('/dashboard');
    }



    public function break()
    {


        DB::transaction(function () {
            $now = now('Europe/Brussels');
            $userRow = auth()->user()->timelogs;
            $dayTotal = UserUtility::findOrCreateUserDayTotal($now, auth()->user()->id);
            $currentUser = auth()->user();


            if ($userRow->StartBreak !== null) {
                $timesheet = (object) [
                    'UserId' => $currentUser->id,
                    'StartWork' => null,
                    'StopWork' => null,
                    'StartBreak' => $userRow->StartBreak,
                    'EndBreak' => $userRow->EndBreak,
                    'Weekend' => $userRow->Weekend,
                    'userNote' =>  null,
                ];
                $timesheetEntry = TimeloggingUtility::createTimesheetEntry($timesheet, $currentUser);
                TimeloggingUtility::updateOrInsertTimesheet($timesheetEntry, null);
            }

            $userRow->update([
                'StartBreak' => $now,
                'BreakStatus' => true,
                'BreaksTaken' => $userRow->BreaksTaken += 1
            ]);

            $dayTotal->update([
                'Regularhours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
                    $userRow->EndBreak ?: $userRow->StartWork,
                    $now
                ),
            ]);
        });

        return redirect()->back();
    }


    public function stopBreak()
    {
        $now = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        DB::transaction(function () use ($userRow, $now) {
            $dayTotal = UserUtility::findOrCreateUserDayTotal($now, auth()->user()->id);
            $userRow->update([
                'BreakStatus' => false,
                'EndBreak' => $now,
            ]);
            $dayTotal->update([
                'Breakhours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now),
                // 'BreaksTaken' => $dayTotal->BreaksTaken += 1,
            ]);
        });
        return redirect()->back();
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $now = now('Europe/Brussels');

        DB::transaction(function () use ($userRow, $now) {
            $dayTotal = UserUtility::findOrCreateUserDayTotal($now, auth()->user()->id);
            $userRow->update([
                'ShiftStatus' => false,
                'StopWork' => $now,
            ]);
            if ($userRow->BreakStatus == true) {
                $userRow->update([
                    'BreakStatus' => false,
                    'EndBreak' => $now,
                ]);
                $dayTotal->update([
                    'BreakHours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now)
                ]);
            }
            $dayTotal->update([
                'RegularHours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
                    $userRow->EndBreak ?? $userRow->StartWork,
                    $now
                ),

            ]);
        });
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
