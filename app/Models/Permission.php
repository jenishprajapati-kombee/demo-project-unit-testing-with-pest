<?php

namespace App\Models;

use App\Traits\CreatedbyUpdatedby;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use CreatedbyUpdatedby;
    use HasFactory;

    protected $fillable = ['name', 'guard_name', 'label'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
