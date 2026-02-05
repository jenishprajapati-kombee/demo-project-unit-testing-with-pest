<?php

namespace App\Models;

use App\Helper;
use App\Notifications\ResetPasswordNotification;
use App\Traits\CommonTrait;
use App\Traits\CreatedbyUpdatedby;
use App\Traits\ImportTrait;
use App\Traits\Legendable;
use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use CommonTrait;
    use CreatedbyUpdatedby;
    use HasFactory;
    use ImportTrait;
    use Legendable;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use UploadTrait;

    protected $fillable = ['name', 'email', 'role_id', 'dob', 'profile', 'country_id', 'state_id', 'city_id', 'gender', 'status', 'email_verified_at', 'remember_token', 'locale'];

    public $light = [];

    public $legend = ['{{users_name}}', '{{users_email}}'];

    protected $casts = [

    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * The gender relationship.
     */
    public static function gender()
    {
        return collect(
            [['key' => 'F', 'label' => 'Female'], ['key' => 'M', 'label' => 'Male']]
        );
    }

    /**
     * The status relationship.
     */
    public static function status()
    {
        return collect(
            [['key' => 'Y', 'label' => 'Active'], ['key' => 'N', 'label' => 'Inactive']]
        );
    }

    /**
     * The UserTag relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userTags()
    {
        return $this->hasMany(UserTag::class, 'user_id');
    }

    public function hasPermission($permission, $roleId)
    {
        $permissions = Helper::getCachedPermissionsByRole($roleId);

        return in_array($permission, $permissions);
    }

    /**
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string|null
     */
    public function getProfileAttribute($value)
    {
        if (! empty($value) && $this->is_file_exists($value)) {
            return $this->getFilePathByStorage($value);
        }

        return null;
    }

    public function initials(): string
    {
        $nameField = $this->first_name ?? $this->name ?? '';

        return Str::of($nameField)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, 'users'));
    }
}
