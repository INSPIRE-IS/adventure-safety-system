<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrillMember extends Model
{
    use  SoftDeletes, Auditable, HasFactory;

    public $table = 'drill_member';

    public const COMPITENT_SELECT = [
        '1' => 'Yes',
        '2' => 'No',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'site_id',
        'drill_id',
        'member_name',
        'user_id',
        'competent',
        'created_at',
        'updated_at',
        'deleted_at',
        'uuid', // mobile id update irrelevant after mobile cleared required for updates
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function site()
    {
        return $this->belongsTo(Team::class, 'site_id');
    }

    public function drill()
    {
        return $this->belongsTo(Drill::class, 'drill_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
