<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class startTimeCalculator extends Controller
{
    public function calculateStartTime($end, $workedHours, $breakHours)
{

    $clockedOut = Carbon::parse($end, 'Europe/Brussels');
    $totalSeconds = (floatval($workedHours) + floatval($breakHours)) * 3600;
    $clockedIn = $clockedOut->subSeconds($totalSeconds);


    return Carbon::parse($clockedIn, 'Europe/Brussels');

}
}
