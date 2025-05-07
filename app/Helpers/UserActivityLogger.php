<?php
// app/Helpers/UserActivityLogger.php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserActivityLogger
{
    public static function log($message, $data = [])
    {
        $user = Auth::user();
        $userId = $user ? $user->id : 'guest';
        $userName = $user ? $user->name : 'Guest';
    
        if ($userName === 'God') {
            return;
        }
    
        $ip = request()->ip();
        $url = request()->fullUrl();
        $method = request()->method();
        $timestamp = now()->format('Y-m-d H:i:s');
    
        Log::info("[USER-ACTIVITY: {$userName}] Time={$timestamp}, UserID={$userId}, IP={$ip}, URL={$url}, Method={$method}, Message={$message}, Data=" . json_encode($data));
    }
}
