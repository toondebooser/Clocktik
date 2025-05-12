<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Utilities\UserUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
{



public function registrateCompany(Request $request)
{
    try {
        return DB::transaction(function () use ($request) {
            $userController = new UserController;
            $companyName = $request->companyName;
            $adminName = request()->input('adminName');
            $email = request()->input('adminEmail');
            $companyCode = UserUtility::companyNumberGenerator();

            $company = Company::create([
                'company_name' => $companyName,
                'company_code' => $companyCode,
            ]);

            $newAdmin = $userController->createUser(
                $adminName,
                $email,
                'tiktrackadmin',
                $companyCode,
                true
            );

            $newAdmin->sendEmailVerificationNotification($companyCode, $email);

            if ($company) {
                return redirect('/')->with('success', 'Registration successful! Please verify your email');
            }

            // If $company is null, throw an exception to rollback
            throw new \Exception('Company creation failed');
        });
    } catch (\Exception $e) {
        return redirect('/')->withErrors('error', 'Registration failed: ' . $e->getMessage());
    }
}
}
