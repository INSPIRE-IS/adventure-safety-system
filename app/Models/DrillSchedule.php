<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrillSchedule extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'drill_schedules';

    protected $dates = [
        'active_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PERIOD_SELECT = [
        '1' => 'None',
        '2' => '3 Months',
        '3' => '6 Month',
        '4' => '12 Month',
    ];

    protected $fillable = [
        'name',
        'type_activity_id',
        'site_id',
        'type_drill_id',
        'period_id',
        'active_date',
        'assigned_to_id',
        'assigned_by_id',
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

    public function type_drill()
    {
        return $this->belongsTo(TypeDrill::class, 'type_drill_id');
    }

    public function type_activity()
    {
        return $this->belongsTo(TypeActivity::class, 'type_activity_id');
    }

    public function getActiveDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setActiveDateAttribute($value)
    {
        //dd($value);
        // $value = trim($value); // Remove leading/trailing spaces
        //$value = preg_replace('/[^0-9\/\-:. ]/', '', $value); // Remove unexpected characters      
        $this->attributes['active_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d') // Converts to "2025-01-31"
            : null;
        //$this->attributes['active_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function drill_details()
    {
        return $this->hasMany(DrillDetail::class, 'drill_schedule_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }
}
