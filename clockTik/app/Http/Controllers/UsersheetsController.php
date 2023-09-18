<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\Usertotal;
use Illuminate\Http\Request;

class UsersheetsController extends Controller
{
    public function myProfile(Request $request)
    {
        $userTimesheet = new Timesheet;
        $userTotal = new Usertotal;
        $currentUser = auth()->user();
        $now = now('Europe/Brussels');

        //posted data
        $targetDate = $request->month . $request->year;
        // current month data.
        $monthString = date('F', strtotime($now));
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        if (isset($request->month)) {
            $month = $request->month;
        }


        $timesheet = $userTimesheet
            ->where('UserId', '=', $currentUser->id)
            ->whereMonth('Month', '=', $month)
            ->whereYear('Month', '=', $year)
            ->get();

        $monthlyTotal = $userTotal
            ->where('UserId', '=', $currentUser->id)
            ->whereMonth('Month', '=', $month)
            ->whereYear('Month', '=', $year)
            ->get();

        $clockedMonths = $userTotal->select($userTotal->raw('DISTINCT MONTH(Month) AS month'))
            ->where('UserId', '=', $currentUser->id)
            ->whereyear('Month', '=', $year)
            ->get();


        $clockedYears = $userTotal->select($userTotal->raw('DISTINCT YEAR(Month) AS year'))
            ->where('UserId', '=', $currentUser->id)
            ->get();


        return view('profile', ['targetDate' => $targetDate, 'clockedMonths' => $clockedMonths, 'clockedYears' => $clockedYears, 'timesheet' => $timesheet, 'monthString' => $monthString, 'monthlyTotal' => $monthlyTotal]);
    }
}
