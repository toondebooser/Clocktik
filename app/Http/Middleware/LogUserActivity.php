<?php
// app/Http/Middleware/LogUserActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\UserActivityLogger;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $data = $request->except(['password', 'password_confirmation', '_token']);
            $timestamp = now()->format('Y-m-d H:i:s');

            $logMessage = "[{$timestamp}] Visited page";

            UserActivityLogger::log($logMessage, $data);
        }

        return $next($request);
    }
}
