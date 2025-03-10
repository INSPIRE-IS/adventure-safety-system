<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMessage extends Model
{
    use HasFactory;

    public $table = 'bulk_message';

    protected $fillable = [
        'subject',
        'message',
        'user_id',
        'site_id',
    ];

    public function logs()
    {
        return $this->hasMany(BulkMessageLog::class);
    }
}
