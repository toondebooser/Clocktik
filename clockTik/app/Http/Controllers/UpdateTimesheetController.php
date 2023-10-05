<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\TimesheetController;

class UpdateTimesheetController extends Controller
{
    public function updateForm($id, $timesheet)
    {
        
        $worker = User::find($id);
        $timesheet = Timesheet::find($timesheet);
        if($timesheet->type !== 'workday') 
        {
            $postData = [
                'worker' => $id,
            ];
        return redirect()->route('getData',$postData)->with('error', 'Dit is geen werkdag.');
        }
        return view('updateTimesheet',['worker'=>$worker, 'timesheet'=>$timesheet]);
    }

    public function updateTimesheet (Request $request)
    {
        $timesheetController = new TimesheetController();
        
        // $clockedTime = $timesheetController->calculateClockedHours($timesheet->ClockedIn,$timesheet->ClockedOut);
        
    }
}
