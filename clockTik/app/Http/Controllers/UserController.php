<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function newUser()
    {

        return view('newUser');
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
                'email.email' => "your pigeon adres seems to be none existing.",
                'password.string' => "The provided password is not allowed.",
            ]
        );

        $newUser = new User;
        $name = request()->input('name');
        $email = request()->input('email');
        $password = request()->input('password');
        $hashedPassword = bcrypt($password);
        $checkEmail = User::where('email', $email)->first();

        if ($checkEmail) {
            $exists = "This email adres already exists!";
            return view("newUser", ['exists' => $exists]);
        }

        $newUser->name = $name;
        $newUser->email = $email;
        $newUser->password = $hashedPassword;
        $newUser->email_verified_at = Carbon::now();
        $newUser->save();



        return view('welcome');
    }
    
}
