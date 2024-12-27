<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Usertotal;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;

class UsersheetsController extends Controller
{
    public function myProfile(Request $request)
    {

        $userTimesheet = new Timesheet;
        $userTotal = new Usertotal;
        $currentUser = auth()->user();
        $now = now('Europe/Brussels');

        // $monthString = date('F', strtotime($now));
        $month = date('m', strtotime($now));
        $year = date('Y', strtotime($now));

        if (isset($request->month)) {
            $month = $request->month;
            // $carbonDate = Carbon::create(null, $month, 1);
            // $monthString =  $carbonDate->format('F');
        }
        if (isset($request->worker)) {
            $currentUser = User::find($request->worker);
        }

        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $userTimesheet->where('UserId', '=', $currentUser->id)
            ->whereMonth('Month', '<', $threeMonthsAgo)
            ->delete();

        $userTotal->where('UserId', '=', $currentUser->id)
            ->where('Month', '<', $threeMonthsAgo)
            ->delete();

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
            ->orderBy('Month', 'desc')
            ->whereyear('Month', '=', $year)
            ->get();

        return view('profile', ['user' => $currentUser, 'clockedMonths' => $clockedMonths, 'timesheet' => $timesheet, 'monthlyTotal' => $monthlyTotal]);
    }
}
