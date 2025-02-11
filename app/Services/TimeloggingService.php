<?php

namespace App\Services;

use App\Http\Controllers\TimesheetController;
use Carbon\Carbon;
use App\Models\Timesheet;
use Dotenv\Parser\Entry;

class TimeloggingService
{
    public function calculateDecimal($start, $end)
    {
        $start = $start ? Carbon::parse($start, 'Europe/Brussels') : null;
        $end = $end ? Carbon::parse($end, 'Europe/Brussels') : null;
        if ($start === null) {
            return 0;
        }


        $diffInMin = $end->diffInMinutes($start);
        $decimalTime = round($diffInMin / 60, 2);

        return $decimalTime;
    }
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
        
        // Fetch existing timesheet for the day or prepare for a new one
        $existingTimesheet = Timesheet::where('UserId', $newEntry['UserId'])
                                      ->where('Month', $newEntry['Month'])
                                      ->orderBy('ClockedIn', 'asc')
                                      ->first();
        if ($timesheet !== null) $oldTimesheet = Timesheet::find($timesheet);
        if ($existingTimesheet ) {
            // Update existing timesheet
            $updatedSummary = $this->updateSummaryFields($existingTimesheet, $newEntry, $oldTimesheet ?? null);
            // $newEntry['id'] = $existingTimesheet->id; // For update operation
            
            return Timesheet::updateOrCreate(['id' => $existingTimesheet->id], array_merge($newEntry, $updatedSummary));
        } elseif(!$existingTimesheet ) {

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
        $breakHours =  $this->calculateDecimal($newEntry['BreakStart'], $newEntry['BreakStop']);
        $newBreakHours = ($existing->BreakHours - $oldTimesheet ? $oldTimesheet->BreakHours : 0) + $breakHours;
        $regularHours = $this->calculateDecimal($newEntry['ClockedIn'], $newEntry['ClockedOut']) - $newBreakHours;
        $newRegularHours = ($existing->RegularHours - $oldTimesheet ? $oldTimesheet->RegularHours : 0) + $regularHours;
        return [
            'BreakHours' => $newBreakHours,
            'RegularHours' => $newRegularHours,
            'DaytimeCount' => $oldTimesheet ? $oldTimesheet->DaytimeCount : $existing->DaytimeCount + 1,
            'OverTime' => $newRegularHours - 7.6
        ];
    }
    
    /**
     * Calculate summary for a new day's first entry
     */
    private function calculateSummaryForNew(array $entries)
    {
        
       $entry = $entries[0];
        $breakHours = $this->calculateDecimal($entry['BreakStart'], $entry['BreakStop']);
        $regularHours =$this->calculateDecimal($entry['ClockedIn'], $entry['ClockedOut']);
        return [
            'BreakHours'=> $breakHours,
            'RegularHours'=> $regularHours,
            'accountableHours' => 7.6,
            'OverTime' => $regularHours - 7.6,
        ];
    }
}
