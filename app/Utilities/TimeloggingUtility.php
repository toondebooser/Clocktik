<?php

namespace App\Utilities;

use App\Models\Daytotal;
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
        return DB::transaction(function () use ($userRow, $userId, $oldLog) {
            $user = User::find($userId);
            if (!$user) {
                throw new Exception("User $userId not found");
            }

            $newEntry = static::createTimesheetEntry($userRow, $user);
            $timesheet = static::updateOrInsertTimesheet($newEntry, $oldLog);
            if (isset($userRow->BreaksTaken) && $userRow->BreaksTaken >= 1) { 
                static::linkExtraBreakSlots($timesheet->id, $userRow);
            }
            static::updateDailySummery($userId, $newEntry['Month']);
            return CalculateUtility::calculateUserTotal($userId);
        });
    }

   
    public static function linkExtraBreakSlots($timesheetId, $userRow)
    {
        DB::transaction(function () use ($timesheetId, $userRow) {
            foreach ($userRow->dayTotal->extraBreakSlots as $breakSlot) {
                if(!$breakSlot->timesheet_id){
                $breakSlot->update([
                    'timesheet_id' => $timesheetId,
                ]);
            }
            }
        });
    }

 
    public static function createTimesheetEntry($userRow, $user)
    {
        return DB::transaction(function () use ($userRow, $user) {
            $date = $userRow->StartWork->format('Y-m-d');
            $dayTotal = UserUtility::findOrCreateUserDayTotal($date, $user->id);
            $overlapCheck = DateUtility::checkIfSameDay($userRow->StartWork, $userRow->StopWork);
            $dayTotal->update([
                'DayOverlap' => !$overlapCheck,
                'NightShift' => (!$overlapCheck || DateUtility::checkNightShift($userRow->StartWork) || DateUtility::checkNightShift($userRow->StopWork)),
            ]);

            return [
                'UserId' => $user->id,
                'daytotal_id' => $dayTotal->id,
                'ClockedIn' => $userRow->StartWork,
                'ClockedOut' => $userRow->StopWork,
                'BreakStart' => $userRow->StartBreak,
                'BreakStop' => $userRow->EndBreak,
                'Month' => $date,
                'userNote' => $userRow->userNote ?? null,
            ];
        });
    }


    public static function updateOrInsertTimesheet(array $newEntry, $oldLog)
    {
        return DB::transaction(function () use ($newEntry, $oldLog) {
            if ($oldLog) {
                $oldLog->update($newEntry);
                return $oldLog;
            }
            return Timesheet::create($newEntry);
        });
    }

  
    public static function updateDailySummery($userId, $day)
    {
        DB::transaction(function () use ($userId, $day) {
            $user = User::find($userId);
            if (!$user) {
                throw new Exception("User $userId not found");
            }

            $companyDayHours = $user->company->day_hours;
            $timesheets = $user->timesheets()->where('Month', $day)->get();
            $dayTotal = UserUtility::userDayTotalFetch($day, $userId);
            $summary = CalculateUtility::calculateSummaryForDay($timesheets, $companyDayHours);
            $dayTotal->update($summary);
        });
    }
}