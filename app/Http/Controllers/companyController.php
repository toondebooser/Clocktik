<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyController extends Controller
{

    
    public function registrateCompany(Request $request){

        $userController = new UserController;
        $companyName = request()->input('companyName');
        $adminName = request()->input('adminName');
        $email = request()->input('adminEmail');
        // $userController->createUser();
    }
}
