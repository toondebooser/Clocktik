<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Daytotal as ModelsDaytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;

class TimeloggingUtility
{
    public static function CompletePreviousDay($userRow, $userId)
    {
        $startWork = DateUtility::carbonParse($userRow->StartWork); // e.g., '2025-04-14 22:00:00'
        $stopWork = DateUtility::carbonParse($userRow->StopWork);   // e.g., '2025-04-15 02:00:00'
        $user = User::find($userId);
        $firstDayRow = (object) [
            'StartWork' => $userRow->StartWork,
            'StopWork' => $startWork->copy()->endOfDay(),
            'StartBreak' => $userRow->StartBreak,
            'EndBreak' => $userRow->EndBreak,
            'Weekend' => $userRow->Weekend ?? false,
            'userNote' => $userRow->userNote ?? null,
        ];
        $firstDayEntry = TimeloggingUtility::createTimesheetEntry($firstDayRow, $user);
        TimeloggingUtility::updateOrInsertTimesheet($firstDayEntry, null);
        TimeloggingUtility::updateDailySummery($userId, $firstDayEntry['Month']);
        return CalculateUtility::calculateUserTotal($firstDayEntry['Month'], $userId);
    }

    public function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $user = User::find($userId);
        $newEntry = $this->createTimesheetEntry($userRow, $user);
        $this->updateOrInsertTimesheet($newEntry, $oldLog);
        $this->updateDailySummery($newEntry['UserId'], $newEntry['Month']);
        return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }


    private  function createTimesheetEntry($userRow, $user)
    {
        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        $dayTotal = UserUtility::findOrCreateUserDayTotal($date, $user->id);
        return [
            'UserId' => $user->id,
            'daytotal_id' => $dayTotal->id,
            'ClockedIn' => $userRow->StartWork,
            'ClockedOut' => $userRow->StopWork,
            'BreakStart' => $userRow->StartBreak,
            'BreakStop' => $userRow->EndBreak,
            'Weekend' => $userRow->Weekend ?? false,
            'Month' => $date,
            'userNote' => $userRow->userNote ?? null,
        ];
    }


    private  function updateOrInsertTimesheet(array $newEntry, $oldLog)
    {
        if ($oldLog != null) {
            $oldLog->update($newEntry);
        } else {
            Timesheet::create($newEntry);
        }
    }

    public function updateDailySummery($userId, $day)
    {
        $user = User::find($userId);
        $companyDayHours = $user->company->day_hours;
        $timesheets = $user->timesheets()
            ->where('Month', $day)
            ->get();
        $dayTotal = UserUtility::userDayTotalFetch($day, $userId);
        $summary = CalculateUtility::calculateSummaryForDay($timesheets, $companyDayHours);
        $dayTotal->update($summary);
    }


   
}
