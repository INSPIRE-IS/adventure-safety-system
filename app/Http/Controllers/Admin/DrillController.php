<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillRequest;
use App\Http\Requests\UpdateDrillRequest;
use App\Models\Drill;
use App\Models\DrillMember;
use App\Models\DrillSchedule;
use App\Models\File;
use App\Models\InspectionSchedule;
use App\Models\Team;
use App\Models\TypeDrill;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class DrillController extends Controller
{
    public function index(Request $request)
    {

        /** @disregard P1013 */
        abort_if(!auth()->user()->can('drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'complete_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $user = auth()->user();
        //dd($user->hasAnyRole(['admin', 'super']));
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());

        $drills = Drill::with(['drill_schedule', 'assigned_to', 'photo_file', 'signature_file'])
            //->where('complete_date', 'like', '%' . $searchTerm . '%')
            ->where('site_id', $teamId)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);

        $sites = Team::select('name as label', 'id as value')->get();
        $drill_schedules = DrillSchedule::select('name as label', 'id as value')->get();
        $typeDrills = TypeDrill::select('name as label', 'id as value')->get();
        setPermissionsTeamId($teamId);
        $users = User::select('id', 'name', 'surname', 'updated_at')
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId);
            })
            ->whereHas('roles', function ($query) {
                $query->whereIn('role_id', [4, 5, 6]); //
            })
            ->with(['teams:id,name']) // Load only role ID & name
            ->with(['teams.type_activities:id,name']) // Load only role ID & name
            ->with(['roles:id,name']) // Load only role ID & name
            ->get();
        // $users = User::with('teams.type_activities', 'roles')
        //     ->whereHas('teams', function ($query) use ($teamId) {
        //         $query->where('team_id', $teamId);
        //     })
        //     ->whereHas('roles', function ($query) {
        //         $query->whereIn('role_id', [4, 5, 6]); //
        //     })
        //     ->get();
        //$teams = Team::with('type_activities')->get(); //['teams.id', 'teams.name']
        //dd($users);
        $assigned_to = User::select('name as label', 'id as value')->get();
        $periodSelects = InspectionSchedule::PERIOD_SELECT;
        $periods = [];
        foreach ($periodSelects as $value => $label) {
            $periods[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }
        $actives = array(
            [
                'value' => "1",
                'label' => "No",
            ],
            [
                'value' => "2",
                'label' => "Yes",
            ]
        );


        /** @disregard P1013 */
        return Inertia::render('Drill/Index', [
            'drills' => $drills,
            'sites' => $sites,
            'drillSchedules' => $drill_schedules,
            'assignedTo' => $assigned_to,
            'typeDrills' => $typeDrills,
            'periods' => $periods,
            'actives' => $actives,
            'users' => $users,
            //'signature_files' => $signature_files,
            //'photoFiles' => $photo_files,
            'can' => [
                'drill_show' => auth()->user()->can('drill_show'),
                'drill_edit' => false, //auth()->user()->can('drill_edit'),
                'drill_delete' => false, //auth()->user()->can('drill_delete'),
                'drill_create' => true, //auth()->user()->can('drill_create'),
                'drill_edit_reminder' => false, //auth()->user()->can('drill_create'),
                'drill_edit_inspector' => false, //auth()->user()->can('drill_create'),
                'drill_edit_date' => false, //auth()->user()->can('drill_create'),
            ],
        ]);
    }


    public function store(StoreDrillRequest $request)
    {
        $drill = Drill::create($request->all());

        return redirect()->route('drills.index');
    }


    public function update(Request $request, Drill $drill)
    {
        $drill->update($request->all());

        return redirect()->route('admin.drills.index');
    }

    public function show(Drill $drill)
    {
        //dd($drill);
        // abort_if(Gate::denies('drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());

        $drill->load('drill_details', 'drill_schedule', 'assigned_to', 'drill_members', 'type_drill');

        setPermissionsTeamId($teamId);
        $users = User::whereHas('teams', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['manager', 'inspector']);
            })
            ->select('name as label', 'id as value')->get();
        return Inertia::render('Drill/Show', [
            'drill' => $drill,
            'drill_id' => $drill->id,
            'category_id' => $drill->type_drill->id,
            'users' => $users,
            //'signature_files' => $signature_files,
            //'photoFiles' => $photo_files,
            'can' => [
                'drill_edit_reminder' => true, //auth()->user()->can('drill_create'),
                'drill_edit_inspector' => true, //auth()->user()->can('drill_create'),
                'drill_edit_date' => true, //auth()->user()->can('drill_create'),
            ],
        ]);
    }

    public function destroy(Drill $drill)
    {
        //abort_if(Gate::denies('drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drill->delete();

        return back();
    }
}
