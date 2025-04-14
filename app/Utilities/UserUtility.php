<?php

namespace App\Utilities;

use App\Models\Company;
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
            ->firstOrCreate([], [
                'UserId' => $id,
                'Month' => $date,
                'RegularHours' => 0,
                'BreakHours' => 0,
                'OverTime' => 0
            ]);

        return $userTotal;
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



    public static function companyNumberGenerator()
    {

        do {
            $randomNumber = mt_rand(1000000000, 9999999999);
            $companyCheck = Company::where('company_code', $randomNumber)->exists();
        } while ($companyCheck);
    
        return $randomNumber;

    }
}
