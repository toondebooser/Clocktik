<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
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
            $dayTotal = Daytotal::where('UserId', $userId)
            ->whereMonth('Month', $date)
            ->whereYear('Month', $date);
            $userTotal->update([
                'RegularHours' => $dayTotal->sum('accountableHours'),
                'BreakHours' => $dayTotal->sum('BreakHours'),
                'OverTime' => $dayTotal->sum('OverTime')
            ]);
        }

        return $userTotal;
    }
 
    public static function calculateSummaryForDay($timesheets, $companyDayHours)
    {
        $summary = [
            'BreakHours' => 0,
            'RegularHours' => 0,
            'DaytimeCount' => $timesheets->count(),
            'OverTime' => 0,
            'accountableHours' => 0,
            'Weekend' => false,
            'Completed' => true,
        ];

        $dailyHours = 0;
        $isWeekendDay = $timesheets->isNotEmpty() && DateUtility::checkWeekend(
            $timesheets->first()->ClockedIn,
            User::find($timesheets->first()->UserId)->company
        );
        $summary['Weekend'] = $isWeekendDay;

        foreach ($timesheets as $timesheet) {
            $workHours = CalculateUtility::calculateDecimal($timesheet->ClockedIn, $timesheet->ClockedOut);
            $breakHours = CalculateUtility::calculateDecimal($timesheet->BreakStart, $timesheet->BreakStop);
            $netWorkHours = $workHours - $breakHours;

            if($isWeekendDay){
                $summary['OverTime'] += $netWorkHours;
                $summary ['BreakHours'] += $breakHours;
            }else{
                $dailyHours += $netWorkHours;
                $summary['BreakHours'] += $breakHours;
                $summary['RegularHours'] += $netWorkHours;

            }
        }
        if(!$isWeekendDay) {
            $summary['OverTime'] += $dailyHours - $companyDayHours;
            $summary['accountableHours'] = $companyDayHours;
        };
        
        return $summary;
    }

}