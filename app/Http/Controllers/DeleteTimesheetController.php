<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Utilities\CalculateUtility;
use Illuminate\Http\Request;

class DeleteTimesheetController extends Controller
{
    public function deleteTimesheet (Request $request){
        $timesheet = Timesheet::find($request->deleteSheet);
        $date = $request->date;
        $id = $request->workerId;
        $delete = $timesheet->delete();
        if($delete == true)
        {
            
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
