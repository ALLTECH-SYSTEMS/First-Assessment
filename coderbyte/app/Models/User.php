<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'category',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * This are the types of user
     *
     * @var array
     */
    protected $category = [
        '1' => 'Admin',
        '2' => 'Product Owner',
        '3' => 'Photographer.',
    ];

    /**
     * Override the mail body for reset password notification mail.
     */
    public function sendPasswordResetNotification($token)
    {
        // $link = $this->baseUrl. "/api/auth/reset-password-token?token=" . $token
        $link = "http://localhost:8000/api/auth/reset-password?token=" . $token;
        $this->notify(new ResetPasswordNotification($link));
    }

    public function request()
    {
        return $this->hasMany(Request::class, 'owner_id');
    }

    public function assigned()
    {
        return $this->hasMany(Request::class, 'photographer_id');
    }
}
