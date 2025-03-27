<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function settingsView($company_code)
    {

        $data = Company::where('company_code', $company_code)->first();
        $admins = User::where('company_code', $company_code)->where('admin', true)->get();
        $workers = User::where('company_code', $company_code)->where('admin', false)->get();
        return view('settings', ['data' =>  $data, 'admins' => $admins, 'workers' => $workers]);

        return view('settings', ['data' =>  $data]);
    }
    public function changeRights($id, $company_code)
    {
        $user = User::find($id);
        $company = Company::where('company_code', $company_code)->first();
        if ($user) {
            $user->update([
                'admin' => !$user->admin 
            ]);
            return redirect()->route('adminSettings', ['company_code' => $company_code, 'godMode' => true])
                             ->with('success', "Rechten voor $user->name zijn aangepast in bedrijf: $company->company_name");
        }
        return redirect()->route('adminSettings', ['company_code' => $company_code, 'godMode' => true])
                         ->with('error', "User $id not found");
    }
}
