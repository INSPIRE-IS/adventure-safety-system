<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeIncidentRequest;
use App\Http\Requests\UpdateTypeIncidentRequest;
use App\Http\Resources\Admin\TypeIncidentResource;
use App\Models\TypeIncident;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeIncidentApiController extends Controller
{
    public function index()
    {
        // $user = auth()->user();
        // dd($user->can('type_incident_access'));
        abort_if(Gate::denies('type_incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return new TypeIncidentResource(TypeIncident::all());
    }

    public function store(StoreTypeIncidentRequest $request)
    {
        $typeIncident = TypeIncident::create($request->all());

        return (new TypeIncidentResource($typeIncident))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TypeIncident $typeIncident)
    {
        abort_if(Gate::denies('type_incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeIncidentResource($typeIncident);
    }

    public function update(UpdateTypeIncidentRequest $request, TypeIncident $typeIncident)
    {
        $typeIncident->update($request->all());

        return (new TypeIncidentResource($typeIncident))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TypeIncident $typeIncident)
    {
        abort_if(Gate::denies('type_incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeIncident->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
