<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function settingsView ($company_code, $godMode = false){

        $data = Company::where('company_code',$company_code)->first();

        if($godMode){
            $admins = User::where ('company_code', $company_code)->where('admin', true)->get();
            $workers = User::where('company_code', $company_code) ->where('admin', false)->get();
            return view('settings', ['data' =>  $data, 'admins' => $admins, 'workers' => $workers]);
        }

        return view('settings', ['data' =>  $data]);

    }


}
