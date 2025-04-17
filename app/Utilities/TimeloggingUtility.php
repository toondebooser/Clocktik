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
    // public static function CompletePreviousDay($userRow, $userId)
    // {
    //     $startWork = DateUtility::carbonParse($userRow->StartWork); // e.g., '2025-04-14 22:00:00'
    //     $stopWork = DateUtility::carbonParse($userRow->StopWork);   // e.g., '2025-04-15 02:00:00'
    //     $user = User::find($userId);
    //     if (!$user) {
    //         throw new Exception("User $userId not found");
    //     }

    //     $firstDayRow = (object) [
    //         'StartWork' => $startWork,
    //         'StopWork' => $startWork->copy()->endOfDay(),
    //         'StartBreak' => $userRow->StartBreak,
    //         'EndBreak' => $userRow->EndBreak,
    //         'Weekend' => $userRow->Weekend ?? false,
    //         'userNote' => $userRow->userNote ?? null,
    //     ];

    //     $firstDayEntry = static::createTimesheetEntry($firstDayRow, $user);
    //     $firstDayTotal = UserUtility::findOrCreateUserDayTotal($firstDayEntry['Month'], $userId);
    //     $firstDayTotal->update(['nightshift' => true]);

    //     static::updateOrInsertTimesheet($firstDayEntry, null);
    //     static::updateDailySummery($userId, $firstDayEntry['Month']);
    //     return CalculateUtility::calculateUserTotal($userId);
    // }

    public static function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $user = User::find($userId);
        if (!$user) {
            throw new Exception("User $userId not found");
        }
        // if (!$startWork->isSameDay($stopWork)) {
        //     return static::handleNightShift($userRow, $userId, $oldLog);
        // }

        $newEntry = static::createTimesheetEntry($userRow, $user);
        static::updateOrInsertTimesheet($newEntry, $oldLog);
        static::updateDailySummery($userId, $newEntry['Month']);
        return CalculateUtility::calculateUserTotal($userId);
    }

    // public static function handleNightShift($userRow, $userId, $oldLog)
    // {
    //     $startWork = DateUtility::carbonParse($userRow->StartWork);
    //     $stopWork = DateUtility::carbonParse($userRow->StopWork);
    //     $user = User::find($userId);
    //     if (!$user) {
    //         throw new Exception("User $userId not found");
    //     }

    //     $dayEntries = [
    //         (object) [
    //             'StartWork' => $startWork,
    //             'StopWork' => $startWork->copy()->endOfDay(),
    //             'StartBreak' => $userRow->StartBreak,
    //             'EndBreak' => $userRow->EndBreak,
    //             'Weekend' => $userRow->Weekend ?? false,
    //             'userNote' => $userRow->userNote ?? null,
    //         ],
    //         (object) [
    //             'StartWork' => $stopWork->copy()->startOfDay(),
    //             'StopWork' => $stopWork,
    //             'StartBreak' => null, // Adjust if breaks occur on second day
    //             'EndBreak' => null,
    //             'Weekend' => Carbon::parse($stopWork)->isWeekend(),
    //             'userNote' => $userRow->userNote ?? null,
    //         ],
    //     ];

    //     DB::transaction(function () use ($dayEntries, $user, $oldLog) {
    //         foreach ($dayEntries as $index => $dayRow) {
    //             $entry = static::createTimesheetEntry($dayRow, $user);
    //             $dayTotal = UserUtility::findOrCreateUserDayTotal($entry['Month'], $user->id);
    //             $dayTotal->update(['nightshift' => true]);
    //             static::updateOrInsertTimesheet($entry, $index === 0 ? $oldLog : null);
    //             static::updateDailySummery($user->id, $entry['Month']);
    //         }
    //     });

    //     return CalculateUtility::calculateUserTotal($user->id);
    // }

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
