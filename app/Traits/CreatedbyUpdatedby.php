<?php

namespace App\Traits;

use Auth;

trait CreatedbyUpdatedby
{
    public static function bootCreatedbyUpdatedby()
    {
        static::creating(function ($model) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user) {
                $model->created_by = $user->id;
                $model->updated_by = $user->id;
            }
        });

        static::updating(function ($model) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user) {
                $model->updated_by = $user->id;
            }
        });

        static::deleting(function ($model) {
            /** @var \App\Models\User|null $user */
            $user = Auth::user();
            if ($user) {
                $model->updated_by = $user->id;
            }
        });
    }
}
