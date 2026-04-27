<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'help_answers';

    protected $fillable = [
        'help_question_id',
        'answer_md',
    ];

    protected $casts = [
        'help_question_id' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(HelpQuestion::class, 'help_question_id');
    }
}

