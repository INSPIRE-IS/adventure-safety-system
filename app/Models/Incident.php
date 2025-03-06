<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Incident extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'incidents';

    protected $dates = [
        'incident_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'type_incident_id',
        'incident_date',
        'instructor_one_name',
        'instructor_two_name',
        'description',
        'site_id',
        'user_id',
        'uuid', // unique id created on mobile incase of update
        'signature_file_id',
        'incident_file_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'incident_instructor', 'incident_id', 'instructor_id')->withTimestamps();
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'site_id');
    }

    public function type_incident()
    {
        return $this->belongsTo(TypeIncident::class, 'type_incident_id');
    }

    public function getIncidentDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setIncidentDateAttribute($value)
    {
        $this->attributes['incident_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function signature_file()
    {
        return $this->belongsTo(File::class, 'signature_file_id');
    }

    public function incident_file()
    {
        return $this->belongsTo(File::class, 'incident_file_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'table_row_id', 'id')
            ->where('table_name', 'incidents');
    }
}
