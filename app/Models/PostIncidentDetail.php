<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostIncidentDetail extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'post_incident_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'post_incident_id',
        'question_id',
        'answer',

        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function post_incident()
    {
        return $this->belongsTo(PostIncident::class, 'post_incident_id');
    }

    public function question()
    {
        return $this->belongsTo(TypeQuestion::class, 'question_id');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'id', 'site_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
