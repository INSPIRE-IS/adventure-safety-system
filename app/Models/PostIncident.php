<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostIncident extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'post_incidents';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'due_date',
        'complete_date',
        'post_incident_schedule_id',
        'report_file_id',
        'site_id',
        'assigned_to_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function post_incident_schedule()
    {
        return $this->belongsTo(PostIncidentSchedule::class, 'post_incident_schedule_id');
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

    public function report_file()
    {
        return $this->belongsTo(File::class, 'report_file_id');
    }

    public function post_incident_details()
    {
        return $this->hasMany(PostIncidentDetail::class, 'post_incident_id');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'id', 'site_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
