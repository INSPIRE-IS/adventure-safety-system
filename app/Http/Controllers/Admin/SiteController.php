<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Role;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\User;
use File;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('site_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');

        $user = auth()->user();
        //dd($user->hasAnyRole(['admin', 'super']));
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());

        $sites  = Team::with(['type_activities:id,name'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->where('id', $teamId)
            // ->when(!$user->hasAnyRole(['admin', 'super']), function ($query) use ($teamId) {
            //     $query->whereHas('teams', function ($subQuery) use ($teamId) {
            //         $subQuery->where('id', $teamId);
            //     });
            // })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(10);

        $typeActivities = TypeActivity::select('name', 'id')->get();
        //dd($typeActivities);
        //$site  = Team::where('owner_id', auth()->user()->id)->first();
        //$roles = Role::get(['id', 'title']);
        //$users = User::where('team_id', $site->id)->get();
        /** @disregard P1013 */
        return Inertia::render('Site/Index', [
            'sites' => $sites,
            'typeActivities' => $typeActivities,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'site_show' => auth()->user()->can('site_show'),
                'site_create' => auth()->user()->can('site_create'),
                'site_edit' => auth()->user()->can('site_edit'),
                'site_delete' => auth()->user()->can('site_delete'),

            ],
        ]);
    }

    public function store(StoreTeamRequest $request)
    {
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        setPermissionsTeamId($teamId);

        $saveImage = false;
        if ($request->hasFile('file')) {

            $request->validate([
                'file' => 'required|file',
            ]);
            $saveImage = true;
        }
        $team = Team::create($request->all());
        $team->type_activities()->sync($request->input('type_activities', []));
        // spatie permission with teams reuires us to first find users with the logged in user's team id
        $teams = Team::pluck('id')->toArray(); // Get all team IDs
        $superUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'super');
        })->get(); // Get all users with the "super" role   
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get(); // Get all users with the "super" role    
        $authorUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'author');
        })->get(); // Get all users with the "author" role      
        $directorUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'director');
        })->get(); // Get all users with the "owner" role   
        $managerUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'manager');
        })->get(); // Get all users with the "owner" role  


        // now we update the team id to assign the new team to these users or no one will be able to see the new team
        setPermissionsTeamId($team->id);
        foreach ($superUsers as $superUser) {
            $superUser->teams()->syncWithoutDetaching($teams); // Add missing teams without removing existing ones
            $superUser->assignRole('super'); // Assign 'super' role for Team
        }

        foreach ($adminUsers as $adminUser) {
            $adminUser->teams()->syncWithoutDetaching($teams); // Add missing teams without removing existing ones
            $adminUser->assignRole('admin'); // Assign 'admin' role for Team
        }

        foreach ($authorUsers as $authorUser) {
            $authorUser->teams()->syncWithoutDetaching($teams); // Add missing teams without removing existing ones
            $authorUser->assignRole('author'); // Assign 'author' role for Team
        }

        foreach ($directorUsers as $directorUser) {
            $directorUser->teams()->syncWithoutDetaching($teams); // Add missing teams without removing existing ones
            $directorUser->assignRole('director'); // Assign 'director' role for Team
        }

        foreach ($managerUsers as $managerUser) {
            $managerUser->teams()->syncWithoutDetaching($teams); // Add missing teams without removing existing ones
            $managerUser->assignRole('manager'); // Assign 'manager' role for Team
        }

        if ($saveImage) {
            $image_file = $request->file('file');

            $fileName = time() . '_' . $image_file->getClientOriginalName();
            $directoryPath = 'uploads/teams/' . $team->id;

            $filePath = $image_file->storeAs($directoryPath, $fileName, 'public');
            $file = File::create([
                'site_id'   => $team->id,
                'table_name' => 'teams',
                'file_name' => $fileName,
                'type_file_id' => 1, // logo
                'row' => $team->id,
                'path' => $filePath,
            ]);

            $team->image = $file->id;
            $team->save();
        }

        return redirect()->route('sites.index');
    }


    public function update(UpdateTeamRequest $request, Team $team)
    {
        $saveImage = false;
        if ($request->hasFile('image')) {

            $request->validate([
                'image' => 'required|file|image',
            ]);
            $saveImage = true;
        }

        $team = Team::find($request->id);
        $team->update($request->all());
        $team->type_activities()->sync($request->input('type_activities', []));

        if ($saveImage) {
            $image_file = $request->file('image');

            $fileName = time() . '_' . $image_file->getClientOriginalName();
            $directoryPath = 'uploads/teams/' . $team->id;

            $filePath = $image_file->storeAs($directoryPath, $fileName, 'public');
            $file = File::create([
                'site_id'   => $team->id,
                'table_name' => 'teams',
                'file_name' => $fileName,
                'type_file_id' => 1, // logo
                'row' => $team->id,
                'path' => $filePath,
            ]);

            $team->image = $file->id;
            $team->save();
        }

        return redirect()->route('sites.index');
    }

    public function show(Team $team)
    {
        //abort_if(Gate::denies('type_drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('site.show', compact('team'));
    }

    public function destroy(Team $team)
    {
        //abort_if(Gate::denies('type_drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $team->delete();

        return back();
    }


    public function change($site_id)
    {

        //$request->validate(['site_id' => 'required']);
        //$teamId = Session::get('team_id'); 
        //dd($site_id);

        $site = Team::find($site_id);
        //dd($site);
        if ($site) {
            session(['team_id' => $site->id]);
            setPermissionsTeamId($site->id);
            Log::info('site controller site id: ' . $site->id);
        } else {
            Log::info('site controller site: none');
        }
        return redirect()->back()->with('message', 'Site changed.');
    }

    public function invite(Request $request)
    {
        $request->validate(['email' => 'email']);
        $team    = Team::where('owner_id', auth()->user()->id)->first();
        $url     = URL::signedRoute('register', ['team' => $team->id]);
        $message = new \App\Notifications\TeamMemberInvite($url);
        Notification::route('mail', $request->input('email'))->notify($message);

        return redirect()->back()->with('message', 'Invite sent.');
    }
}
