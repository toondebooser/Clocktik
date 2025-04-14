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
    public function logOverMultipleDays($userRow, $userId)
    {
        $startWork = Carbon::parse($userRow->StartWork); // e.g., '2025-04-14 22:00:00'
        $stopWork = Carbon::parse($userRow->StopWork);   // e.g., '2025-04-15 02:00:00'
    
        // If same day, fallback to single-day logic
        if ($startWork->isSameDay($stopWork)) {
            return $this->logTimeEntry($userRow, $userId);
        }
    
        $user = User::find($userId);
    
        // First day: StartWork to end of day
        $firstDayEnd = $startWork->copy()->endOfDay(); // '2025-04-14 23:59:59'
        $firstDayRow = (object) [
            'StartWork' => $userRow->StartWork,
            'StopWork' => $firstDayEnd,
            'StartBreak' => $userRow->StartBreak, // Adjust break if needed
            'EndBreak' => $userRow->EndBreak,
            'Weekend' => $userRow->Weekend ?? false,
            'userNote' => $userRow->userNote ?? null,
        ];
    
        $firstDayEntry = $this->createTimeEntry($firstDayRow, $user);
        $this->updateOrInsertTimesheet($firstDayEntry, null);
        $this->updateDailySummery($userId, $firstDayEntry['Month']);
    
        // Second day: Start of day to StopWork
        $secondDayStart = $stopWork->copy()->startOfDay(); // '2025-04-15 00:00:00'
        $secondDayRow = (object) [
            'StartWork' => $secondDayStart,
            'StopWork' => $userRow->StopWork,
            'StartBreak' => null, // Assume no break unless specified
            'EndBreak' => null,
            'Weekend' => $userRow->Weekend ?? false,
            'userNote' => $userRow->userNote ?? null,
        ];
    
        $secondDayEntry = $this->createTimeEntry($secondDayRow, $user);
        $this->updateOrInsertTimesheet($secondDayEntry, null);
        $this->updateDailySummery($userId, $secondDayEntry['Month']);
    
        // Return total for the first day (or adjust as needed)
        return CalculateUtility::calculateUserTotal($firstDayEntry['Month'], $userId);
    }

    public function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $user = User::find($userId);
        $newEntry = $this->createTimeEntry($userRow, $user);
        $this->updateOrInsertTimesheet($newEntry, $oldLog);
        $this->updateDailySummery($newEntry['UserId'], $newEntry['Month']);
        return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }


    private  function createTimeEntry($userRow, $user)
    {

        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        $dayTotal = UserUtility::findOrCreateUserDayTotale($date, $user->id);
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
        // $this->updateDailySummery($newEntry['UserId'], $newEntry['Month']);

        // return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }

    public function updateDailySummery($userId, $day)
    {
        $user = User::find($userId);
        $companyDayHours = $user->company->day_hours;
        $timesheets = $user->timesheets()
            ->where('Month', $day)
            ->get();

        $dayTotal = UserUtility::findOrCreateUserDayTotale($day,$userId);
        $summary = $this->calculateSummaryForDay($timesheets, $companyDayHours);
        $dayTotal->update($summary);
    }


    private function calculateSummaryForDay($timesheets, $companyDayHours)
    {
        $summary = [
            'BreakHours' => 0,
            'RegularHours' => 0,
            'DaytimeCount' => $timesheets->count(),
            'OverTime' => 0,
            'accountableHours' => $companyDayHours
        ];

        $dailyHours = 0;

        foreach ($timesheets as $timesheet) {
            $workHours = CalculateUtility::calculateDecimal($timesheet->ClockedIn, $timesheet->ClockedOut);
            $breakHours = CalculateUtility::calculateDecimal($timesheet->BreakStart, $timesheet->BreakStop);
            $netWorkHours = $workHours - $breakHours;




            $dailyHours += $netWorkHours;

            $summary['BreakHours'] += $breakHours;
            $summary['RegularHours'] += $netWorkHours;
        }

        $summary['OverTime'] += $dailyHours - $companyDayHours;

        return $summary;
    }
}
