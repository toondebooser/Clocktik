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

    public function logTimeEntry($userRow, $userId, $oldLog = null)
    {
        $user = User::find($userId);
        $newEntry = $this->createTimeEntry($userRow, $user);
        return $this->updateOrInsertTimesheet($newEntry, $oldLog);
    }


    private  function createTimeEntry($userRow, $user)
    {

        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        $dayTotal = Daytotal::firstOrCreate(['Month' => $date, 'UserId' => $user->id], [
            'UserId' => $user->id,
            'Month' => $date,
        ]);
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


    private  function updateOrInsertTimesheet(array $newEntry, $oldLog = null)
    {
        if ($oldLog) {
            $oldLog->update($newEntry);
        } else {
            Timesheet::create($newEntry);
        }
        $this->updateDailySummery($newEntry['UserId'], $newEntry['Month']);

        return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }

    public function updateDailySummery($userId, $day)
    {
        $user = User::find($userId);
        $companyDayHours = $user->company->day_hours;
        $timesheets = $user->timesheets()
            ->where('Month', $day)
            ->get();

        $dayTotal = $user->dayTotals()
            ->where('Month', $day)
            ->first();
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
