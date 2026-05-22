<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // Retrieve the logged-in user
        /** @var \App\Models\User|null $user */
        $user = $request->user();

        $team = null;
        $teams = [];
        $permissions = [];
        $roles = [];
        $alerts = [];
        $is_admin = false;

        if ($user) {
            /** @disregard P1013 */
            $user->loadMissing('teams');

            $teamIds = $user->teams->pluck('id')->values();

            $team_id = (int) session()->get('team_id', 0);

            // If the session team is missing/invalid for this user, choose a safe default.
            if ($team_id <= 0 || ($teamIds->isNotEmpty() && !$teamIds->contains($team_id))) {
                $team_id = (int) ($teamIds->first() ?? 0);
            }

            setPermissionsTeamId($team_id > 0 ? $team_id : null);

            /** @disregard P1013 */
            $user->unsetRelation('roles')->unsetRelation('permissions');

            /** @disregard P1013 */
            $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);

            // If the chosen team has no roles for this user, fall back to a team where they DO have roles.
            // This prevents "no roles" logins when a user belongs to multiple sites but only has roles on some.
            if ($team_id > 0 && $teamIds->count() > 1) {
                $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
                $modelHasPermissionsTable = config('permission.table_names.model_has_permissions', 'model_has_permissions');
                $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
                $morphKey = config('permission.column_names.model_morph_key', 'model_id');

                $rolesTable = config('permission.table_names.roles', 'roles');
                $hasRolesInChosenTeam = DB::table($modelHasRolesTable)
                    ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
                    ->where($morphKey, $user->getKey())
                    ->where('model_type', $user->getMorphClass())
                    ->where("{$modelHasRolesTable}.{$teamKey}", $team_id)
                    ->where(function ($q) use ($rolesTable, $teamKey, $team_id) {
                        $q->whereNull("{$rolesTable}.{$teamKey}")
                            ->orWhere("{$rolesTable}.{$teamKey}", $team_id);
                    })
                    ->exists();

                $hasDirectPermissionsInChosenTeam = DB::table($modelHasPermissionsTable)
                    ->where($morphKey, $user->getKey())
                    ->where('model_type', $user->getMorphClass())
                    ->where($teamKey, $team_id)
                    ->exists();

                if (!$hasRolesInChosenTeam && !$hasDirectPermissionsInChosenTeam) {
                    // Find the first team where the user has *effective* access.
                    // Important: the role definition itself may be global (team_id NULL) or team-scoped.
                    // We must consider both the assignment team_id and the role.team_id when choosing a fallback.
                    $fallbackTeamId = DB::table($modelHasRolesTable)
                        ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
                        ->where("{$modelHasRolesTable}.{$morphKey}", $user->getKey())
                        ->where("{$modelHasRolesTable}.model_type", $user->getMorphClass())
                        ->whereIn("{$modelHasRolesTable}.{$teamKey}", $teamIds->all())
                        ->where(function ($q) use ($rolesTable, $teamKey, $modelHasRolesTable) {
                            $q->whereNull("{$rolesTable}.{$teamKey}")
                                ->orWhereColumn("{$rolesTable}.{$teamKey}", "{$modelHasRolesTable}.{$teamKey}");
                        })
                        ->orderBy("{$modelHasRolesTable}.{$teamKey}")
                        ->value("{$modelHasRolesTable}.{$teamKey}");

                    if (!$fallbackTeamId) {
                        $fallbackTeamId = DB::table($modelHasPermissionsTable)
                            ->where("{$modelHasPermissionsTable}.{$morphKey}", $user->getKey())
                            ->where("{$modelHasPermissionsTable}.model_type", $user->getMorphClass())
                            ->whereIn("{$modelHasPermissionsTable}.{$teamKey}", $teamIds->all())
                            ->orderBy("{$modelHasPermissionsTable}.{$teamKey}")
                            ->value("{$modelHasPermissionsTable}.{$teamKey}");
                    }

                    if ($fallbackTeamId) {
                        $team_id = (int) $fallbackTeamId;
                        session(['team_id' => $team_id]);

                        setPermissionsTeamId($team_id);
                        /** @disregard P1013 */
                        $user->unsetRelation('roles')->unsetRelation('permissions');
                        /** @disregard P1013 */
                        $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
                    }
                }
            }

            if (!$is_admin && $team_id > 0) {
                $hasTeamAccess = Team::query()
                    ->whereKey($team_id)
                    ->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->exists();

                if (!$hasTeamAccess) {
                    $team_id = (int) ($teamIds->first() ?? 0);
                    session(['team_id' => $team_id]);

                    setPermissionsTeamId($team_id > 0 ? $team_id : null);
                    /** @disregard P1013 */
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                    /** @disregard P1013 */
                    $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
                }
            }

            if ($team_id > 0) {
                session(['team_id' => $team_id]);
            }

            $teams = $user->getAllTeamNames();
            $permissions = $user->getAllPermissions()->pluck('name')->values()->all();
            $roles = $user->getRoleNames()->values()->all();
            $team = $team_id > 0 ? Team::find($team_id) : null;
            $alerts = $user->userAlerts;
        }
        //dd(method_exists($user, 'can'));
        //dd($user->can('show_permission'));

        //dd($permissions);
        // return [
        //     ...parent::share($request),
        //     'auth' => [
        //         'user' => $user,
        //         'team' => $team ?? null,
        //         'teams' => $teams  ?? [],
        //         'permissions' => $permissions ?? [],
        //         'roles' => $roles ?? [],
        //     ],
        // ];

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'team' => $team ?? null,
                'teams' => $teams  ?? [],
                'permissions' => $permissions ?? [],
                'roles' => $roles ?? [],
                'alerts' => $alerts ?? [],
                'is_admin' => $is_admin,
            ],
            'flash' => [
                'message' => fn() => $request->session()->get('message'),
            ],
        ]);
    }
}
