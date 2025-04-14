<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\JsonController;
use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use App\Utilities\UserUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{


    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $userRow = $currentUser->timelogs;
        $now = now('Europe/Brussels');

        // dd(Carbon::parse($currentUser->company->weekend_day_1));
        UserUtility::findOrCreateUserDayTotale($now, $currentUser->id);
        // Carbon::parse($userRow->StartWork)->format('Y-m-d');
        //TODO: rewrite start logic when a user has already logged this day
        // $dayCheck = Timesheet::where('UserId', $currentUser->id)
        // ->where('Month', '=', $now->format('Y-m-d'))
        // ->first();
        // if(!$dayCheck) {
        //     $userRow->fill([
        //         'BreakHours' => 0,
        //         'BreaksTaken' => 0,
        //         'RegularHours' => 0,
        //     ]);
        // }

        $weekDay = Carbon::parse($now)->weekday();
        //TODO Check with stored weekend day company
        $weekDay == 0 || $weekDay == 6 ? $userRow->Weekend = true : $userRow->Weekend = false;
        //TODO: check if userRow exists
        $userRow->fill([
            'StartWork' => $now,
            'StartBreak' => null,
            'EndBreak' => null,
            'StopWork' => null,
            'userNote' => null,
            'ShiftStatus' => true
        ]);




        $userRow->save();

        return redirect('/dashboard');
    }


    public function break()
    {

        $now = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $dayTotal = UserUtility::findOrCreateUserDayTotale($now, auth()->user()->id);
        $dayTotal->RegularHours += CalculateUtility::calculateDecimal($userRow->EndBreak ? $userRow->EndBreak : $userRow->StartWork, $now);
        $userRow->BreakStatus = true;
        $userRow->fill([
            'StartBreak' => $now,
            'BreaksTaken' => $userRow->BreaksTaken += 1
        ]);
        $userRow->save();
        $dayTotal->save();
        return redirect()->back();
    }

    public function stopBreak()
    {
        $now = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $dayTotal = UserUtility::findOrCreateUserDayTotale($now, auth()->user()->id);
        $userRow->fill([
            'BreakStatus' => false,
            'EndBreak' => $now,
            // 'BreakHours' => $userRow->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $timeStamp)
        ]);
        $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now);
        $userRow->save();
        $dayTotal->save();
        return redirect()->back();
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $now = now('Europe/Brussels');
        
        $dayTotal = UserUtility::findOrCreateUserDayTotale($now, auth()->user()->id);
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $start = Carbon::parse($userRow->StartBreak, 'Europe/Brussels');
            $end = Carbon::parse($now, 'Europe/Brussels');
            $dayTotal->BreakHours  += CalculateUtility::calculateDecimal($start, $end);
            $dayTotal->save();
            $userRow->fill([
                'BreakStatus' => false,
                'EndBreak' => $now,
                // 'BreakHours' => $userRow->BreakHours += CalculateUtility::calculateDecimal($start, $end)
            ]);
        }

        $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
            $userRow->EndBreak ?? $userRow->StartWork,
            $now
        );
        $userRow->fill([
            'StopWork' => $now,

        ]);
        $dayTotal->save();
        $userRow->save();
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
