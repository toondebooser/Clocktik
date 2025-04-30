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
        static::updateOrInsertTimesheet($newEntry, $oldLog);
        static::updateDailySummery($userId, $newEntry['Month']);
        return CalculateUtility::calculateUserTotal($userId);
    }

   

    public static function createTimesheetEntry($userRow, $user)
    {
        if (!$user instanceof User) {
            Log::error('Invalid user in createTimesheetEntry', ['user' => $user]);
            throw new Exception('Expected a single User instance, received: ' . gettype($user));
        }
        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        $dayTotal = UserUtility::findOrCreateUserDayTotal($date, $user->id);
        $dayTotal->update([
            "DayOverlap" => !DateUtility::checkIfSameDay($userRow->StartWork,$userRow->StopWork),
            "NightShift" => ( !DateUtility::checkIfSameDay($userRow->StartWork,$userRow->StopWork) || DateUtility::checkNightShift($userRow->StartWork) || DateUtility::checkNightShift($userRow->StopWork) ) ? true : false,
        ]);
            return [
            'UserId' => $user->id,
            'daytotal_id' => $dayTotal->id,
            'ClockedIn' => $userRow->StartWork,
            'ClockedOut' => $userRow->StopWork,
            'BreakStart' => $userRow->StartBreak,
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
            Timesheet::create($newEntry);
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
