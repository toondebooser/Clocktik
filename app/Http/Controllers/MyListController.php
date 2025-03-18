<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;

class MyListController extends Controller
{
    public function fetchList($type, $company_code)
    {
        dd($company_code);
        $workers = User::with('timelogs')->get();
        $setForTimesheet = true;
        return view('my-list', ['workers' => $workers, 'setForTimesheet' => $setForTimesheet, 'type' => $type]);
    }
}
