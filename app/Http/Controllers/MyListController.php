<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Timelog;
use App\Models\User;
use Illuminate\Http\Request;

class MyListController extends Controller
{
    public function fetchList($type, $company_code)
    {
        $dataSet = $type == "Bedrijven" ? Company::get() : User::where('company_code', $company_code)->with('timelogs')->get();
        return view('my-list', ['dataSet' => $dataSet, 'type' => $type == 'Voorwie' ? "Voor wie?": $type]);
    }
}
