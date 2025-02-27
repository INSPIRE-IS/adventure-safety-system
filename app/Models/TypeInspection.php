<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeInspection extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'type_inspections';

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

    public const NAME_SELECT = [
        '1' => 'Monthly safety inspection',
        '2' => 'Instructor Rescue Drills',
        '3' => 'Incident Report',
        '4' => 'PPE Inspections',
        '5' => 'Annual Safety Inspection',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function inspection_schedule()
    {
        return $this->hasMany(InspectionSchedule::class, 'type_inspection_id');
    }
}
