<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryItem extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'category_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const CERTIFICATE_RECIEVED_SELECT = [
        '1' => 'Yes',
        '2' => 'No',
        '3' => 'N/A',
    ];

    protected $fillable = [
        'site_id',
        'type_inspection_id',
        'name',
        'in_use_date',
        'out_use_date',
        'certificate_recieved_id',
        'certificate_file_id',
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

    // public function inspection_schedule()
    // {
    //     return $this->belongsTo(InspectionSchedule::class);
    // }

    public function type()
    {
        return $this->belongsTo(TypeInspection::class, 'type_inspection_id');
    }

    public function certificate_recieved()
    {
        return $this->belongsTo(TypeBool::class, 'certificate_recieved_id');
    }

    public function certificate_file()
    {
        return $this->belongsTo(File::class, 'certificate_file_id');
    }

    public function categoryDetails()
    {
        return $this->hasMany(CategoryDetail::class, 'category_item_id');
    }
}
