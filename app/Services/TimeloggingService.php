<?php

namespace App\Services;

use App\Http\Controllers\TimesheetController;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Timesheet;

class TimeloggingService
{
     
    public function logTimeEntry($userRow, $id, $timesheet)
    {

        $newEntry = $this->createTimeEntry($userRow, $id);
        return $this->updateOrInsertTimesheet($newEntry, $timesheet);
    }

    /**
     * Create a new time entry without summary fields
     */
    private function createTimeEntry($userRow, $id)
    {
        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');

        return [
            'UserId' => $id,
            'CLockedIn' => $userRow->StartWork,
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
    private function updateSummaryFields(Timesheet $existing, array $newEntry)
    {
        $timesheetController = new TimesheetController;
        $newBreakHours =  $timesheetController->calculateDecimal($newEntry['BreakStart'], $newEntry['BreakStop']);
        $breakHours = $existing->BreakHours + $newBreakHours;
        $newRegularHours = $timesheetController->calculateDecimal($newEntry['ClockedIn'], $newEntry['ClockedOut']) - $newBreakHours;

        return [
            'BreakHours' => $breakHours,
            'RegularHours' => $existing->RegularHours + $newRegularHours,
            'DaytimeCount' => $existing->DaytimeCount + $newEntry('DaytimeCount'),
            'OverTime' => 
        ];
    }

    /**
     * Calculate summary for a new day's first entry
     */
    private function calculateSummaryForNew(array $entries)
    {
        $timesheetController = new TimesheetController;
        return [
            'BreakHours'=> $timesheetController->calculateDecimal($entries['BreakStart'], $entries['BreakStop']),
            'RegularHours'=>
        ];
    }
}
