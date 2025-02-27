<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Models\CategoryItem;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\Team;
use App\Models\TypeInspection;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'due_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $inspections = Inspection::with(['inspection_schedule', 'assigned_to', 'photo_file', 'signature_file'])
            //->where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

        $inspection_schedules = InspectionSchedule::select('name as label', 'id as value')->get();

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        //$photo_files = File::pluck('path', 'id')->prepend(trans('global.pleaseSelect'), '');

        //$signature_files = File::pluck('path', 'id')->prepend(trans('global.pleaseSelect'), '');


        $sites = Team::select('name as label', 'id as value')->get();
        $typeInspections = TypeInspection::select('name as label', 'id as value')->get();
        $categoryItems = CategoryItem::select('name as label', 'id as value')->get();
        $users = User::whereHas('teams', function ($query) use ($teamId) {
            $query->where('team_id', $teamId); // Replace $teamId with the actual team ID you're filtering by
        })
            ->select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('Inspection/Index', [
            'inspections' => $inspections,
            'sites' => $sites,
            'typeInspections' => $typeInspections,
            'categoryItems' => $categoryItems,
            'users' => $users,
            'can' => [
                'inspection_show' => auth()->user()->can('inspection_show'),
                'inspection_edit' => false, //auth()->user()->can('inspection_edit'),
                'inspection_delete' => false, //auth()->user()->can('inspection_delete'),
                'inspection_create' => false, //auth()->user()->can('inspection_create'),
                'inspection_edit_reminder' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_inspector' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_date' => true, //auth()->user()->can('inspection_create'),
            ],
        ]);
    }


    public function store(StoreInspectionRequest $request)
    {
        $inspection = Inspection::create($request->all());

        return redirect()->route('admin.inspections.index');
    }

    public function update(UpdateInspectionRequest $request, Inspection $inspection)
    {
        $inspection->update($request->all());

        return redirect()->route('admin.inspections.index');
    }

    public function show(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->load('inspection_schedule', 'assigned_to');

        return view('admin.inspections.show', compact('inspection'));
    }

    public function destroy(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->delete();

        return back();
    }
}
