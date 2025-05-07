<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use App\Models\Company;
use App\Utilities\UserUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function registrateCompany(Request $request)
    {
        try {
            $userController = new UserController;
            $companyName = $request->companyName;
            $adminName = $request->input('adminName');
            $email = $request->input('adminEmail');
            $companyCode = UserUtility::companyNumberGenerator();

            $newAdmin = null;
            $company = null;

            DB::transaction(function () use ($userController, $adminName, $email, $companyCode, $companyName, &$newAdmin, &$company) {
                $newAdmin = $userController->createUser($adminName, $email, Hash::make('tiktrackadmin'), $companyCode, true);
                if (!$newAdmin) {
                    throw new \Exception('Failed to create admin user');
                }

                $company = Company::create([
                    'company_name' => $companyName,
                    'company_code' => $companyCode,
                ]);
                if (!$company) {
                    throw new \Exception('Failed to create company');
                }
                $newAdmin->sendEmailVerificationNotification($companyCode);
            });


            // Log success
            UserActivityLogger::log('Company registered successfully', [
                'company_code' => $companyCode,
                'company_name' => $companyName,
                'admin_email' => $email,
                'user_id' => auth()->user()->id ?? null,
            ]);

            return redirect('/')->with('success', 'Registration successful! Please verify your email');
        } catch (QueryException $e) {
            Log::error('Failed to register company', [
                'company_name' => $request->companyName,
                'company_code' => $companyCode ?? null,
                'admin_email' => $request->input('adminEmail'),
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het registreren van het bedrijf.');
        } catch (\Exception $e) {
            Log::error('Failed to register company', [
                'company_name' => $request->companyName,
                'company_code' => $companyCode ?? null,
                'admin_email' => $request->input('adminEmail'),
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het registreren van het bedrijf.');
        }
    }
}