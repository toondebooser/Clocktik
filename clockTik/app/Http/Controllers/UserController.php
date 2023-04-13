<?php

namespace App\Http\Controllers;

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
                'name.string' => "Unless your mother was a cold hearted ****, please enter your name!",
                'email.email' => "your pigeon adres seems to be none existing.",
                'password.string' => "The provided password is not allowed.",
            ]
        );

        return view('welcome', []);
    }
}
