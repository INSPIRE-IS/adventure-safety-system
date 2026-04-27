<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HelpCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'help_categories';

    protected $fillable = [
        'name',
        'role_slugs',
        'order_id',
    ];

    protected $casts = [
        'role_slugs' => 'array',
        'order_id' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(HelpQuestion::class, 'help_category_id');
    }
}

