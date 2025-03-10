<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMessageLog extends Model
{
    use HasFactory;
    public $table = 'bulk_message_logs';

    protected $fillable = [
        'bulk_message_id',
        'email',
        'sent_status',
        'user_id',

    ];

    public function bulk_message()
    {
        return $this->belongsTo(BulkMessage::class);
    }
}
