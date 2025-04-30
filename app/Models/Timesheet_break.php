<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timesheet_break extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','daytotal_id', 'ClockedIn', 'ClockedOut', 'BreakStart', 'BreakStop', 'DaytimeCount', 'RegularHours', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime', 'userNote', 'Weekend', 'NightShift', 'Month', 'type'];
    protected $casts = [
        'ClockedIn' => 'datetime',
        'ClockedOut' => 'datetime',
        'BreakStart' => 'datetime',
        'BreakStop' => 'datetime',
        'UserId' => 'integer',
        'DaytimeCount' => 'integer',
        'RegularHours' => 'float',
        'accountableHours' => 'float',
        'BreaksTaken' => 'integer',
        'BreakHours' => 'float',
        'OverTime' => 'float',
        'userNote' => 'string',
        'Weekend' => 'boolean',
        'NightShift' => 'boolean',
        'Month' => 'datetime:Y-m-d',
        'type' => 'string',
    ];
//     public function timesheet_shift(): BelongsTo
//     {
//         // return $this->belongsTo(::class, 'daytotal_id');
//     }
}
