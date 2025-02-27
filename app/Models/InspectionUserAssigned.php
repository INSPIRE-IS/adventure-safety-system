<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionUserAssigned extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'inspection_user_assigneds';

    protected $dates = [
        'due_by_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'inspection_id',
        'assigned_to_id',
        'due_by_date',
        'photo_id',
        'signature_id',
        'report_file_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function getDueByDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDueByDateAttribute($value)
    {
        $this->attributes['due_by_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function photo()
    {
        return $this->belongsTo(File::class, 'photo_id');
    }

    public function signature()
    {
        return $this->belongsTo(File::class, 'signature_id');
    }

    public function report_file()
    {
        return $this->belongsTo(File::class, 'report_file_id');
    }
}
