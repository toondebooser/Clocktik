<?php

namespace App\Utilities;

use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use Carbon\Carbon;

class TimeloggingUtility
{
   
    public function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $newEntry = $this->createTimeEntry($userRow, $userId);
        return $this->updateOrInsertTimesheet($newEntry, $oldLog);
    }

  
    private function createTimeEntry($userRow, $userId)
    {
        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        return [
            'UserId' => $userId,
            'ClockedIn' => $userRow->StartWork,
            'ClockedOut' => $userRow->StopWork,
            'BreakStart' => $userRow->StartBreak,
            'BreakStop' => $userRow->EndBreak,
            'Weekend' => $userRow->Weekend ?? false,
            'Month' => $date,
            'userNote' => $userRow->userNote ?? null,
        ];
    }

 
    private function updateOrInsertTimesheet(array $newEntry, $oldLog = null)
    {
        if ($oldLog) {
            // Update an existing timesheet
            $oldLog->update($newEntry);
        } else {
            // Create a new timesheet
            Timesheet::create($newEntry);
        }

        $this->updateMonthlySummary($newEntry['UserId'], $newEntry['Month']);
        
        return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }

    /**
     * Updates the summary fields for the first timesheet entry of the month.
     *
     * @param int $userId
     * @param string $month
     */
    private function updateMonthlySummary($userId, $month)
    {
        $timesheets = Timesheet::where('UserId', $userId)
            ->where('Month', $month)
            ->get();

        if ($timesheets->count() > 0) {
            $summary = $this->calculateSummaryForMonth($timesheets);
            $firstTimesheet = $timesheets->first();
            $firstTimesheet->update($summary);
        }
    }


    private function calculateSummaryForMonth($timesheets)
    {
        $summary = [
            'BreakHours' => 0,
            'RegularHours' => 0,
            'DaytimeCount' => $timesheets->count(), // Count of timesheets/logins for the month
            'OverTime' => 0,
            'accountableHours' => 7.6 * count(array_unique($timesheets->pluck('ClockedIn')->map(function($date) {
                return Carbon::parse($date)->format('Y-m-d');
            })->toArray())), // Only count unique days
        ];

        $dailyHours = [];

        foreach ($timesheets as $timesheet) {
            $date = Carbon::parse($timesheet->ClockedIn)->format('Y-m-d');
            $workHours = CalculateUtility::calculateDecimal($timesheet->ClockedIn, $timesheet->ClockedOut);
            $breakHours = CalculateUtility::calculateDecimal($timesheet->BreakStart, $timesheet->BreakStop);
            $netWorkHours = $workHours - $breakHours;

            if (!isset($dailyHours[$date])) {
                $dailyHours[$date] = 0;
            }

            $dailyHours[$date] += $netWorkHours;

            // Update summary
            $summary['BreakHours'] += $breakHours;
            $summary['RegularHours'] += $netWorkHours;
        }

        // Calculate overtime for each day
        foreach ($dailyHours as $date => $hours) {
            if ($hours > 7.6) {
                $summary['OverTime'] += $hours - 7.6;
            }
        }

        return $summary;
    }
}