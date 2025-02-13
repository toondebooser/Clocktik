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
   
   

}