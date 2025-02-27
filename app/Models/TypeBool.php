<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class TypeBool extends Model
{
    use SoftDeletes, HasApiTokens, HasFactory;

    public $table = 'type_bools';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const NAME_SELECT = [
        '0' => 'No',
        '1' => 'Yes',
        '2' => 'N/A',
    ];

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
