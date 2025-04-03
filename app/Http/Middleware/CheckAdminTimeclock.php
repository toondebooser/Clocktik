<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class CheckAdminTimeclock
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if ($user->admin) { 
            $company = $user->company;
            
            if (!$company->Admin_timeclock) {
                return redirect('/');
            }
        }
        
        return $next($request);
    }
}