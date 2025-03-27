<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDrillScheduleRequest;
use App\Http\Requests\StoreDrillScheduleRequest;
use App\Http\Requests\UpdateDrillScheduleRequest;
use App\Models\CategoryItem;
use App\Models\Drill;
use App\Models\DrillSchedule;
use App\Models\InspectionSchedule;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\TypeDrill;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Session;
use Symfony\Component\HttpFoundation\Response;

class DrillScheduleController extends Controller
{
    public function index(Request $request)
    {
        // abort_if(Gate::denies('drill_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // return view('drillSchedules.index', compact('drillSchedules', 'drills', 'teams', 'type_drills', 'users'));

        /** @disregard P1013 */
        abort_if(!auth()->user()->can('drill_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = $request->user();
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        setPermissionsTeamId($teamId);
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $drillSchedules = DrillSchedule::with(['site', 'type_drill', 'assigned_by', 'assigned_to', 'type_activity'])
            // where('name', 'like', '%' . $searchTerm . '%')
            ->when($teamId, function ($query, $teamId) {
                $query->where('site_id', $teamId); // Filter by site_id if it's provided
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        //dd($drillSchedules);
        //$drillSchedules = DrillSchedule::with(['site', 'type_drill', 'assigned_to', 'drill'])->get();

        $teams = Team::get();

        $typeDrills = TypeDrill::select('name as label', 'id as value')->get();

        $users = User::with(['roles', 'teams'])
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Replace $teamId with the actual team ID you're filtering by
            })
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['super', 'admin', 'author']);
            })
            // unless user has this role do not show users with this role
            // ->when(!$user->hasAnyRole(['owner']), function ($query) { // 'author', 
            //     $query->whereDoesntHave('roles', function ($subQuery) {
            //         $subQuery->whereIn('name', ['owner']);
            //     });
            // })
            ->select('name as label', 'id as value')->get();

        $drills = Drill::get();

        $typeActivities = TypeActivity::whereHas('teams', function ($query) use ($teamId) {
            $query->where('id', $teamId);
        })->select('id as value', 'name as label')->get();

        $categoryItems = CategoryItem::whereHas('site', function ($query) use ($teamId) {
            $query->where('site_id', $teamId); // Replace $teamId with the actual team ID you're filtering by
        })->get();

        $periodSelects = InspectionSchedule::PERIOD_SELECT;
        $periods = [];
        foreach ($periodSelects as $value => $label) {
            $periods[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }
        //$sites = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');


        //$assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        //$drills = Drill::pluck('complete_date', 'id')->prepend(trans('global.pleaseSelect'), '');


        /** @disregard P1013 */
        return Inertia::render('DrillSchedule/Index', [
            'drillSchedules' => $drillSchedules,
            'drills' => $drills,
            'teams' => $teams,
            'typeDrills' => $typeDrills,
            'typeActivities' => $typeActivities,
            'categoryItems' => $categoryItems,
            'periods' => $periods,
            'users' => $users,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'drill_schedule_show' => auth()->user()->can('drill_schedule_show'),
                'drill_schedule_edit' => auth()->user()->can('drill_schedule_edit'),
                'drill_schedule_delete' => auth()->user()->can('drill_schedule_delete'),
                'drill_schedule_create' => auth()->user()->can('drill_schedule_create'),
            ],
        ]);
    }


    public function store(StoreDrillScheduleRequest $request)
    {
        //dd($request);
        try {
            $drillSchedule = DrillSchedule::create($request->all());
            return redirect()->back()->with('success', 'Drill Schedule added successfully.');
        } catch (\Exception $e) {
            Log::error($e);
            return redirect()->back()->with('error', 'Error adding Drill Schedule.');
        }
        //$drillSchedule = DrillSchedule::create($request->all());
        //return redirect()->back()->with('success', 'Equipment item added successfully.');
        //return redirect()->route('drill-schedules.index');
    }

    public function update(UpdateDrillScheduleRequest $request, DrillSchedule $drillSchedule)
    {
        $drillSchedule->update($request->all());

        return redirect()->route('drill-schedules.index');
    }

    public function show(DrillSchedule $drillSchedule)
    {
        abort_if(Gate::denies('drill_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillSchedule->load('site', 'type_drill', 'assigned_to', 'drill');

        return view('drillSchedules.show', compact('drillSchedule'));
    }

    public function destroy(DrillSchedule $drillSchedule)
    {
        abort_if(Gate::denies('drill_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillSchedule->delete();

        return back();
    }
}
