<?php

namespace App\Http\Controllers;

use App\Models\Timelog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            ],
            [
                'name.string' => "please enter your name!",
                'email.email' => "your email adress seems to be none existing.",
                'password.string' => "The provided password is not allowed.",
            ]
        );

        $newUser = new User;
        $userRow = new Timelog;
        $name = request()->input('name');
        $email = request()->input('email');
        $password = request()->input('password');
        $hashedPassword = bcrypt($password);
        $checkEmail = $newUser->where('email', $email)->first();


        if ($checkEmail) {
            $exists = "This email adres already exists!";
            return redirect()->back()->withErrors(['email'=> $exists]);
        }

        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = $hashedPassword;
        $newUser->email_verified_at = now();
        $newUser->save();
        auth()->login($newUser);
        $userRow->UserId = $newUser->id;
        $userRow->ShiftStatus = false;
        $userRow->BreakStatus = false;
        $userRow->save();



        return redirect('/dashboard');
    }

}
