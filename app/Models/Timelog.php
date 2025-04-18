<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Timelog extends Model
{
    use HasFactory;
    protected $fillable = ['BreakStatus','ShiftStatus','Weekend','NightShift','StartWork','StartBreak', 'EndBreak','StopWork','BreaksTaken','BreakHours','RegularHours', 'userNote', 'userId'];
    protected $casts = [
        'id' => 'integer',
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
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'UserId');
    }
}
