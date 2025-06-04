<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasProfilePhoto, TwoFactorAuthenticatable;

    public function roles()
    {
        return $this->belongsToMany(Role::class,'user_role','user_id','role_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function requestForms()
    {
        return $this->hasMany(RequestForm::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function approvedRequests()
    {
        return $this->belongsToMany(RequestForm::class,'requests_user','user_id','request_id');
    }

    public function hasAnyRole($roles)
    {
        if(is_array($roles)){
            foreach ($roles as $role){
                if($this->hasRole($role)){
                    return true;
                }
            }
        }else{
            if($this->hasRole($roles)){
                return true;
            }
        }
        return false;
    }

    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'middleName',
        'lastName',
        'email',
        'password',
        'verified',
        'position_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
        'email',
        'position_id',
        'email_verified_at',
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        "two_factor_secret",
        "two_factor_recovery_codes",
        "two_factor_confirmed_at"
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
