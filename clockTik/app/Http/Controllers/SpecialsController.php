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
        $worker = User::find($request->worker);
        $workersArray = json_decode($request->input('worker'), true);

        if (is_array($workersArray) && count($workersArray) > 1) {
            $forWho = 'iedereen';
        } else {
            $forWho = $worker->name;
        }

        $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald verlof','Betaald verlof', 'Feestdag', 'Solicitatie verlof'];

    
        return view('specials', ['forWho' => $forWho, 'specialDays' => $specialDays]);
    }
}
