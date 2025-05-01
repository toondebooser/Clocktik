<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class extra_break_slot extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','timesheet_id', 'BreakStart', 'BreakStop' , 'Month'];
    protected $casts = [
        'BreakStart' => 'datetime',
        'BreakStop' => 'datetime',
        'timeheet_id' => 'integer',
        'UserId' => 'integer',
        'Month' => 'datetime:Y-m-d',
    ];
    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class, 'timesheet_id');
    }
}
