<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\User;
use App\Models\Usertotal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function registrationForm()
    {

        return view('registration-form');
    }

    public function registrate(Request $request)
    {

        $request->validate(
            [

                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|min:8|confirmed',
                'company_code' => 'required|numeric|exists:companies,company_code'

            ],
            [
                'name.string' => "please enter your name!",
                'email.email' => "your email adress seems to be none existing.",
                'password.string' => "The provided password is not allowed.",
                'company_code.numeric' => "Bedrijfscode moet een nummer zijn.",
                'company_code.required' => "Voer een bedrijfscode in.",
                'company_code.exists' => "De opgegeven bedrijfscode is ongeldig.",
            ]
        );


        $name = request()->input('name');
        $email = request()->input('email');
        $password = request()->input('password');
        $company_code = request()->input('company_code');
        $checkEmail = User::where('email', $email)->first();

        
        if ($checkEmail) {
            $exists = "This email adres already exists!";
            return redirect()->back()->withErrors(['email' => $exists]);
        }
        
        $newUser = $this->createUser($name, $email, $password, $company_code);
   

        $newUser->sendEmailVerificationNotification();

   
        return redirect('/')->with('success', 'Registration successful! Please verify your email:');




        // return redirect('/dashboard');
    }
    public function createUser($name, $email, $password, $company_code, $admin = false)
    {
        // Create the User with mass assignment
        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'admin' => $admin,
            'company_code' => $company_code
        ]);

        $newUser->timelogs()->create([
            'ShiftStatus' => false,
            'BreakStatus' => false,
            'Weekend' => false,
        ]);

        $newUser->userTotals()->create([
            'RegularHours' => 0,
            'BreakHours' => 0,
            'OverTime' => 0,
            'Month' => Carbon::now('Europe/Brussels')->startOfMonth(),
        ]);

        return $newUser; 
    }
}
