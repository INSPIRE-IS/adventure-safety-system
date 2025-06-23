<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inspection extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'inspections';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'inspection_schedule_id',
        'assigned_to_id',
        'due_date',
        'type_inspection_id',
        'complete_date',
        'closing_comment',
        'photo_file_id',
        'signature_file_id',
        'site_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const PERIOD_SELECT = [
        [
            'value' => "",
            'label' => "Select Period",
        ],
        [
            'value' => "30",
            'label' => "Last 30 Days",
        ],
        [
            'value' => "60",
            'label' => "Last 60 Days",
        ],
        [
            'value' => "90",
            'label' => "Last 90 Days",
        ],
    ];

    public const MONTHS_SELECT = [
        [
            'value' => "",
            'label' => "Select Month",
        ],
    ];
    public static function getMonths()
    {
        return collect(self::MONTHS_SELECT)->merge(
            collect(range(0, 5))->map(function ($i) {
                $date = Carbon::now()->subMonths($i);
                return [
                    'value' => $date->format('M Y'), // Example: "Mar 2025"
                    'label' => $date->format('M Y'),
                ];
            })
        );
    }
    public const SHOWS_SELECT = [
        [
            'value' => "",
            'label' => "Select Show",
        ],
        [
            'value' => "1000",
            'label' => "All",
        ],
        [
            'value' => "10",
            'label' => "10",
        ],
        [
            'value' => "25",
            'label' => "25",
        ],
        [
            'value' => "50",
            'label' => "50",
        ]
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function inspection_schedule()
    {
        return $this->belongsTo(InspectionSchedule::class, 'inspection_schedule_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function getDueDateAttribute($value)
    {
        return $value
            ? Carbon::parse($value)->format(config('panel.date_format'))
            : null;
        //return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setDueDateAttribute($value)
    {
        //dd($value);
        $this->attributes['due_date'] = $value
            ? Carbon::parse($value)->format('Y-m-d')
            : null;
        //$this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
        //$this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function getCompleteDateAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setCompleteDateAttribute($value)
    {
        $this->attributes['complete_date'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
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
        return $this->belongsTo(Team::class, 'site_id');
    }

    public function type_inspection()
    {
        return $this->belongsTo(TypeInspection::class, 'type_inspection_id');
    }

    public function safety_inspection_details()
    {
        return $this->hasMany(SafetyInspectionDetail::class, 'inspection_id');
    }

    public function equipment_inspection_details()
    {
        return $this->belongsTo(EquipmentInspectionDetail::class, 'inspection_id');
    }
}
