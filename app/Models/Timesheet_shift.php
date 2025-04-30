<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Timesheet_shift extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','daytotal_id', 'ClockedIn', 'ClockedOut', 'BreaksTaken','userNote', 'Month'];
    protected $casts = [
        'ClockedIn' => 'datetime',
        'ClockedOut' => 'datetime',
        'UserId' => 'integer',
        'BreaksTaken' => 'integer',
        'userNote' => 'string',
        'Weekend' => 'boolean',
        'NightShift' => 'boolean',
        'Month' => 'datetime:Y-m-d',
    ];
    public function daytotal(): BelongsTo
    {
        return $this->belongsTo(Daytotal::class, 'daytotal_id');
    }
    // public function timesheets_break(): 
}
