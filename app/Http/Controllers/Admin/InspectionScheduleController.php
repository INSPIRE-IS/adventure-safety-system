<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionScheduleRequest;
use App\Http\Requests\StoreInspectionScheduleRequest;
use App\Http\Requests\UpdateInspectionScheduleRequest;
use App\Models\CategoryItem;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\TypeInspection;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class InspectionScheduleController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('inspection_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $data = $request->get('data', '');
        //dd($data);
        $type_items = $data == 1 ? [1, 2] : [3];
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        setPermissionsTeamId($teamId);
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $inspectionSchedules = InspectionSchedule::with(['site', 'type_inspection', 'user', 'assigned_by', 'category_items'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->whereIn('type_inspection_id', $type_items)
            ->when($teamId, function ($query, $teamId) {
                $query->where('site_id', $teamId); // Filter by site_id if it's provided
            })
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

        $sites = Team::select('name as label', 'id as value')->get();
        // $site_type_activities = Team::where('id', $teamId)
        //     ->select('name', 'id')
        //     ->with(['type_activities:id  as value,name as label'])->first();
        //$site = Team::find($teamId)->select('name as label', 'id as value');
        $typeActivities = TypeActivity::whereHas('teams', function ($query) use ($teamId) {
            $query->where('id', $teamId);
        })->select('id as value', 'name as label')->get();
        //dd($typeActivities);
        //$type_activities = Team::->get();


        $type_inspections = TypeInspection::whereIn('id', $type_items)->select('name as label', 'id as value')->get();
        //$typeInspections = TypeInspection::select('name as label', 'id as value')->where('name', '!=', 'PPE Inspections')->get();

        $categoryItems = CategoryItem::whereHas('site', function ($query) use ($teamId) {
            $query->where('site_id', $teamId); // Replace $teamId with the actual team ID you're filtering by
        })->get();

        $users = User::with(['roles', 'teams'])
            ->whereHas('teams', function ($query) use ($teamId) {
                $query->where('team_id', $teamId); // Replace $teamId with the actual team ID you're filtering by
            })
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['super', 'admin', 'author']);
            })
            // unless user has this role do not show users with this role
            // ->when(!$user->hasAnyRole(['director']), function ($query) { // 'author', 
            //     $query->whereDoesntHave('roles', function ($subQuery) {
            //         $subQuery->whereIn('name', ['director']);
            //     });
            // })
            ->select('name as label', 'id as value')->get();

        $periodSelects = InspectionSchedule::PERIOD_SELECT;
        $periods = [];
        foreach ($periodSelects as $value => $label) {
            $periods[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }
        //dd($users);
        /** @disregard P1013 */
        return Inertia::render('InspectionSchedule/Index', [
            'inspectionSchedules' => $inspectionSchedules,
            'sites' => $sites,
            'typeActivities' => $typeActivities,
            'periods' => $periods,
            'typeInspections' => $type_inspections,
            'categoryItems' => $categoryItems,
            'users' => $users,
            'data' => $data,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'inspection_schedule_show' => auth()->user()->can('inspection_schedule_show'),
                'inspection_schedule_edit' => auth()->user()->can('inspection_schedule_edit'),
                'inspection_schedule_delete' => auth()->user()->can('inspection_schedule_delete'),
                'inspection_schedule_create' => auth()->user()->can('inspection_schedule_create'),
            ],
        ]);
    }


    public function store(StoreInspectionScheduleRequest $request)
    {
        //dd($request);
        $data = $request->all();
        $data['active_date'] = Carbon::parse($data['active_date'])->format('Y-m-d');
        $inspectionSchedule = InspectionSchedule::create($data);
        //$inspectionSchedule->category_items()->sync($request->input('category_items', []));
        return redirect()->back()->with('success', 'Added successfully.');
        //return redirect()->route('admin.inspection-schedules.index');

        // return redirect()->route('inspection-schedules.index')
        //     ->with('message', ['info' => 'Application redirect year does not match id: ' . $inspectionSchedule->id]);
    }
    //     Carbon::parse($value)->format('Y-m-d') 
    //     $inspectionSchedule = InspectionSchedule::create($request->all());
    //     $inspectionSchedule->category_items()->sync($request->input('category_items', []));

    //     return redirect()->route('admin.inspection-schedules.index');
    // }


    public function update(UpdateInspectionScheduleRequest $request, InspectionSchedule $inspectionSchedule)
    {
        $data = $request->all();
        $data['active_date'] = Carbon::parse($data['active_date'])->format('Y-m-d');
        $inspectionSchedule->update($data);
        //$inspectionSchedule->category_items()->sync($request->input('category_items', []));
        return redirect()->back()->with('success', 'Added successfully.');
        //return redirect()->route('inspection-schedules.index');
    }

    public function show(InspectionSchedule $inspectionSchedule)
    {
        //abort_if(Gate::denies('inspection_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionSchedule->load('site', 'type_inspection', 'user', 'assigned_by', 'category_items');

        return view('inspectionSchedules.show', compact('inspectionSchedule'));
    }

    public function destroy(InspectionSchedule $inspectionSchedule)
    {
        //abort_if(Gate::denies('inspection_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionSchedule->delete();

        return back();
    }
}
