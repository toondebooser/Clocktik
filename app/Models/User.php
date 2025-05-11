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

    public function sendEmailVerificationNotification($companyCode = null, $email = null)
    {
        $this->notify(new CustomVerifyEmail($companyCode, $email));
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
        'id' => 'integer',
        'name' => 'string',
        'email' => 'string',
        'admin' => 'boolean',
        'god' => 'boolean',
        'company_code' => 'string',
        'email_verified_at' => 'datetime',
        'password' => 'string',
        'remember_token' => 'string',
    ];
    public function timelogs()
    {
        return $this->hasOne(Timelog::class, 'UserId');
    }
    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'UserId');
    }
    public function userTotals()
    {
        return $this->hasMany(Usertotal::class, 'UserId');
    }
/**
 * Get the day totals for the user.
 *
 * @return \Illuminate\Database\Eloquent\Relations\HasMany
 */
public function dayTotals()
{
    return $this->hasMany(Daytotal::class, 'UserId');
}
    public function company()
    {
        return $this->hasOne(Company::class, 'company_code', 'company_code');
    }
}
