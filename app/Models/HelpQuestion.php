<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'help_questions';

    protected $fillable = [
        'question',
        'help_category_id',
        'team_id',
        'is_internal',
    ];

    protected $casts = [
        'help_category_id' => 'integer',
        'team_id' => 'integer',
        'is_internal' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(HelpCategory::class, 'help_category_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function answer()
    {
        return $this->hasOne(HelpAnswer::class, 'help_question_id');
    }
}

