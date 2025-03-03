<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Utilities\UserUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{

    
    public function registrateCompany(Request $request){

        $userController = new UserController;
        $companyName = request()->input('companyName');
        $adminName = request()->input('adminName');
        $email = request()->input('adminEmail');
        $companyCode = UserUtility::companyNumberGenerator();
        $userController->createUser($adminName,$email,Hash::make('tiktrackadmin'),true);
        $company = Company::create([
            'company_name'=> $companyName,
            'company_code' => $companyCode,
        ]);

    }
}
