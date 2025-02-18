<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = ['UserId', 'ClockedIn', 'ClockedOut', 'BreakStart', 'BreakStop', 'DaytimeCount', 'RegularHours', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime', 'userNote', 'Weekend', 'NightShift','Month','type'];
}
