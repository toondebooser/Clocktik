<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class BuilderController extends Controller
{

    public function timesheetBuilder(object $input, $userId, $type)
    {

        if ($type == 'workday') {
            return [
                'UserId' => $userId,
                'ClockedIn' => $input->StartWork,
                'ClockedOut' => $input->StopWork,
                'BreakStart' => $input->StartBreak,
                'BreakStop' => $input->EndBreak,
                'BreakHours' => $input->BreakHours,
                'RegularHours' => $input->RegularHours > 7.6 ? 7.6 : $input->RegularHours,
                'OverTime' => $input->RegularHours - 7.6,
                'Weekend' => $input->Weekend,
                'userNote' => $input->userNote ?? null,
                'Month' => Carbon::parse($input->StartWork)->format('Y-m-d'),
                'accountableHours' => $input->Weekend ? 0 : 7.6,
            ];
        } elseif ($type == 'specialday') {
            return [
                'UserId' => $userId,
                'ClockedIn' => $input->StartWork,
                'ClockedOut' => $input->StopWork,
                'BreakStart' => $input->StartBreak,
                'BreakStop' => $input->EndBreak,
                'BreakHours' => $input->BreakHours,
                'RegularHours' => $input->RegularHours > 7.6 ? 7.6 : $input->RegularHours,
                'OverTime' => $input->RegularHours - 7.6,
                'Weekend' => $input->Weekend,
                'userNote' => $input->userNote ?? null,
                'Month' => Carbon::parse($input->StartWork)->format('Y-m-d'),
                'accountableHours' => $input->Weekend ? 0 : 7.6,
            ];
        } 
    }
}
