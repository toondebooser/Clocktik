<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Helpers\UserActivityLogger;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        UserActivityLogger::log('User logged in');
    }
}
