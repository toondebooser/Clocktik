<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MyWorkersController extends Controller
{
    public function fetchWorkers()
    {
        $workers = User::all();
        return view('my-workers', ['workers' => $workers]);
    }
}
