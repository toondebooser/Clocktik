<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;

class CalculateUtility
{
    public static function calculateDecimal($start, $end)
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
    public static function calculateUserTotal($date, $id)
    {
        $userTotal = UserUtility::fetchUserTotal($date, $id);
        is_string($date) ? $date = Carbon::parse($date) : null;
        $userId = $id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Daytotal::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('accountableHours');
            $userTotal->BreakHours = Daytotal::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('BreakHours');
            $userTotal->OverTime = Daytotal::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('OverTime');
        }

        return $userTotal->save();
    }
   

}