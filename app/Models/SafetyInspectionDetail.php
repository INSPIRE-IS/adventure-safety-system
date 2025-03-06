<?php

namespace App\Models;

use App\Traits\Auditable;
// use App\Traits\MultiTenantModelTrait;MultiTenantModelTrait, 
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyInspectionDetail extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'safety_inspection_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const CONDITION_SELECT = [
        '1' => 'Good',
        '2' => 'Bad',
        '3' => 'N/A',
    ];

    protected $fillable = [
        'site_id',
        'inspection_id',
        'category_item_id',
        'category_detail_id',
        'condition',
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

    public function inspection()
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }

    public function category_item()
    {
        return $this->belongsTo(CategoryItem::class, 'category_item_id');
    }

    public function category_detail()
    {
        return $this->belongsTo(CategoryDetail::class, 'category_detail_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'table_row_id', 'id')
            ->where('table_name', 'safety_inspection_details');
    }
}
