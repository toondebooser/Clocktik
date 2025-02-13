<?php

namespace App\Utilities;

use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;

class UserUtility
{
   
    public static function fetchUserTotal($date, $id)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
    
        $userTotal = Usertotal::firstOrNew([
            'UserID' => $id,
            'Month' => $date->format('Y-m-d') 
        ], [
            'RegularHours' => 0,
            'BreakHours' => 0,
            'OverTime' => 0
        ]);
    
        if (!$userTotal->exists) {
            $userTotal->save();
        }
    
        return $userTotal;
    }
   
    public static function userTimesheetCheck($date, $id)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $timesheetCheck = Timesheet::where('UserId', $id)
            ->whereDate('Month', $date)
            ->orderBy('ClockedIn', 'asc')
            ->get();
        return $timesheetCheck;
    }

}