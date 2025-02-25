<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    public function dayTotals()
    {
        return $this->hasMany(DayTotal::class, 'company_code', 'company_code');
    }

}
