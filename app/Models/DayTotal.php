<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayTotal extends Model
{
    use HasFactory;
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'day_id');
    }
}
