<?php

namespace App\Services;

use App\Http\Controllers\TimesheetController;
use Carbon\Carbon;
use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use Dotenv\Parser\Entry;

class TimeloggingService
{
  
    public function logTimeEntry($userRow, $userId, $timesheet)
    {

        $newEntry = $this->createTimeEntry($userRow, $userId);
        return $this->updateOrInsertTimesheet($newEntry, $timesheet);
    }

    /**
     * Create a new time entry without summary fields
     */
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

    /**
     * Update or insert a timesheet entry, including updating summary fields
     */
    private function updateOrInsertTimesheet(array $newEntry, $timesheet)
    {
        
        $existingTimesheet = Timesheet::where('UserId', $newEntry['UserId'])
        ->where('Month', $newEntry['Month'])
        ->orderBy('ClockedIn', 'asc')
        ->first();
        if ($timesheet !== null) $oldTimesheet = $timesheet;
        if ($existingTimesheet) {
            $updatedSummary = $this->updateSummaryFields($existingTimesheet, $newEntry, $oldTimesheet ?? null);
            if($existingTimesheet->Month == $newEntry['Month']){  
                
                $update = Timesheet::where('id', $existingTimesheet->id)->update($updatedSummary);  
                 $create = Timesheet::create($newEntry);
                 return $update && $create ? true : false ;

            }else{
                $updateOrCreate = Timesheet::updateOrCreate(['id' => $existingTimesheet->id], array_merge($newEntry, $updatedSummary));
                return $updateOrCreate;
            }

        } 
        elseif(!$existingTimesheet ) {

            $summaryForNew = $this->calculateSummaryForNew([$newEntry]);
            $timesheet = new Timesheet(array_merge($newEntry, $summaryForNew));
            $timesheet->save();
            return $timesheet;
        } 
    }

    /**
     * Update summary fields for an existing day's timesheet
     */
    private function updateSummaryFields(Timesheet $existing, array $newEntry, $oldTimesheet)
    {
        $breakHours =  CalculateUtility::calculateDecimal($newEntry['BreakStart'], $newEntry['BreakStop']);
        $regularHours = CalculateUtility::calculateDecimal($newEntry['ClockedIn'], $newEntry['ClockedOut']);
        
        $existingBreakHours = $existing->BreakHours ?? 0;
        $oldBreakHours = $oldTimesheet ? ($oldTimesheet->BreakHours ?? 0) : 0;
        $oldRegularHours = $oldTimesheet->RegularHours ?? 0;
        $newBreakHours = max(0, $existingBreakHours - $oldBreakHours + $breakHours);
        $newRegularHours = $existing->RegularHours -  $oldRegularHours  + $regularHours;

        return [
            'BreakHours' => $newBreakHours,
            'RegularHours' => $newRegularHours,
            'DaytimeCount' => $oldTimesheet !== null ? $oldTimesheet->DaytimeCount : $existing->DaytimeCount + 1,
            'OverTime' => $newRegularHours - 7.6
        ];
    }
    
    /**
     * Calculate summary for a new day's first entry
     */
    private function calculateSummaryForNew(array $entries)
    {
        
       $entry = $entries[0];
        $breakHours = CalculateUtility::calculateDecimal($entry['BreakStart'], $entry['BreakStop']);
        $regularHours = CalculateUtility::calculateDecimal($entry['ClockedIn'], $entry['ClockedOut']);
        return [
            'BreakHours'=> $breakHours,
            'RegularHours'=> $regularHours,
            'accountableHours' => 7.6,
            'OverTime' => $regularHours - 7.6,
        ];
    }
}
