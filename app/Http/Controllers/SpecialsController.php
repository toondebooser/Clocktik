<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SpecialsController extends Controller
{
    public function forWorker()
    {
        $workers = User::with('timelogs')->get();
        $setForTimesheet = false;
        return view('my-workers', ['workers' => $workers, 'setForTimesheet' => $setForTimesheet]);
    }

    public function specials(Request $request)
    {   
        $workerInput = $request->input('worker');
        // if($workerInput == null) $workerInput = $request->old('worker');
        $workersArray = json_decode($workerInput, true);
             
        if (is_array($workersArray) && count($workersArray) > 1) {
            $forWho = 'iedereen';
        } else {
            $worker = User::find($workerInput);
            $forWho = $worker->name;
        }

        $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald_verlof','Betaald_verlof', 'Feestdag', 'Solicitatie_verlof'];

    
        return view('specials', ['forWho' => $forWho, 'specialDays' => $specialDays, 'worker' => $workerInput]);
    }
}
