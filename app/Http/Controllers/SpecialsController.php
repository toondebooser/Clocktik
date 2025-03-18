<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SpecialsController extends Controller
{
    // public function forWorker()
    // {
    //     $workers = User::with('timelogs')->get();
    //     $setForTimesheet = false;
    //     return view('my-workers', ['workers' => $workers, 'setForTimesheet' => $setForTimesheet]);
    // }

    public function specials(Request $request)
    {   
        $workerInput = $request->input('worker');
        $workersArray = json_decode($workerInput, true);
             
        if (is_array($workersArray) && count($workersArray) > 1) {
            $forWho = 'iedereen';
        } else {
            $worker = User::find($workerInput);
            $forWho = $worker->name;
        }


    
        return view('specials', ['forWho' => $forWho,  'worker' => $workerInput]);
    }
}
