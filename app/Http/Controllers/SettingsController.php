<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settingsView ($company_code){

        $data = Company::where('company_code',$company_code)->first();

        return view('settings', ['data' =>  $data]);

    }


}
