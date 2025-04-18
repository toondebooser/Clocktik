<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;
    protected $fillable = ['company_code','company_name','company_logo','company_color', 'weekend_day_1', 'weekend_day_2', 'day_hours', 'admin_timeclock'];
    protected $casts = [
        'id' => 'integer',
        'company_name' => 'string',
        'company_logo' => 'string',
        'company_color' => 'string',
        'company_code' => 'integer',
        'weekend_day_1' => 'integer',
        'weekend_day_2' => 'integer',
        'day_hours' => 'float',
        'admin_timeclock' => 'boolean',
        'start_time' => 'date',
        'end_time' => 'date',
    ];
    public function users()
    {

        return $this->hasMany(User::class, 'company_code', 'company_code');
    }

}
