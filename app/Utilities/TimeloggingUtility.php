<?php

namespace App\Utilities;

use App\Http\Controllers\TimesheetController;
use App\Models\ExtraBreakSlot;
use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\User;
use App\Utilities\CalculateUtility;
use Illuminate\Support\Facades\DB;
use Exception;

class TimeloggingUtility
{
  
    public static function logTimeEntry($userRow, $userId, $timesheetExists = null)
    {
        return DB::transaction(function () use ($userRow, $userId, $timesheetExists) {
           

            $newEntry = static::createTimesheetEntry($userRow, $userId);
            static::updateOrInsertTimesheet($newEntry, $timesheetExists);
            UserUtility::CheckUserMonthTotal($newEntry['Month'], $userId);

            static::updateDailySummery($userId, $newEntry['Month']);
            return CalculateUtility::calculateUserTotal($userId);
        });
    }

   
    public static function ExtraBreakSlot($userRow_id )
    {
        DB::transaction(function () use ( $userRow_id) {
           $userRow = Timelog::find($userRow_id);
           $addExtraBreakSlot = ExtraBreakSlot::create([
            'timesheet_id' => $userRow->timesheet_id,
            'Month' => $userRow->StartWork->format('Y-m-d'),
            'UserId' => auth()->user()->id,
            'daytotal_id' => $userRow->daytotal_id,
            'BreakStart' => $userRow->StartBreak,
            'BreakStop' => $userRow->EndBreak
        ]); return $addExtraBreakSlot;
        });
    }

        
    
    public static function createTimesheetEntry($userRow_id, $user)
    {
        return DB::transaction(function () use ($userRow_id, $user) {
            if (is_object($userRow_id)) {
                $userRow = $userRow_id;

            } else {
                $userRow = Timelog::find($userRow_id)->first();
            }  
            $date = $userRow->StartWork->format('Y-m-d');
            $dayTotal = UserUtility::findOrCreateUserDayTotal($date, $user);
            $sameDay = DateUtility::checkIfSameDay($userRow->StartWork, $userRow->StopWork);
            $dayTotal->update([
                'DayOverlap' => !$sameDay,
                'NightShift' => (!$sameDay || DateUtility::checkNightShift($userRow->StartWork) || DateUtility::checkNightShift($userRow->StopWork)),
            ]);

            // Required attributes always included
            $timesheetAttributes = [
                'UserId' => $user,
                'daytotal_id' => $dayTotal->id,
                'ClockedIn' => $userRow->StartWork,
                'ClockedOut' => $userRow->StopWork,
                'userNote' => $userRow->userNote,
                'Month' => $date,
            ];

            
            if (isset($userRow->StartBreak) && $userRow->StartBreak !== null) {
                $timesheetAttributes['BreakStart'] = $userRow->StartBreak;
            }
            if (isset($userRow->EndBreak) && $userRow->EndBreak !== null ) {
                $timesheetAttributes['BreakStop'] = $userRow->EndBreak ;

            }

            return $timesheetAttributes;
        });
    
    }


    public static function updateOrInsertTimesheet(array $newEntry, $timesheetExists)
    {
        return DB::transaction(function () use ($newEntry, $timesheetExists) {
            if ($timesheetExists) {
                $timesheet = Timesheet::find($timesheetExists);
                $timesheet->update($newEntry);
                return $timesheetExists;
            }
            return Timesheet::create($newEntry);
        });
    }

  
    public static function updateDailySummery($userId, $day)
    {
        DB::transaction(function () use ($userId, $day) {
            $user = User::find($userId);
            if (!$user) {
                throw new Exception("User $userId not found");
            }

            $companyDayHours = $user->company->day_hours;
            $timesheets = $user->timesheets()->where('Month', $day)->get();
            $dayTotal = UserUtility::userDayTotalFetch($day, $userId);
            $summary = CalculateUtility::calculateSummaryForDay($timesheets, $companyDayHours);
            $dayTotal->update($summary);
        });
    }
}