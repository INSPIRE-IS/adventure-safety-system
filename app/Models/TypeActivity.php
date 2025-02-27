<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeActivity extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'type_activities';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function inspection_schedule()
    {
        return $this->hasMany(InspectionSchedule::class, 'type_activity_id');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }
}
