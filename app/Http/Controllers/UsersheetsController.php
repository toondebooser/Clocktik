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
        // $userTimesheet = new Timesheet;
        $userTotal = new Usertotal;
        $currentUser = auth()->user();
        $now = now('Europe/Brussels');

        $month = date('m', strtotime($now));
        // $year = date('Y', strtotime($now));

        if (isset($request->month)) {
            $month = $request->month;
        }
        if (isset($request->worker)) {
            $currentUser = User::find($request->worker);
        }

        $threeMonthsAgo = Carbon::now()->startOfMonth()->subMonths(3);

         Timesheet::where('UserId', '=', $currentUser->id)
            ->where('Month', '<=', $threeMonthsAgo)
            ->delete();

        Usertotal::where('UserId', '=', $currentUser->id)
            ->where('Month', '<', $threeMonthsAgo)
            ->delete();

        $timesheet = $currentUser->timesheets()
            ->whereMonth('Month', '=', $month)
            ->orderBy('Month', 'asc')
            ->get();

        $days = $currentUser->dayTotals()->whereMonth('Month', $month)->where('Completed', true)->orderBy('Month','asc')->get();
        
        
        $monthlyTotal = $currentUser->userTotals()->where('UserId', '=', $currentUser->id)
            ->whereMonth('Month', $month)
            ->get();
        $clockedMonths = $userTotal->select($userTotal->raw('DISTINCT MONTH(Month) AS month'))
            ->where('UserId', '=', $currentUser->id)
            ->orderBy('Month', 'desc')
            ->get();

        return view('profile', ['user' => $currentUser, 'companyDayHours' => $currentUser->company->day_hours , 'days' => $days,'clockedMonths' => $clockedMonths, 'timesheet' => $timesheet, 'monthlyTotal' => $monthlyTotal]);
    }
}
