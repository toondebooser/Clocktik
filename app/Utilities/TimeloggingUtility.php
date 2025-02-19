<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Daytotal as ModelsDaytotal;
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

  
    private  function createTimeEntry($userRow, $userId)
    {

        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');
        $dayTotal = Daytotal::firstOrCreate(['Month'=>$date, 'UserId'=>$userId],[
            'company_code' => '1234567890',
            'UserId' => $userId,
            'Month' => $date,
        ]);
        return [
            'UserId' => $userId,
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
        //TODO updateorcreate auth()->daytotal->where('Month, $newEntry->Month)
        // if ($oldLog) {
        //     $oldLog->update($newEntry);
        // } else {
        //     Timesheet::create($newEntry);
        // }
        
        Timesheet::updateOrCreate(
            ['Month' => $newEntry['Month'], 'UserId' => $newEntry['UserId']],
            $newEntry
        );

        $this->updateDailySummery($newEntry['UserId'], $newEntry['Month']);
        
        return CalculateUtility::calculateUserTotal($newEntry['Month'], $newEntry['UserId']);
    }

    public function updateDailySummery($userId, $day)
    {
        $timesheets = Timesheet::where('UserId', $userId)
            ->where('Month', $day)
            ->get();

        $dayTotal = Daytotal::where('UserId', $userId)
        ->where('Month', $day)
        ->first();
        if ($timesheets->count() > 0) {
            $summary = $this->calculateSummaryForDay($timesheets); 
            $dayTotal->update($summary);
        }
    }


    private function calculateSummaryForDay($timesheets)
    {
        $summary = [
            'BreakHours' => 0,
            'RegularHours' => 0,
            'DaytimeCount' => $timesheets->count(), // Count of timesheets/logins for the month
            'OverTime' => 0,
            'accountableHours' => 7.6
        ];

        $dailyHours = 0;

        foreach ($timesheets as $timesheet) {
            $workHours = CalculateUtility::calculateDecimal($timesheet->ClockedIn, $timesheet->ClockedOut);
            $breakHours = CalculateUtility::calculateDecimal($timesheet->BreakStart, $timesheet->BreakStop);
            $netWorkHours = $workHours - $breakHours;
            //TODO TEST before proceeding
            // $timesheet->update([
            //     'BreakHours' => $breakHours,
            //     'RegularHours' => $netWorkHours,
            //     'OverTime' => $netWorkHours - 7.6

            // ]);

           

            $dailyHours += $netWorkHours;

            // Update summary
            $summary['BreakHours'] += $breakHours;
            $summary['RegularHours'] += $netWorkHours;
        }

        $summary['OverTime'] += $dailyHours - 7.6;
        // foreach ($dailyHours as $date => $hours) {
        // }

        return $summary;
    }
}