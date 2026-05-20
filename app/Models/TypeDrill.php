<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeDrill extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'type_drills';

    protected $dates = [
        'specific_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'due_days',
        'frequency',
        'specific_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const FREQUENCY_SELECT = [
        '1' => 'Weekly',
        '2' => 'Fourtnightly',
        '3' => 'Monthly',
        '4' => 'tri Monthly',
        '5' => 'Quarterly',
        '6' => 'six Monthly',
        '7' => 'Annually',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getSpecificDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setSpecificDateAttribute($value)
    {
        $this->attributes['specific_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }
}
