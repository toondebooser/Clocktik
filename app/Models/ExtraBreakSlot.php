<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtraBreakSlot extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','type', 'timesheet_id','daytotal_id', 'BreakStart', 'BreakStop' , 'Month'];
    protected $casts = [
        'BreakStart' => 'datetime',
        'BreakStop' => 'datetime',
        'daytotal_id' => 'integer',
        'timesheet_id' => 'integer',
        'UserId' => 'integer',
        'Month' => 'datetime:Y-m-d',
        'type' => 'string'
    ];
    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class, 'timesheet_id');
    }
    public function dayTotal(): BelongsTo
    {
        return $this->belongsTo(Daytotal::class, 'daytotal_id');
    }
}
