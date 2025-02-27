<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Team;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        //dd('here');
        //$user = $request->user();
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        setPermissionsTeamId($teamId);
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        // DB::enableQueryLog();
        $users = User::with(['roles', 'permissions', 'teams'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->orderBy($sortColumn, $sortDirection)
            // ->get();
            ->paginate(10);
        //dd(DB::getQueryLog());
        $roles = Role::select('name', 'id')->get(); //->pluck('name', 'id');
        $teams = Team::select('name', 'id')->get(); //->pluck('name', 'id');
        //dd($roles);

        //return view('admin.users.index', compact('users'));
        /** @disregard P1013 */
        return Inertia::render('User/Index', [
            'users' => $users,
            'roles' => $roles,
            'teams' => $teams,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'user_show' => false, //auth()->user()->can('user_show'),
                'user_create' => auth()->user()->can('user_create'),
                'user_edit' => auth()->user()->can('user_edit'),
                'user_delete' => auth()->user()->can('user_delete'),

            ],
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $teams = $request->input('teams', []);
        $roles = $request->input('roles', []);

        $user_data = $request->all();
        $user_data['password'] = bcrypt('password'); //($request->input('password'));
        //'password' => bcrypt('password')
        $user = User::create($user_data);
        $user->teams()->sync($request->input('teams', []));
        foreach ($roles as $roleId) {
            foreach ($teams as $teamId) {
                $user->roles()->attach($roleId, ['team_id' => $teamId]);
            }
        }
        //$user->roles()->sync($request->input('roles', []));
        return redirect()->route('users.index');
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $teams = $request->input('teams', []);
        // Get the roles from the request
        $roles = $request->input('roles', []);

        $user->update($request->all());
        $user->teams()->sync($teams);
        // Detach all roles and teams first
        //dd($user->roles()->get());
        //$user->roles()->detach();
        DB::table('model_has_roles')->where('model_id', $user->id)->delete();
        // Sync roles with team_id included
        //dd($roles, $teams);
        foreach ($roles as $roleId) {
            foreach ($teams as $teamId) {
                $user->roles()->attach($roleId, ['team_id' => $teamId]);
                // $user->roles()->syncWithoutDetaching([
                //     $roleId => ['team_id' => $teamId]  // Sync role with team_id

                // ]);
            }
        }
        //$user->roles()->sync($request->input('roles', []));
        //$user->teams()->sync($request->input('teams', []));

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        //abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->load('roles', 'team', 'userUserAlerts');

        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        //abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return back();
    }
}
