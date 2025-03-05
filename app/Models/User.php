<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use DateTimeInterface;
use Gate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Log;
use Spatie\Permission\Traits\HasRoles;

/**
 * User model for authentication.
 *
 * @method bool can(string $ability, array $arguments = [])
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    public $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'surname',
        'email',
        'mobile_number',
        'password',
        'is_admin',
        'is_email_recipient',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);
    //     self::created(function (self $user) {
    //         $registrationRole = config('panel.registration_default_role');
    //         if (! $user->roles()->get()->contains($registrationRole)) {
    //             $user->roles()->attach($registrationRole);
    //         }
    //     });
    // }

    // public function hasAnyRole(array $roles)
    // {
    //     return $this->getRoleNames()->intersect($roles)->isNotEmpty();
    // }

    public function hasAnyRole(array $roles, $teamId = null): bool
    {
        return $this->hasRole($roles, $teamId);
    }

    public function userUserAlerts()
    {
        return $this->belongsToMany(UserAlert::class);
    }

    public function getAllTeamNames()
    {
        // return $this->teams()
        // ->join('team_user', 'teams.id', '=', 'team_user.team_id') // Make sure to join properly
        // ->select('teams.id as team_id', 'teams.name', 'team_user.user_id as pivot_user_id', 'team_user.team_id as pivot_team_id')
        // ->get();
        return $this->teams()->with('type_activities')->get(); //['teams.id', 'teams.name']
    }

    public function getTeam($id)
    {
        return $this->teams()->with('type_activities')->find($id); //['teams.id', 'teams.name']
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class)->withPivot('team_id');
    // }

    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'team_id'); // Adjust for your schema
    }

    public function isAdminOfTeam(int $teamId): bool
    {
        return $this->teams()
            ->where('teams.id', $teamId)
            ->wherePivot('user_id', $this->id) // Adjust the pivot column for roles if needed
            ->exists();
    }

    /**
     * Check if the user has the given ability.
     *
     * @param string $ability
     * @param array $arguments
     * @return bool
     */
    // public function can($ability, $arguments = [])
    // {
    //     $out = \Illuminate\Support\Facades\Gate::check($ability, $arguments);
    //     Log::info('can: ' . $out);
    //     return $out;
    // }

    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_instructor')->withTimestamps();
    }

    /**
     * Get the team ID from the user's token.
     *
     * @return int|null
     */
    public function getTeamIdFromToken()
    {
        // Example logic: Extract team_id from a token or session
        $token = request()->bearerToken();
        Log::info('getTeamIdFromToken token: ' . $token);
        if ($token) {
            //$decodedToken = json_decode(base64_decode(explode('.', $token)[1]), true);
            $decodedToken = $token;
            return $decodedToken['team_id'] ?? null; // Replace with actual token structure
        }

        return null;
    }
}
