<?php

namespace App\Http\Controllers;

use App\Models\Daytotal;
use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use App\Utilities\TimeloggingUtility;
use Illuminate\Http\Request;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet (Request $request){
        $timesheet = Timesheet::find($request->deleteSheet);
        $timeloggingUtility = new TimeloggingUtility;
        $date = $request->date;
        $id = $request->workerId;
        $delete = $timesheet->delete();
        if($delete == true)
        {
            $dayTotal = DayTotal::where('Month', $date)->first();
            if(count($dayTotal->timesheets) == 0)  {
                $dayTotal->delete();
                CalculateUtility::calculateUserTotal($date, $id);
                return redirect('/my-workers');
            };
            $timeloggingUtility->updateDailySummery($id, $date);
            CalculateUtility::calculateUserTotal($date, $id);
            
            return redirect('/my-workers');
            
        }
        else 
            {
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData)->with('error', 'Er ging iets mis, kijk even na Of de dag verwijderd is.');
            }
    
    }
}
