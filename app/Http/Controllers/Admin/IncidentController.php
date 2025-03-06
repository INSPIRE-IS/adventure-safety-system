<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyIncidentRequest;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Models\File;
use App\Models\Incident;
use App\Models\Team;
use App\Models\TypeIncident;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'description'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');

        $user = auth()->user();
        //dd($user->hasAnyRole(['admin', 'super']));
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());

        $incidents  = Incident::with(['site', 'type_incident', 'signature_file', 'incident_file'])
            ->where('description', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->where('site_id', $teamId)
            ->paginate(10);

        $sites = Team::select('name as label', 'id as value')->get();

        $typeIncidents = TypeIncident::select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('Incident/Index', [
            'incidents' => $incidents,
            'sites' => $sites,
            'typeIncidents' => $typeIncidents,
            'can' => [
                'incident_show' => auth()->user()->can('incident_show'),
                'incident_create' => false, // auth()->user()->can('incident_create'),
                'incident_edit' => false, //  auth()->user()->can('incident_edit'),
                'incident_delete' => false, //  auth()->user()->can('incident_delete'),

            ],
        ]);
    }

    public function create()
    {
        //abort_if(Gate::denies('incident_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sites = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $type_incidents = TypeIncident::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signature_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.incidents.create', compact('signature_files', 'sites', 'type_incidents'));
    }

    public function store(StoreIncidentRequest $request)
    {
        $incident = Incident::create($request->all());


        return redirect()->route('admin.incidents.index');
    }

    public function edit(Incident $incident)
    {
        //abort_if(Gate::denies('incident_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sites = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $type_incidents = TypeIncident::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signature_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $incident->load('site', 'type_incident', 'signature_file', 'incident_file', 'team');

        return view('admin.incidents.edit', compact('incident', 'signature_files', 'sites', 'type_incidents'));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        $incident->update($request->all());

        return redirect()->route('admin.incidents.index');
    }

    public function show(Incident $incident)
    {
        //abort_if(Gate::denies('incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incident->load('site', 'type_incident', 'signature_file', 'incident_file', 'team', 'incidentIncidentInstructors');

        return view('admin.incidents.show', compact('incident'));
    }

    public function destroy(Incident $incident)
    {
        //abort_if(Gate::denies('incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incident->delete();

        return back();
    }
}
