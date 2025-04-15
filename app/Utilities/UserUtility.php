<?php

namespace App\Utilities;

use App\Models\Company;
use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\User;
use App\Models\Usertotal;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class UserUtility
{
    public static function CheckUserMonthTotal($date, $id)
    {
        try {

            if (is_string($date)) {
                $date = Carbon::parse($date);
            }

            $userTotal = Usertotal::where('UserId', $id)
                ->whereMonth('Month', $date->month)
                ->whereYear('Month', $date->year)
                ->firstOrCreate(
                    ['UserId' => $id, 'Month' => $date->startOfMonth()],
                    [
                        'RegularHours' => 0,
                        'BreakHours' => 0,
                        'OverTime' => 0
                    ]
                );

            return $userTotal; // Return the Usertotal record
        } catch (Exception $e) {
            Log::error("Error in CheckUserMonthTotal for user ID $id: " . $e->getMessage());
            return ['error' => 'Failed to check user month total: ' . $e->getMessage()];
        }
    }

    public static function findOrCreateUserDayTotal($date, $id)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        $dayTotal = Daytotal::firstOrCreate(['Month' => $date, 'UserId' => $id], [
            'UserId' => $id,
            'Month' => $date,
        ]);
        return $dayTotal;
    }

    public static function userDayTotalCheck($date, $id)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return Daytotal::where('UserId', $id)
            ->whereDate('Month', $date)
            ->exists();
    }
    public static function userDayTotalFetch($date, $id)
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }

        return Daytotal::where('UserId', $id)
            ->whereDate('Month', $date)
            ->first();
    }


    public static function updateAllUsersDayTotals($company_code)
    {
        try {
            $users = User::with(['dayTotals', 'timesheets'])->where('company_code', $company_code)->get();

            foreach ($users as $user) {
                foreach ($user->dayTotals as $dayTotal) {
                    if ($dayTotal->type !== 'workday') {
                        $dayTotal->update([
                            'accountableHours' => $user->company->day_hours,
                        ]);
                        if (DateUtility::checkWeekend($dayTotal->Month, $user->company)) {
                            dd('We should delete');
                        }
                    } else {
                        // Filter timesheets for the specific day of the Daytotal
                        $dayTimesheets = $user->timesheets->filter(function ($timesheet) use ($dayTotal) {
                            return Carbon::parse($timesheet->ClockedIn)->startOfDay()->eq(
                                Carbon::parse($dayTotal->Month)->startOfDay()
                            );
                        });
                        
                        $summary = CalculateUtility::calculateSummaryForDay($dayTimesheets, $user->company->day_hours);
                        $dayTotal->update($summary);
                    }
                }

                // Recalculate monthly totals
                $result = CalculateUtility::calculateUserTotal($user->id);
                if (is_array($result) && isset($result['error'])) {
                    throw new Exception($result['error']);
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error("Error in updateAllUsersDayTotals for company $company_code: " . $e->getMessage());
            return ['error' => 'Failed to update day totals: ' . $e->getMessage()];
        }
    }
    public static function companyNumberGenerator()
    {

        do {
            $randomNumber = mt_rand(1000000000, 9999999999);
            $companyCheck = Company::where('company_code', $randomNumber)->exists();
        } while ($companyCheck);

        return $randomNumber;
    }
}
