<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Daytotal extends Model
{
    use HasFactory;
    protected $fillable = ['official_holiday',"DayOverlap","NightShift",'Completed','Month','company_code','UserId','RegularHours','type', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime','Weekend'];
    protected $casts = [
        'id' => 'integer',
        'UserId' => 'integer',
        'DaytimeCount' => 'integer',
        'RegularHours' => 'float',
        'accountableHours' => 'float',
        'BreaksTaken' => 'integer',
        'BreakHours' => 'float',
        'OverTime' => 'float',
        'type' => 'string',
        'userNote' => 'string',
        'Month' => 'date',
        'Completed' => 'boolean',
        'Weekend' => 'boolean',
        'official_holiday' => 'boolean',
        'NightShift' => 'boolean',
        'DayOverlap' => 'boolean',
    ];
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'daytotal_id');
    }
    public function extraBreakSlots()
    {
        return $this->hasMany(ExtraBreakSlot::class, 'daytotal_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserId');
    }
   
}
