<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Timesheet extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','daytotal_id', 'ClockedIn', 'ClockedOut', 'BreakStart', 'BreakStop', 'DaytimeCount', 'RegularHours', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime', 'userNote', 'Weekend', 'NightShift', 'Month', 'type'];

    public function daytotal(): BelongsTo
    {
        return $this->belongsTo(Daytotal::class, 'daytotal_id');
    }
}
