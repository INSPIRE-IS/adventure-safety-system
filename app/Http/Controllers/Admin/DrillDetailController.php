<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDrillDetailRequest;
use App\Http\Requests\StoreDrillDetailRequest;
use App\Http\Requests\UpdateDrillDetailRequest;
use App\Models\Drill;
use App\Models\DrillDetail;
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

class DrillDetailController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('drill_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'complete_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $drillDetails = DrillDetail::with(['site', 'drill', 'instructor', 'signature_file'])
            ->where('complete_date', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);
        $sites = Team::select('name as label', 'id as value')->get();

        $drills = Drill::select('due_date as label', 'id as value')->get();

        $instructors = User::select('name as label', 'id as value')->get();
        $members = User::all();

        $signature_files = File::select('file_name as label', 'id as value')->get();

        $competentSelects = DrillDetail::COMPETENT_SELECT;
        $competents = [];
        foreach ($competentSelects as $value => $label) {
            $competents[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }

        /** @disregard P1013 */
        return Inertia::render('DrillDetail/Index', [
            'drills' => $drills,
            'sites' => $sites,
            'drills' => $drills,
            'instructors' => $instructors,
            'signature_files' => $signature_files,
            'competents' => $competents,
            'members' => $members,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'drill_detail_show' => auth()->user()->can('drill_detail_show'),
                'drill_detail_edit' => auth()->user()->can('drill_detail_edit'),
                'drill_detail_delete' => auth()->user()->can('drill_detail_delete'),
                'drill_detail_create' => auth()->user()->can('drill_detail_create'),
            ],
        ]);
    }


    public function store(StoreDrillDetailRequest $request)
    {
        //$drillDetail = DrillDetail::create($request->all());
        //dd($request);
        $data = $request->all();
        $memberIds = $data['memberIds'];
        // change this to drill detail
        $drill = Drill::create([
            'site_id' => $data['site_id'],
            'instructor_id' => $data['instructor_id'],
            'complete_date' => $data['complete_date'],
            'signature_file_id' => $data['signature_file_id'],
            'competent' => $data['competent'],
            'type_drill_id' => $data['type_drill_id'],
        ]);
        //$drill->instructor()->associate(auth()->user());
        foreach ($memberIds as $memberid) {
            DrillMember::create([
                'drill_id' => $drill->id,
                'user_id' => $memberid,
                'site_id' => $drill->site_id,
                "competent" => $drill->competent,
            ]);
        }

        // Files
        $file = File::create([
            'table_name' => $data['signature_file_id'],
            'file_name' => $data['signature_file_id'],
            'row' => $drill->id,
            'path' => $data['signature_file_id'],
            'site_id' => $drill->site_id,
            'type_file_id' => 3, // Signature File
        ]);


        // DrillFile
        $drill['signature_file_id'] = $file->id;
        $drill->save;

        // DrillSchedule
        $drillSchedule = DrillSchedule::create([
            'name' => '',
            'period_id' => 11, // DUE IN 11 MONTHS FROM NOW
            'active_date' => $data['complete_date'],
            'site_id' => $drill->site_id,
            'type_drill_id' => $drill->type_drill_id,
            'assigned_to_id' => $data['assigned_to_id'],
            'assigned_by_id' => auth()->user()->id,
            'drill_id' => $drill->id,
        ]);

        // update drills and save the drill that was scheduled to be completed on the due date so this can appear on calendar

        return redirect()->route('drill-details.index');
    }


    public function update(UpdateDrillDetailRequest $request, DrillDetail $drillDetail)
    {
        $drillDetail->update($request->all());

        return redirect()->route('admin.drill-details.index');
    }

    public function show(DrillDetail $drillDetail)
    {
        abort_if(Gate::denies('drill_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillDetail->load('site', 'instructor', 'signature_file', 'type_drill');

        return view('admin.drillDetails.show', compact('drillDetail'));
    }

    public function destroy(DrillDetail $drillDetail)
    {
        abort_if(Gate::denies('drill_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillDetail->delete();

        return back();
    }
}
