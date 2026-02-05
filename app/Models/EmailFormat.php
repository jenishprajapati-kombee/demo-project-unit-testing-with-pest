<?php

namespace App\Models;

use App\Traits\CreatedbyUpdatedby;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailFormat extends Model
{
    use CreatedbyUpdatedby;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['type', 'label', 'body'];
}
