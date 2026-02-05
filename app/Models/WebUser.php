<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use App\Traits\CommonTrait;
use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;

class WebUser extends Authenticatable implements OAuthenticatable
{
    use CommonTrait;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use UploadTrait;

    protected $fillable = ['name', 'email', 'dob', 'country_id', 'state_id', 'city_id', 'gender', 'status', 'email_verified_at', 'remember_token'];

    public $light = [];

    public $legend = ['{{users_name}}', '{{users_email}}'];

    protected $casts = [

    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, 'webusers'));
    }
}
