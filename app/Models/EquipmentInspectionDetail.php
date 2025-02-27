<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentInspectionDetail extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'equipment_inspection_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'condition',
        'identity',
        'created_at',
        'updated_at',
        'deleted_at',
        'site_id',
        'inspection_id',
        'category_item_id',
        'category_detail_id',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'site_id');
    }
}
