<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\JsonController;
use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Models\Usertotal;
use App\Utilities\CalculateUtility;
use App\Utilities\DateUtility;
use App\Utilities\TimeloggingUtility;
use App\Utilities\UserUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{


    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $userRow = $currentUser->timelogs;
        $now = now('Europe/Brussels');

        UserUtility::findOrCreateUserDayTotal($now, $currentUser->id);
      

        $weekDay = Carbon::parse($now)->weekday();
        $weekDay == $currentUser->company->weekend_day_1 || $weekDay == $currentUser->company->weekend_day_2 ? $userRow->Weekend = true : $userRow->Weekend = false;
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
        if (!DateUtility::checkDayDiff($userRow->StartWork, $userRow->StopWork)) {
            TimeloggingUtility::CompletePreviousDay($userRow, auth()->user()->id);
            return redirect()->back()->with('error', 'Je hebt ingeklokt op een andere dag');
        }
        $dayTotal =  UserUtility::userDayTotalFetch($now, auth()->user()->id);
        if($dayTotal->BreaksTaken > 0) return  redirect()->back()->with('error', 'Je hebt al pauze genomen vandaag');
        DB::transaction(function () use ($userRow, $dayTotal, $now) {
            $userRow->update([
                'StartBreak' => $now,
                'BreakStatus' => true,
            ]);

            $dayTotal->update([
                'Regularhours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal($userRow->EndBreak ? $userRow->EndBreak : $userRow->StartWork, $now),
            ]);
        });
        return redirect()->back();
    }

    public function stopBreak()
    {
        $now = now('Europe/Brussels');
        $userRow = auth()->user()->timelogs;
        $dayTotal = UserUtility::userDayTotalFetch($now, auth()->user()->id);
        DB::transaction(function () use ($userRow, $dayTotal, $now) {
            $userRow->update([
                'BreakStatus' => false,
                'EndBreak' => $now,
            ]);
            $dayTotal->update([
                'Breakhours' => $dayTotal->BreakHours += CalculateUtility::calculateDecimal($userRow->StartBreak, $now),
                'BreaksTaken' => $dayTotal->BreaksTaken += 1,
            ]);
        });
        return redirect()->back();
    }

    public function stop()
    {
        $userRow = auth()->user()->timelogs;
        $now = now('Europe/Brussels');
        if (!DateUtility::checkDayDiff($userRow->StartWork, $userRow->StopWork)) {
            TimeloggingUtility::CompletePreviousDay($userRow, auth()->user()->id);
            return redirect()->back()->with('error', 'Je hebt ingeklokt op een andere dag');
        }
        $dayTotal = UserUtility::userDayTotalFetch($now, auth()->user()->id);
        DB::transaction(function () use ($userRow, $dayTotal, $now) {
            $userRow->update([
                'ShiftStatus' => false,
                'StopWork' => $now,
            ]);
            if ($userRow->BreakStatus == true) {
                $start = DateUtility::carbonParse($userRow->StartBreak);
                $end = DateUtility::carbonParse($now);
                
                $dayTotal->BreakHours  += CalculateUtility::calculateDecimal($start, $end);
                $dayTotal->save();
                $userRow->update([
                    'BreakStatus' => false,
                    'EndBreak' => $now,
                    // 'BreakHours' => $userRow->BreakHours += CalculateUtility::calculateDecimal($start, $end)
                ]);
            }
            $dayTotal->update([
                'RegularHours' => $dayTotal->RegularHours += CalculateUtility::calculateDecimal(
                    $userRow->EndBreak ?? $userRow->StartWork,
                    $now),
                
            ]);
        });
        return Redirect::route('makeTimesheet', ['id' => auth()->user()->id]);
    }
}
