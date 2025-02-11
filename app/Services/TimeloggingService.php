<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Timesheet;
class TimeloggingService
{
    public function logTimeEntry($userRow, $id, $timestamp)
    {
        $newEntry = $this->createTimeEntry($userRow, $id, $timestamp);
        return $this->updateOrInsertTimesheet($newEntry);
    }

    /**
     * Create a new time entry without summary fields
     */
    private function createTimeEntry($userRow, $id, $timestamp)
    {
        $date = Carbon::parse($userRow->StartWork)->format('Y-m-d');

        return [
            'UserId' => $id,
            'CLockedIn' => $userRow->StartWork,
            'ClockedOut' => $userRow->StopWork,
            'BreakStart' => $userRow->StartBreak,
            'BreakStop' => $userRow->EndBreak,
            // 'BreakHours' => $userRow->BreakHours,
            // 'RegularHours' => $userRow->RegularHours,
            // 'OverTime' => $userRow->RegularHours - 7.6,
            'Weekend' => $userRow->Weekend ?? false,
            'Month' => $date,
            'userNote' => $userRow->userNote ?? null,
        ];
    }

    /**
     * Update or insert a timesheet entry, including updating summary fields
     */
    private function updateOrInsertTimesheet(array $newEntry)
    {
        // Fetch existing timesheet for the day or prepare for a new one
        $existingTimesheet = Timesheet::where('UserId', $newEntry['UserId'])
                                      ->where('date', $newEntry['date'])
                                      ->first();

        if ($existingTimesheet) {
            // Update existing timesheet
            $updatedSummary = $this->updateSummaryFields($existingTimesheet, $newEntry);
            // $newEntry['id'] = $existingTimesheet->id; // For update operation
            
            return Timesheet::updateOrCreate(['id' => $existingTimesheet->id], array_merge($newEntry, $updatedSummary));
        } else {

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
        return [
            'total_daily_hours' => $existing->total_daily_hours + ($newEntry['RegularHours'] + $newEntry['OverTime']),
            'daily_log_count' => $existing->daily_log_count + 1,
            'accountable_hours' => $existing->accountable_hours + ($newEntry['Weekend'] ? 0 : 7.6),
        ];
    }

    /**
     * Calculate summary for a new day's first entry
     */
    private function calculateSummaryForNew(array $entries, $type)
    {
        return collect($entries)->reduce(function ($carry, $entry) {
            $carry['total_daily_hours'] = ($carry['total_daily_hours'] ?? 0) + ($entry['RegularHours'] + $entry['OverTime']);
            $carry['daily_log_count'] = ($carry['daily_log_count'] ?? 0) + 1;
            $carry['accountable_hours'] = ($carry['accountable_hours'] ?? 0) + ($entry['Weekend'] ? 0 : 7.6);
            return $carry;
        }, []);
    }
}
