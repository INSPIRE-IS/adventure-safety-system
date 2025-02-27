<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Team extends Model
{
    use HasFactory, SoftDeletes, Auditable, HasRoles;

    public $table = 'teams';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // protected $fillable = [
    //     'created_at',
    //     'updated_at',
    //     'location',
    //     'color_primary',
    //     'color_secondary',
    //     'color_other',
    //     'deleted_at',
    //     'name',
    //     'owner_id',
    // ];



    protected $fillable = ['name', 'description', 'location', 'phone', 'email', 'website', 'image'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function type_activities()
    {
        return $this->belongsToMany(TypeActivity::class);
    }

    // public function owner()
    // {
    //     return $this->belongsTo(User::class, 'owner_id');
    // }

    public function siteFiles()
    {
        return $this->hasMany(File::class, 'site_id', 'id');
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    // public static function boot()
    // {
    //     parent::boot();

    //     self::created(function ($model) {
    //         // Save the current team_id from session for restoration later
    //         $session_team_id = getPermissionsTeamId();

    //         // Set Spatie Permissions team context to the new team
    //         setPermissionsTeamId($model);

    //         // Assign the Super Admin role to the specified user (replace 'your_user_id' with actual logic)
    //         $user = auth()->user();
    //         //$user->assignRole('Super Admin');
    //         //User::find('your_user_id')->assignRole('Super Admin');
    //         User::find($user->id)->assignRole('super');
    //         // User::find('your_user_id')->assignRole('Super Admin');
    //         /** @var App\Models\User */
    //         // auth()->user()->assignRole('Super Admin');

    //         // Restore the original team_id to session
    //         setPermissionsTeamId($session_team_id);
    //     });
    // }
}
