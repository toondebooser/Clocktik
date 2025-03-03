<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Utilities\UserUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{


    public function registrateCompany(Request $request)
    {

        $userController = new UserController;
        $companyName = $request->companyName;
        $adminName = request()->input('adminName');
        $email = request()->input('adminEmail');
        $companyCode = UserUtility::companyNumberGenerator();
        $newAdmin =  $userController->createUser($adminName, $email, Hash::make('tiktrackadmin'), $companyCode, true);
        $company = Company::create([
            'company_name' => $companyName,
            'company_code' => $companyCode,
        ]);
         $newAdmin->sendEmailVerificationNotification($companyCode);
        if ($company) return redirect('/')->with('success', 'Registration successful! Please verify your email');
    }
}
