<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppError extends Model
{
    use HasFactory;

    protected $table = 'app_errors';

    protected $fillable = [
        'user_id',
        'team_id',
        'type',
        'error',
    ];

    protected $casts = [
        'error' => 'array',
    ];
}
