<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Resources\Admin\IncidentResource;
use App\Models\Incident;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidentApiController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncidentResource(Incident::with(['site', 'type_incident', 'signature_file', 'incident_file', 'team'])->get());
    }

    public function store(StoreIncidentRequest $request)
    {
        $incident = Incident::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
                'type_incident_id' => $request->type_incident_id,
                'uuid' => $request->id,
            ],
            [
                'incident_date' => $request->incident_date,
                'instructor_one_name' => $request->instructor_one_name,
                'instructor_two_name' => $request->instructor_two_name,
                'description' => $request->description,
                // 'incident_file_id' => $request->incident_file_id,
                // 'signature_file_id' => $request->signature_file_id,
            ]
        );

        return (new IncidentResource($incident))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Incident $incident)
    {
        abort_if(Gate::denies('incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncidentResource($incident->load(['site', 'type_incident', 'signature_file', 'incident_file', 'team']));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        $incident->update($request->all());

        return (new IncidentResource($incident))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Incident $incident)
    {
        abort_if(Gate::denies('incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incident->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
