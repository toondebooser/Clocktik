<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timelog extends Model
{
    use HasFactory;
    protected $fillable = ['BreakStatus','ShiftStatus','Weekend','NightShift','StartWork','StartBreak', 'EndBreak','StopWork','BreaksTaken','BreakHours','RegularHours', 'userNote', 'userId'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
