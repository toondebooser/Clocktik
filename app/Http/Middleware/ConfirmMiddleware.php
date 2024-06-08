<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfirmMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('confirmed_action') ) {
            return redirect()->route('dashboard')->with('error','oeps');
        }

        $response = $next($request);
        $request->session()->forget('confirmed_action');
        
        return $response;
    }
}
