<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Daytotal extends Model
{
    use HasFactory;
    protected $fillable = ['Month','company_code','UserId','RegularHours', 'accountableHours', 'BreaksTaken', 'BreakHours', 'OverTime'];
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'daytotal_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'UserId');
    }
}
