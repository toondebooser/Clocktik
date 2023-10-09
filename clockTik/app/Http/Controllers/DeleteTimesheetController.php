<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet (Request $request){
        $timesheetController = new TimesheetController;
        $timesheet = Timesheet::find($request->deleteSheet);
        $date = $request->date;
        $id = $request->workerId;
        $delete = $timesheet->delete();
        if($delete == true) $total = $timesheetController->calculateUserTotal($date, $id);      
        if($total == true)
        {
            
                $postData = [
                    'worker' => $id,
                ];

                return redirect()->route('getData', $postData);
            
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
