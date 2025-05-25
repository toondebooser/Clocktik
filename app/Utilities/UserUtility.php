<?php

namespace App\Utilities;

use App\Http\Controllers\TimesheetController;
use App\Models\Company;
use App\Models\Daytotal;
use App\Models\User;
use App\Models\Usertotal;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;

class UserUtility
{
    public static function CheckUserMonthTotal($date, $id)
    {
        try {
            if (is_string($date)) {
                $date = Carbon::parse($date);
            }
            $user = User::find($id);
            $monthStart = $date->copy()->startOfMonth();

            // Search by exact match on Month column (assuming it's a DATE or DATETIME field)
            $userTotal = $user->userTotals()
                ->where('Month', $monthStart)
                ->first();
            // If no record found, create one
            if (!$userTotal) {
                $userTotal = $user->userTotals()->create([
                    'UserId' => $id,
                    'Month' => $monthStart,
                    'RegularHours' => 0,
                    'BreakHours' => 0,
                    'OverTime' => 0
                ]);
            }

            return $userTotal;
        } catch (Exception $e) {
            Log::error("Error in CheckUserMonthTotal for user ID $id: " . $e->getMessage());
            return ['error' => 'Failed to check user month total: ' . $e->getMessage()];
        }
    }

    public static function workersHolidayCheck($company_code, $holidays)
    {
        $now = now('Europe/Brussels');
        $month = $now->month;
        $year = $now->year;


        $unaddedHolidays = array_filter($holidays, function ($holiday) use ($company_code) {
            return !self::hasDayTotalsForHolidays($company_code, $holiday);
        });

        return $unaddedHolidays;
    }
    public static function hasDayTotalsForHolidays($companyCode, $holiday)
    {
        $company = Company::where('company_code', $companyCode)->first();
        $currentMonth = now('Europe/Brussels');
        foreach ($company->users as $user) {
            $name = str_replace('_', ' ', $holiday['name']);

            if ($user->dayTotals()->whereMonth('Month', $currentMonth)->where('type', $name)->where('official_holiday', true)->exists()) {
                return true;
            }
        }

        return false;
    }
    public static function findOrCreateUserDayTotal($date, $id)
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return Daytotal::firstOrCreate(
            ['Month' => Carbon::parse($date)->format('Y-m-d'), 'UserId' => $id],
            [
                'DaytimeCount' => 1,
                'RegularHours' => 0.00,
                'accountableHours' => 0.00,
                'BreaksTaken' => 0,
                'BreakHours' => 0.00,
                'OverTime' => 0.00,
                'type' => 'workday',
                'userNote' => false,
                'Completed' => false,
                'Weekend' => false,
                'NightShift' => false,
                'DayOverlap' => false,
            ]
        );
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
            $errors = new MessageBag();
            foreach ($users as $user) {
                foreach ($user->dayTotals as $dayTotal) {

                    if (!$dayTotal->Completed) {
                        continue;
                    }

                    UserUtility::CheckUserMonthTotal($dayTotal->Month, $user->id);

                    if ($dayTotal->type !== 'workday') {
                        if (DateUtility::checkWeekend($dayTotal->Month, $user->company->company_code)) {
                            $errors->add('weekend_deletion', "Vakantiedag: {$dayTotal->Month->format('Y-m-d')} Verwijderd omdat het in een weekend valt");
                            $dayTotal->delete();
                            continue;
                        } else {
                            $dayTotal->update([
                                'accountableHours' => $user->company->day_hours,
                            ]);
                        }
                    } else {
                        $dayTimesheets = $user->timesheets()->where('Month', $dayTotal->Month)->get();


                        $summary = CalculateUtility::calculateSummaryForDay($dayTimesheets, $user->company->day_hours);

                        $summaryWithFlags = DateUtility::updateDayTotalFlags($dayTimesheets, $summary);
                        $dayTotal->update($summaryWithFlags);
                    }
                }

                $result = CalculateUtility::calculateUserTotal($user->id);
                if (is_array($result) && isset($result['error'])) {
                    throw new Exception($result['error']);
                }
            }
            if ($errors->isNotEmpty()) {
                return redirect()->back()->withErrors($errors);
            } else return true;
        } catch (Exception $e) {
            Log::error("Error in updateAllUsersDayTotals for company $company_code: " . $e->getMessage());
            return ['errors' => 'Failed to update day totals: ' . $e->getMessage()];
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
