<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\CustomPasswordResetEmail;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable;

    public function sendEmailVerificationNotification($companyCode = null)
    {
        $this->notify(new CustomVerifyEmail($companyCode));
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordResetEmail($token));
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_code',
        'admin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function timelogs()
    {
        return $this->hasOne(Timelog::class, 'UserId');
    }
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'UserId');
    }
    public function userTotal()
    {
        return $this->hasMany(Usertotal::class, 'UserId');
    }
    public function dayTotals()
    {
        return $this->hasMany(Daytotal::class, 'UserId');
    }
}
