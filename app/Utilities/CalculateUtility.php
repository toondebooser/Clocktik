<?php

namespace App\Utilities;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Usertotal;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
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

        return DB::transaction(function () use ($id) {
            $user = User::find($id);
            if (!$user) {

                throw new Exception("Arbeider niet gevonden: ID $id");
            }

            $userTotals = $user->userTotals()->get();
            foreach ($userTotals as $monthTotal) {
                $dayTotals = Daytotal::where('UserId', $id)
                    ->whereMonth('Month', Carbon::parse($monthTotal->Month)->month)
                    ->whereYear('Month', Carbon::parse($monthTotal->Month)->year)
                    ->get();

                $monthTotal->update([
                    'RegularHours' => $dayTotals->sum('accountableHours'),
                    'BreakHours' => $dayTotals->sum('BreakHours'),
                    'OverTime' => $dayTotals->sum('OverTime'),
                ]);
            }

            return true;
        });
    }

    public static function calculateSummaryForDay($timesheets, $companyDayHours)
    {
        $summary = [
            'BreakHours' => 0,
            'RegularHours' => 0,
            'OverTime' => 0,
            'accountableHours' => 0,
            'BreaksTaken' => 0,
            'Weekend' => false,
            'Completed' => true,
        ];

        $dailyHours = 0;
        $breaksTaken = 0;
        $isWeekendDay = $timesheets->isNotEmpty() && DateUtility::checkWeekend(
            $timesheets->first()->ClockedIn,
            User::find($timesheets->first()->UserId)->company->company_code
        );
        $summary['Weekend'] = $isWeekendDay;

        foreach ($timesheets as $timesheet) {
            $workHours = CalculateUtility::calculateDecimal($timesheet->ClockedIn, $timesheet->ClockedOut);
            $breakHours = CalculateUtility::calculateDecimal($timesheet->BreakStart, $timesheet->BreakStop);
            $breaksTaken += $timesheet->BreakStart && $timesheet->BreakStop !== null ? 1 : 0;
            if ($timesheet->extraBreakSlots->isNotEmpty()) {
                $breaksTaken += $timesheet->extraBreakSlots->count();
                foreach ($timesheet->extraBreakSlots as $breakSlot) {
                    $breakHours += CalculateUtility::calculateDecimal($breakSlot->BreakStart, $breakSlot->BreakStop);
                }
            }
            $summary['BreaksTaken'] = $breaksTaken;
            $netWorkHours = $workHours - $breakHours;
            $timesheet->userNote ? $summary['UserNote'] = true : null;
            if ($isWeekendDay) {
                $summary['OverTime'] += $netWorkHours;
                $summary['BreakHours'] += $breakHours;
            } else {
                $dailyHours += $netWorkHours;
                $summary['BreakHours'] += $breakHours;
                $summary['RegularHours'] += $netWorkHours;
            }
        }
        if (!$isWeekendDay) {
            $summary['OverTime'] += $dailyHours - $companyDayHours;
            $summary['accountableHours'] = $companyDayHours;
        };

        return $summary;
    }
}
