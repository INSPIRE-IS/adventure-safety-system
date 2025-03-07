<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drill extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'drills';

    protected $dates = [
        'due_date',
        'complete_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'due_date',
        'complete_date',
        'type_activity_id',
        'assigned_to_id',
        'photo_file_id',
        'signature_file_id',
        'site_id',
        'drill_schedule_id',
        'type_activity_id',
        'type_drill_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d') // Converts to "2025-01-31"
            : null;
        //$this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getCompleteDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setCompleteDateAttribute($value)
    {
        $this->attributes['complete_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d') // Converts to "2025-01-31"
            : null;
        //$this->attributes['complete_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function drill_schedule()
    {
        return $this->belongsTo(DrillSchedule::class, 'drill_schedule_id');
    }

    public function type_drill()
    {
        return $this->belongsTo(TypeDrill::class, 'type_drill_id');
    }

    public function type_activity()
    {
        return $this->belongsTo(TypeActivity::class, 'type_activity_id');
    }

    public function drill_details()
    {
        return $this->hasMany(DrillDetail::class, 'drill_id');
    }

    public function drill_members()
    {
        return $this->hasMany(DrillMember::class, 'drill_id');
    }

    // public function type_drill()
    // {
    //     return $this->belongsTo(TypeDrill::class, 'id', 'type_drill_id');
    // }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function photo_file()
    {
        return $this->belongsTo(File::class, 'photo_file_id');
    }

    public function signature_file()
    {
        return $this->belongsTo(File::class, 'signature_file_id');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'id', 'site_id');
    }
}
