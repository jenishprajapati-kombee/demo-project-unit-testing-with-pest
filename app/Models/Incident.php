<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_logs',
        'raw_metrics',
        'severity',
        'likely_cause',
        'confidence',
        'reasoning',
        'next_steps',
    ];

    protected $casts = [
        'raw_logs' => 'array',
        'raw_metrics' => 'array',
        'confidence' => 'float',
    ];
}
