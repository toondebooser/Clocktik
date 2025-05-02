<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Timelog extends Model
{
    use HasFactory;
    protected $fillable = ['BreakStatus','timesheet_id','ShiftStatus','Weekend','NightShift','StartWork','StartBreak', 'EndBreak','StopWork','BreaksTaken','BreakHours','RegularHours', 'userNote', 'userId','daytotal_id'];
    protected $casts = [
        'id' => 'integer',
        'timesheet_id' => 'integer',
        'BreakStatus' => 'boolean',
        'ShiftStatus' => 'boolean',
        'Weekend' => 'boolean',
        'NightShift' => 'boolean',
        'StartWork' => 'datetime',
        'StartBreak' => 'datetime',
        'EndBreak' => 'datetime',
        'StopWork' => 'datetime',
        'BreaksTaken' => 'integer',
        'userNote' => 'string',
        'UserId' => 'integer',
        'daytotal_id' => 'integer'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'UserId');
    }

    public function dayTotal ()
    {
        return $this->hasOne(Daytotal::class, 'id', 'daytotal_id');
    }
}
