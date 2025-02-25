<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !$request->user()->hasVerifiedEmail()) {  
            return redirect()->route('verification.notice')->with('message', 'You need to verify your email address. Please check your inbox.');
        }

        return $next($request);
    }
}
