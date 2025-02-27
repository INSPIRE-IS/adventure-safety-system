<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionSchedule extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'inspection_schedules';

    protected $dates = [
        'active_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PERIOD_SELECT = [
        '1' => 'None',
        '2' => '3 Month',
        '3' => '4 Month',
        '4' => '6 Month',
        '5' => '12 Month',
    ];

    protected $fillable = [
        'name',
        'site_id',
        'type_inspection_id',
        'type_activity_id',
        'active_date',
        'period_id',
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

    public function type_inspection()
    {
        return $this->belongsTo(TypeInspection::class, 'type_inspection_id');
    }

    public function type_activity()
    {
        return $this->belongsTo(TypeActivity::class, 'type_activity_id');
    }

    // public function getActiveDateAttribute($value)
    // {
    //     return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    //     //return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    // }

    // public function setActiveDateAttribute($value)
    // {
    //     $this->attributes['active_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    // }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigned_by()
    {
        return $this->belongsTo(User::class, 'assigned_by_id');
    }

    public function category_items()
    {
        return $this->belongsToMany(CategoryItem::class);
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class, 'inspection_schedule_id');
    }
}
