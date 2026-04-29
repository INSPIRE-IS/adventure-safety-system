<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', function (User $user) {
            $teamId = session('team_id');
            if ($teamId) {
                setPermissionsTeamId((int) $teamId);
                /** @disregard P1013 */
                $user->unsetRelation('roles')->unsetRelation('permissions');
            }

            /** @disregard P1013 */
            return $user->hasAnyRole(['super']);
        });
    }
}
