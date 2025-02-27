<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrillDetail extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'drill_details';

    public const COMPETENT_SELECT = [
        '1' => 'Yes',
        '2' => 'No',
    ];

    protected $dates = [
        'complete_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'site_id',
        'instructor_id',
        'complete_date',
        'signature_file_id',
        'competent',
        'type_drill_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'site_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function drill_schedule()
    {
        return $this->belongsTo(DrillSchedule::class, 'drill_schedule_id');
    }

    public function getCompleteDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setCompleteDateAttribute($value)
    {
        $this->attributes['complete_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function signature_file()
    {
        return $this->belongsTo(File::class, 'signature_file_id');
    }

    public function type_drill()
    {
        return $this->belongsTo(TypeDrill::class, 'type_drill_id');
    }
}
