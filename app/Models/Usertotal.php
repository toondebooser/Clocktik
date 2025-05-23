<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Usertotal extends Model
{
    use HasFactory;
    protected $fillable = ['UserId','RegularHours','BreakHours', 'OverTime','Month','HollidaysAdded'];
    protected $casts = [
        'id' => 'integer',
        'UserId' => 'integer',
        'RegularHours' => 'float',
        'BreakHours' => 'float',
        'OverTime' => 'float',
        'HolidaysAdded' => 'boolean',
        'Month' => 'date',
    ];
}
