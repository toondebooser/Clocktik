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
    public function changeRights(Request $request, $id, $company_code)
    {
        $user = User::find($id);
        if ($user) {
            $targetSide = $request->input('target_side');
    
            $shouldToggle = false;
            if ($targetSide === 'left' && !$user->admin) {
                $shouldToggle = true;
            } elseif ($targetSide === 'right' && $user->admin) {
                $shouldToggle = true;
            }
    
            if ($shouldToggle) {
                $user->update([
                    'admin' => !$user->admin
                ]);
                return redirect()->route('adminSettings', ['company_code' => $company_code])
                                 ->with('success', "Rechten voor $user->name zijn aangepast ");
            }
    
            return redirect()->route('adminSettings', ['company_code' => $company_code])
                             ->with('message', "No rights change needed for user $id");
        }
        return redirect()->route('adminSettings', ['company_code' => $company_code])
                         ->with('error', "User $id not found");
    }
}
