<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\User;
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
        // $targetDate = $request->month . $request->year;
        // current month data.
        $monthString = date('F', strtotime($now));
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        if (isset($request->month)) {
        $month = $request->month;
        }
        if(isset($request->worker)){
            $currentUser = User::find($request->worker);
        }


        $timesheet = $userTimesheet
            ->where('UserId', '=', $currentUser->id)
            ->whereMonth('Month', '=', $month)
            ->whereYear('Month', '=', $year)
            ->orderBy('Month', 'asc')
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


        // $clockedYears = $userTotal->select($userTotal->raw('DISTINCT YEAR(Month) AS year'))
        //     ->where('UserId', '=', $currentUser->id)
        //     ->get();

        // $userTotalRegular = Timesheet::where('UserId', '=', $currentUser->id)->sum('RegularHours');

        return view('profile', [ 'user' => $currentUser,'clockedMonths' => $clockedMonths, 'timesheet' => $timesheet, 'monthString' => $monthString, 'monthlyTotal' => $monthlyTotal]);
    }
}
