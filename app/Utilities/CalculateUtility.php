<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Usertotal;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

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
    public static function calculateUserTotal($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                throw new Exception("User not found: ID $id");
            }

            $userTotals = $user->userTotals()->get();
            foreach ($userTotals as $monthTotal) {
                $dayTotal = Daytotal::where('UserId', $id)
                    ->whereMonth('Month', DateUtility::carbonParse($monthTotal->Month))
                    ->whereYear('Month', DateUtility::carbonParse($monthTotal->Month))
                    ->get();
                $monthTotal->update([
                    'RegularHours' => $dayTotal->sum('accountableHours'),
                    'BreakHours' => $dayTotal->sum('BreakHours'),
                    'OverTime' => $dayTotal->sum('OverTime')
                ]);
            }

            return true; 
        } catch (Exception $e) {
            Log::error("Error in calculateUserTotal for user ID $id: " . $e->getMessage());
            return ['error' => 'Failed to calculate user totals: ' . $e->getMessage()];
        }
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