<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function settingsView($company_code)
    {

        $data = Company::where('company_code', $company_code)->first();
        $admins = User::where('company_code', $company_code)->where('admin', true)->get();
        $workers = User::where('company_code', $company_code)->where('admin', false)->get();
        return view('settings', ['data' =>  $data, 'admins' => $admins, 'workers' => $workers]);

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
    public function logohandler($company, $company_logo){
        $logoName = time(). '_' .$company_logo->getClientOriginalName();
        $folderPath = 'logos/' . $company->company_code;
        $fullPath = public_path($folderPath);
        
        if (!File::exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }
        $company_logo->move($fullPath, $logoName);
        $logoPath = $folderPath . '/' . $logoName;
        
        return $logoPath;        

    }
    public function updateSettings (Request $request)
    {
        $company =Company::where("company_code", $request->company_code)->first();
        $updateData = [];
        foreach ($request->all() as $key => $value) {
            // echo $key. ": ". $value.", ";
            // dd($request->all());
            if ($key === '_token') continue;
            // if($key === 'company_logo') $this->updateLogo($value);
            $updateData[$key] = $key == 'company_logo' ? $this->logohandler($company, $value) : $value;
        }
         $success = $company->update($updateData);
         if($success){
            return redirect()->back()->with('success', 'Instellingen zijn aangepast.');
         } else{
            return redirect()->back()->with('error', 'Er ging iets mis bij het aanpassen van de instellingen.');
         }


        
        
    }
}
