<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPostIncidentScheduleRequest;
use App\Http\Requests\StorePostIncidentScheduleRequest;
use App\Http\Requests\UpdatePostIncidentScheduleRequest;
use App\Models\File;
use App\Models\Incident;
use App\Models\PostIncidentSchedule;
use App\Models\Team;
use App\Models\TypeIncident;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PostIncidentScheduleController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('post_incident_schedule_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $postIncidentSchedules = PostIncidentSchedule::with(['site', 'type_incident', 'incident', 'assigned_by', 'assigned_to'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);


        $sites = Team::select('name', 'id')->get(); //pluck('incident_date', 'id')->prepend(trans('global.pleaseSelect'), '');
        $type_incident = TypeIncident::select('name', 'id')->get(); //pluck('incident_date', 'id')->prepend(trans('global.pleaseSelect'), '');
        $insidents = Incident::select('description', 'id')->get(); //pluck('incident_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_by = User::select('name', 'id')->get(); //pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::select('name', 'id')->get(); //pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $periodSelects = PostIncidentSchedule::PERIOD_SELECT;
        $periods = [];
        foreach ($periodSelects as $value => $label) {
            $periods[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }

        /** @disregard P1013 */
        return Inertia::render('PostIncidentSchedule/Index', [
            'postIncidentSchedules' => $postIncidentSchedules,
            'type_incident' => $type_incident,
            'incidents' => $insidents,
            'users' => $users,
            'periods' => $periods,
            'sites' => $sites,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'post_incident_schedule_show' => false, //auth()->user()->can('post_incident_schedule_show'),
                'post_incident_schedule_create' => false, //auth()->user()->can('post_incident_schedule_create'),
                'post_incident_schedule_edit' => auth()->user()->can('post_incident_schedule_edit'),
                'post_incident_schedule_delete' => auth()->user()->can('post_incident_schedule_delete'),
            ],
        ]);
    }

    public function create()
    {
        //abort_if(Gate::denies('post_incident_schedule_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $insidents = Incident::pluck('incident_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signature_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.postIncidentSchedules.create', compact('assigned_bies', 'assigned_tos', 'insidents', 'signature_files'));
    }

    public function store(StorePostIncidentScheduleRequest $request)
    {
        $postIncidentSchedule = PostIncidentSchedule::create($request->all());

        return redirect()->route('admin.post-incident-schedules.index');
    }

    public function edit(PostIncidentSchedule $postIncidentSchedule)
    {
        //abort_if(Gate::denies('post_incident_schedule_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $insidents = Incident::pluck('incident_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_bies = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signature_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $postIncidentSchedule->load('insident', 'assigned_by', 'assigned_to', 'signature_file', 'report_file');

        return view('admin.postIncidentSchedules.edit', compact('assigned_bies', 'assigned_tos', 'insidents', 'postIncidentSchedule', 'signature_files'));
    }

    public function update(UpdatePostIncidentScheduleRequest $request, PostIncidentSchedule $postIncidentSchedule)
    {
        $postIncidentSchedule->update($request->all());

        return redirect()->route('admin.post-incident-schedules.index');
    }

    public function show(PostIncidentSchedule $postIncidentSchedule)
    {
        //abort_if(Gate::denies('post_incident_schedule_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncidentSchedule->load('insident', 'assigned_by', 'assigned_to', 'signature_file', 'report_file');

        return view('admin.postIncidentSchedules.show', compact('postIncidentSchedule'));
    }

    public function destroy(PostIncidentSchedule $postIncidentSchedule)
    {
        //abort_if(Gate::denies('post_incident_schedule_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncidentSchedule->delete();

        return back();
    }
}
