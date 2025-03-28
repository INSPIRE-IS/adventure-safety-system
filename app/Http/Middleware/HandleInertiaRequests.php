<?php

namespace App\Http\Middleware;

use App\Models\Team;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

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
        $user = $request->user();
        Log::info('Inertia Request');
        Log::info('Request method: ' . $request->method());
        //$team_id = $user->currentTeam()->get();

        // okay so basically we get the user and team then we set the permission based on the team
        // or we need to check in this array if the user has permission to access the team
        // for now we just hard coding the admin and team 
        //$user = User::find(1);
        //dd($request->user());

        //dd($user);
        //$permissions = $user->getAllPermissions()->pluck('name');
        //dd($permissions);
        //$roles = $user->getRoleNames()->pluck('name');
        //dd($roles);
        // Logic for filtering teams
        $is_admin = false;
        if ($user) {
            Log::info('User: ' . $user->id);
            //$team_id = Session::get('team_id');
            $team_id = session()->get('team_id');
            if ($team_id == null) {
                // $team = Team::find($teamId);
                Log::info('update team id inertia: ' . $team_id);
                //$is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
                $team_id = optional($user)->teams->first()->id ?? 0;
                // if ($team_id == 0 && $is_admin) {
                //     $team_id = 1;
                // }
                //dd($is_admin);
                setPermissionsTeamId(1);
            }
            Log::info('team_id: ' . $team_id);
            //session(['team_id' => $team_id]);
            setPermissionsTeamId($team_id);

            //$user->teams;
            //$user->permissions;
            // $user->roles;
            $teams = $user->getAllTeamNames(); //->pluck('name', 'id')->toArray();
            // $items = collect($teams)->map(function ($name, $id) {
            //     return (object)[
            //         'id' => $id,
            //         'name' => $name
            //     ];
            // });
            //$teams = Team::all()->pluck('id', 'name');
            //dd($teams);
            $permissions = $user->getAllPermissions()->pluck('name');

            $roles = $user->getRoleNames()->pluck('name');
            //$permissions = $user->getAllPermissionsAttribute();
            //dd($permissions);
            $team = Team::find($team_id); // with('type_activities')->
            $alerts = $user->userAlerts;
            //dd($alerts);
            $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
            //dd($team);
            if ($user->isAdminOfTeam(1) || $user->id === 2) {
                //setPermissionsTeamId(2);
                //setPermissionsTeamId(null);
                // Admin of Team 1 or user ID 2: Show all teams
                // $teams = Team::all(); // Assuming you have a Team model
                // //$permissions = Permission::all(); // Should list your permissions
                // //$roles = Role::all();       // Should list your roles
                // //$teams = $user->getAllTeamNames()->pluck('name');
                // $permissions = $user->getAllPermissions()->pluck('name');
                // $roles = $user->getRoleNames()->pluck('name'); // Assuming you have a Team model
            } else {
                // Filter teams the user is linked to
                // Log::info('User: no user');
                // $teams = $user->teams; // Assuming a "teams" relationship exists on the User model
                // dd($teams);
                // //$permissions = $user->getAllPermissions()->pluck('name'); // Assuming a "teams" relationship exists on the User model
                // $permissions = $user->getAllPermissions()->pluck('name');
                // $roles = $user->getRoleNames()->pluck('name'); // Assuming you have a Team model
            }
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
