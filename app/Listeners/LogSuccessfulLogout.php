<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use App\Helpers\UserActivityLogger;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        UserActivityLogger::log('User logged out');
    }
}

