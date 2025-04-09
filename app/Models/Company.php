<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = ['company_code','company_name','company_logo','company_color', 'weekend_day_1', 'weekend_day_2', 'day_hours', 'admin_timeclock'];
    public function users()
    {

        return $this->hasMany(User::class, 'company_code', 'company_code');
    }

}
