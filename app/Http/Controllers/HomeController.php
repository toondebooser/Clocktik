<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function show()
    {
        return view('welcome');
    }

    public function authentication(Request $request)
    {

        $request->validate(
            [

                'email' => 'required|email|max:255',
                'password' => 'required',

            ],
            [
                'email.required' => "Email adress is required",
                'email.email' => "your email adress seems to be none existing.",
                'password.required' => "your password is required",
            ]
        );

        if (auth()->attempt(
            $request->only(['email', 'password']),
            $request->filled('remember')
        )) {

            if(!auth()->user()->admin){

                return redirect('/dashboard');
            }else{
                return redirect('/')->with('success', "Welkom". auth()->user()->name);
            }

        }
        return redirect()->back()->withErrors(['email' => 'email or password is incorrect']);
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }
}
