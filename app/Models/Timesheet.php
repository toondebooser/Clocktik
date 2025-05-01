<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Timesheet extends Model
{
    use HasFactory;
    protected $fillable = ['UserId', 'daytotal_id', 'ClockedIn', 'ClockedOut', 'BreakStart', 'BreakStop', 'DaytimeCount', 'RegularHours', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime', 'userNote', 'Weekend', 'NightShift', 'Month', 'type'];
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
    public function daytotal(): BelongsTo
    {
        return $this->belongsTo(Daytotal::class, 'daytotal_id');
    }
    public function extra_break_slots()
    {
        return $this->hasMany(Extra_break_slot::class, 'timesheet_id');
    }
}
