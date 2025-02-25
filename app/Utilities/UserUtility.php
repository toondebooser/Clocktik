<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;

class UserUtility
{
    public static function fetchUserTotal($date, $id)
    {
        $newUserTotal = new Usertotal;
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        $userTotal = $newUserTotal
            ->where('UserId', '=', $id)
            ->whereMonth('Month', '=', $date)
            ->whereYear('Month', '=', $date)
            ->firstOrCreate([],[
                'UserId' => $id,
                'Month' => $date,
                'RegularHours' => 0,
                'BreakHours' => 0,
                'OverTime' => 0
            ]);
        // if ($userTotal == null) {
        //     $newUserTotal->create([
        //         'UserId' => $id,
        //         'Month' => $date,
        //         'RegularHours' => 0,
        //         'BreakHours' => 0,
        //         'OverTime' => 0
        //     ]);
        //     $userTotal = $newUserTotal
        //         ->where('UserId', '=', $id)
        //         ->whereMonth('Month', '=', $date)
        //         ->whereYear('Month', '=', $date)
        //         ->first();
        // }
        return $userTotal;
    }


    public static function userTimesheetCheck($date, $id)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $timesheetCheck = Daytotal::where('UserId', $id)
            ->whereDate('Month', $date)
            ->orderBy('created_at', 'asc')
            ->get();
        // $dayTotalCheck = Daytotal::where
        return $timesheetCheck;
    }
}
