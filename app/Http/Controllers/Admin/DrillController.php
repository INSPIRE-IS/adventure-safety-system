<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillRequest;
use App\Http\Requests\UpdateDrillRequest;
use App\Models\Drill;
use App\Models\DrillMember;
use App\Models\DrillSchedule;
use App\Models\File;
use App\Models\Team;
use App\Models\TypeDrill;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
        $drills = Drill::with(['drill_schedule', 'assigned_to', 'photo_file', 'signature_file'])
            //->where('complete_date', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);
        $sites = Team::select('name as label', 'id as value')->get();
        $drill_schedules = DrillSchedule::select('name as label', 'id as value')->get();

        $assigned_to = User::select('name as label', 'id as value')->get();

        //$photo_files = File::select('name as label', 'id as value')->get();

        //$signature_files = File::select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('Drill/Index', [
            'drills' => $drills,
            'sites' => $sites,
            'drillSchedules' => $drill_schedules,
            'assignedTo' => $assigned_to,
            //'signature_files' => $signature_files,
            //'photoFiles' => $photo_files,
            'can' => [
                'drill_show' => auth()->user()->can('drill_show'),
                'drill_edit' => false, //auth()->user()->can('drill_edit'),
                'drill_delete' => false, //auth()->user()->can('drill_delete'),
                'drill_create' => false, //auth()->user()->can('drill_create'),
                'drill_edit_reminder' => true, //auth()->user()->can('drill_create'),
                'drill_edit_inspector' => true, //auth()->user()->can('drill_create'),
                'drill_edit_date' => true, //auth()->user()->can('drill_create'),
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
        // abort_if(Gate::denies('drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drill->load('site', 'drill', 'instructor', 'signature_file');

        return view('admin.drills.show', compact('drill'));
    }

    public function destroy(Drill $drill)
    {
        //abort_if(Gate::denies('drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drill->delete();

        return back();
    }
}
