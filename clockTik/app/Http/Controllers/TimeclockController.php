<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\Timesheet;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TimeclockController extends Controller
{
    public function startWorking(Request $request)
    {
        $currentUser = auth()->user();
        $userTimesheet = new Timesheet;
        $userRow = Timelog::find($currentUser->id);
        $timestamp = now('Europe/Brussels');
        $day = date('d', strtotime($timestamp));
        $month = date('m', strtotime($timestamp));
        $year = date('Y', strtotime($timestamp));


        $dayCheck = $userTimesheet
            ->where('UserId', '=', $currentUser->id)
            ->whereDay('Month', '=', $day)
            ->whereMonth('Month', '=', $month)
            ->whereYear('Month', '=', $year)
            ->first();

        if ($dayCheck !== null) {
            $userRow->StartWork = $dayCheck->ClockedIn;
           
            $dayCheck->delete();
        } else {
            $userRow->StartWork = $timestamp;
        }

        $weekDay = Carbon::parse($timestamp)->weekday();
        $weekDay === 0 || $weekDay === 6 ? $userRow->Weekend = true : $userRow->Weekend = false;

        $userRow->StartBreak = null;
        $userRow->EndBreak = null;
        $userRow->StopWork = null;
        $userRow->ShiftStatus = true;
        $userRow->save();
        return redirect('/dashboard');
    }
    public function break()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = true;
        $userRow->StartBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stopBreak()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->BreakStatus = false;

        $userRow->EndBreak = $timeStamp;
        $userRow->save();
        return redirect('/dashboard');
    }

    public function stop()
    {
        $timeStamp = now('Europe/Brussels');
        $userRow = Timelog::find(auth()->user()->id);
        $userRow->ShiftStatus = false;
        if ($userRow->BreakStatus == true) {
            $userRow->BreakStatus = false;
            $userRow->EndBreak = $timeStamp;
            $userRow->save();
        }
        $userRow->StopWork = $timeStamp;
        $userRow->save();
        return Redirect::route('makeTimesheet',['id' => auth()->user()->id]);
    }
}
