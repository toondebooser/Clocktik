<?php

namespace App\Services;

use App\Models\Usertotal;
use Carbon\Carbon;

class UserService
{
    public function fetchUserTotal($date, $id)
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
}