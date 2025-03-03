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
                'companyCode' => 'required|min:10|exists:companies,company_code'

            ],
            [
                'name.string' => "please enter your name!",
                'email.email' => "your email adress seems to be none existing.",
                'password.string' => "The provided password is not allowed.",
                'companyCode.required' => "Please provide a company code.",
                'companyCode.exists' => "The provided company code does not exist.",
            ]
        );


        $name = request()->input('name');
        $email = request()->input('email');
        $password = request()->input('password');
        $companyCode = request()->input('companyCode');
        $checkEmail = User::where('email', $email)->first();

        
        if ($checkEmail) {
            $exists = "This email adres already exists!";
            return redirect()->back()->withErrors(['email' => $exists]);
        }
        
        $newUser = $this->createUser($name, $email, $password, $companyCode);
        // $newUser->name = $name;
        // $newUser->email = $email;
        // $newUser->password = $hashedPassword;
        // // $newUser->email_verified_at = now('Europe/Brussels');
        // $newUser->save();
        // $userRow->UserId = $newUser->id;
        // $userRow->ShiftStatus = false;
        // $userRow->BreakStatus = false;
        // $userRow->weekend = false;
        // $userRow->save();

        // $userTotal->UserId = $newUser->id;
        // $userTotal->RegularHours = 0;
        // $userTotal->BreakHours = 0;
        // $userTotal->OverTime = 0;
        // $userTotal->Month = now('Europe/Brussels');
        // $userTotal->save();
        

        $newUser->sendEmailVerificationNotification();

        // Optionally, log in the user after registration (comment if not needed)
        // Auth::login($newUser);

        // auth()->login($newUser);
        return redirect('/')->with('success', 'Registration successful! Please verify your email:');




        // return redirect('/dashboard');
    }
    public function createUser($name, $email, $password, $companyCode, $admin = false)
    {
        // Create the User with mass assignment
        $newUser = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'admin' => $admin,
            'company_code' =>$companyCode
        ]);

        $newUser->userRow()->create([
            'ShiftStatus' => false,
            'BreakStatus' => false,
            'weekend' => false,
        ]);

        $newUser->userTotal()->create([
            'RegularHours' => 0,
            'BreakHours' => 0,
            'OverTime' => 0,
            'Month' => Carbon::now('Europe/Brussels'),
        ]);

        return $newUser; 
    }
}
