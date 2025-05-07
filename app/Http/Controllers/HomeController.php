<?php

namespace App\Http\Controllers;

use App\Helpers\UserActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
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

            $credentials = $request->only(['email', 'password']);
            $remember = $request->filled('remember');

            $authenticated = DB::transaction(function () use ($credentials, $remember) {
                return auth()->attempt($credentials, $remember);
            });

            if ($authenticated) {
                // Log success
                UserActivityLogger::log('User logged in successfully', [
                    'user_id' => auth()->user()->id,
                    'email' => $request->email,
                    'remember' => $remember,
                ]);

                if (!auth()->user()->admin) {
                    return redirect('/dashboard');
                } else {
                    return redirect('/')->with('success', "Welkom " . auth()->user()->name);
                }
            }

            return redirect()->back()->withErrors('error', 'Email of wachtwoord is incorrect.');
        } catch (QueryException $e) {
            Log::error('Failed to authenticate user', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->withErrors('error', 'Er is een fout opgetreden bij het inloggen.');
        }
    }

    public function logout()
    {
        try {
            DB::transaction(function () {
                $userId = auth()->user()->id;
                auth()->logout();

                // Log success
                UserActivityLogger::log('User logged out successfully', [
                    'user_id' => $userId,
                ]);
            });

            return redirect('/');
        } catch (QueryException $e) {
            Log::error('Failed to logout user', [
                'user_id' => auth()->user()->id ?? null,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('/')->withErrors('error', 'Er is een fout opgetreden bij het uitloggen.');
        }
    }
}