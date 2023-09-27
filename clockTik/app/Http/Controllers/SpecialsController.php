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

    public function specials()
    {
        return view('specials');
    }
}
