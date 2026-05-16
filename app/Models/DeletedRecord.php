<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeletedRecord extends Model
{
    use HasFactory;

    protected $table = 'deleted_records';

    protected $fillable = [
        'module',
        'record_id',
        'detail_id',
        'file_count',
        'device_deleted_at',
        'payload',
        'user_id',
        'site_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'device_deleted_at' => 'datetime',
    ];
}