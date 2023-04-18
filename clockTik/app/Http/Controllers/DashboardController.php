<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function userDashboard()
    {
        $DataBase = Timelog::find(auth()->user()->id);
        $shiftStatus = $DataBase->id;
        $start  = $DataBase->StartWork;
        return view('dashboard', ['user' => auth()->user(), 'start' => $start, 'shiftStatus' => $shiftStatus]);
    }

    public function startWorking(Request $request)
    {
        $timestamp = now();
        $newShift = new Timelog;
        $newShift->StartWork = $timestamp;
        $newShift->UserId = auth()->user()->id;
        $newShift->save();
        // $startTime = now();
        // $userId = auth()->user()->id;
        // $newTimestamp->start = $startTime;
        // $newTimestamp->user_id = $userId;
        // $newTimestamp->save();
        return redirect('/dashboard');
    }
}
