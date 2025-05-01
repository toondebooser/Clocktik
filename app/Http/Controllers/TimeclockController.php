<?php

namespace App\Http\Controllers;

use App\Models\Extra_break_slot;
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
           $dayTotal = UserUtility::findOrCreateUserDayTotal($now, $currentUser->id);

            $weekDay = Carbon::parse($now)->weekday();

            // $isWeekend = $weekDay == $currentUser->company->weekend_day_1 || $weekDay == $currentUser->company->weekend_day_2;

            // $userRow->Weekend = $isWeekend;
            $userRow->update([
                'daytotal_id' => $dayTotal->id,
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
            $dayTotal = $userRow->dayTotal;

            if ($userRow->StartBreak !== null && $userRow->BreaksTaken >= 1) {
                
                // $timesheet = (object) [
                //     'UserId' => $currentUser->id,
                //     'BreakStart' => null,
                //     'BreakStop' => null,
                // ];
                // $timesheetEntry = TimeloggingUtility::createTimesheetEntry($timesheet, $currentUser);
                // TimeloggingUtility::updateOrInsertTimesheet($timesheetEntry, null);
                // $extraBreakRow = Extra_break_slot::
                 Extra_break_slot::create([
                    'Month' => $now,
                    'UserId' => auth()->user()->id,
                    'daytotal_id' => $userRow->daytotal_id,
                    'BreakStart' => $userRow->StartBreak,
                    'BreakStop' => $userRow->EndBreak
                ]);
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
            $dayTotal = $userRow->dayTotal;
            $userRow->update([
                'BreakStatus' => false,
                'EndBreak' => $now,
            ]);
            $dayTotal->update([
                'Breakhours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now),
            ]);
        });
        return redirect()->back();
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $now = now('Europe/Brussels');

        DB::transaction(function () use ($userRow, $now) {
            $dayTotal = $userRow->dayTotal;
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
