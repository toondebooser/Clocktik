<?php

namespace App\Utilities;

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
    public static function fetchUserTotal($date, $id)
    {
        $newUserTotal = new Usertotal;
        if (is_string($date)) {
            $date = Carbon::parse($date);
        } else {
            $date = $date;
        }
        $userId = $id;
        $userTotal = $newUserTotal
            ->where('UserID', '=', $userId)
            ->whereMonth('Month', '=', $date)
            ->whereYear('Month', '=', $date)
            ->first();
        if ($userTotal == null) {
            $newUserTotal->UserId = $userId;
            $newUserTotal->Month = $date;
            $newUserTotal->RegularHours = 0;
            $newUserTotal->BreakHours = 0;
            $newUserTotal->OverTime = 0;
            $newUserTotal->save();
            $userTotal = $newUserTotal
                ->where('UserID', '=', $userId)
                ->whereMonth('Month', '=', $date)
                ->whereYear('Month', '=', $date)
                ->first();
        }
        return $userTotal;
    }
    public function calculateUserTotal($date, $id)
    {
        $userTotal = $this->fetchUserTotal($date, $id);
        is_string($date) ? $date = Carbon::parse($date) : null;
        $userId = $id;
        if ($userTotal != null) {
            $userTotal->RegularHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('accountableHours');
            $userTotal->BreakHours = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('BreakHours');
            $userTotal->OverTime = Timesheet::where('UserId', $userId)->whereMonth('Month', '=', $date)->whereYear('Month', '=', $date)->sum('OverTime');
        }

        return $userTotal->save();
    }
   

}