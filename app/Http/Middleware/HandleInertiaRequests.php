<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Illuminate\Http\Request;
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
            $team_id = (int) session()->get('team_id');

            if ($team_id <= 0) {
                $team_id = (int) (optional($user)->teams->first()?->id ?? 0);
                if ($team_id > 0) {
                    session(['team_id' => $team_id]);
                }
            }

            setPermissionsTeamId($team_id > 0 ? $team_id : null);

            /** @disregard P1013 */
            $user->unsetRelation('roles')->unsetRelation('permissions');

            /** @disregard P1013 */
            $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);

            if (!$is_admin && $team_id > 0) {
                $hasTeamAccess = Team::query()
                    ->whereKey($team_id)
                    ->whereHas('users', function ($q) use ($user) {
                        $q->where('users.id', $user->id);
                    })
                    ->exists();

                if (!$hasTeamAccess) {
                    $team_id = (int) (optional($user)->teams->first()?->id ?? 0);
                    session(['team_id' => $team_id]);

                    setPermissionsTeamId($team_id > 0 ? $team_id : null);
                    /** @disregard P1013 */
                    $user->unsetRelation('roles')->unsetRelation('permissions');
                    /** @disregard P1013 */
                    $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
                }
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
