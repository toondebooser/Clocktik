<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settingsView ($company_code){

        return view('settings', ['company_code', $company_code]);

    }
}
