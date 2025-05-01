<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Daytotal as ModelsDaytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class TimeloggingUtility
{


    public static function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $user = User::find($userId);
        if (!$user) {
            throw new Exception("User $userId not found");
        }


        $newEntry = static::createTimesheetEntry($userRow, $user);
        $timesheet =  static::updateOrInsertTimesheet($newEntry, $oldLog);
        $userRow->BrakesTaken >= 1 ? static::linkExtraBreakSlots($timesheet->id, $userRow) : null;
        static::updateDailySummery($userId, $newEntry['Month']);
        return CalculateUtility::calculateUserTotal($userId);
    }


    public static function linkExtraBreakSlots($timesheetId, $userRow)
    {
        foreach ($userRow->dayTotal->extraBreakSlots->get() as $breakSlot) {
            $breakSlot->update([
                'timesheet_id' => $timesheetId
            ]);
        }
    }
    public static function createTimesheetEntry($userRow, $user)
    {
        // if (!$user instanceof User) {
        //     Log::error('Invalid user in createTimesheetEntry', ['user' => $user]);
        //     throw new Exception('Expected a single User instance, received: ' . gettype($user));
        // }
        $date = $userRow->StartWork->format('Y-m-d');
        $dayTotal = UserUtility::findOrCreateUserDayTotal($date, $user->id);
        $overlapCheck = DateUtility::checkIfSameDay($userRow->StartWork, $userRow->StopWork);
        $dayTotal->update([
            "DayOverlap" => !$overlapCheck,
            "NightShift" => (!$overlapCheck || DateUtility::checkNightShift($userRow->StartWork) || DateUtility::checkNightShift($userRow->StopWork)) ? true : false,
        ]);
        return [
            'UserId' => $user->id,
            'daytotal_id' => $dayTotal->id,
            'ClockedIn' => $userRow->StartWork,
            'ClockedOut' => $userRow->StopWork,
            'BreakStart' => $userRow->StartBreak,
            'BreaksTaken' => $userRow->Breakstaken ?? 0,
            'BreakStop' => $userRow->EndBreak,
            'Weekend' => $userRow->Weekend,
            'Month' => $date,
            'userNote' => $userRow->userNote ?? null,
        ];
    }

    public static function updateOrInsertTimesheet(array $newEntry, $oldLog)
    {
        if ($oldLog != null) {
            $oldLog->update($newEntry);
        } else {
            $timesheet = Timesheet::create($newEntry);
            return $timesheet->id;
        }
    }

    public static function updateDailySummery($userId, $day)
    {
        $user = User::find($userId);
        if (!$user) {
            throw new Exception("User $userId not found");
        }

        $companyDayHours = $user->company->day_hours;
        $timesheets = $user->timesheets()->where('Month', $day)->get();
        $dayTotal = UserUtility::userDayTotalFetch($day, $userId);
        $summary = CalculateUtility::calculateSummaryForDay($timesheets, $companyDayHours);
        $dayTotal->update($summary);
    }
}
