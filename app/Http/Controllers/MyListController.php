<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;

class MyListController extends Controller
{
    public function fetchList($type, $company_code)
    {
        $workers = User::where('company_code', $company_code)->with('timelogs')->get();
        dd($workers);
        return view('my-list', ['workers' => $workers, 'type' => $type]);
    }
}
