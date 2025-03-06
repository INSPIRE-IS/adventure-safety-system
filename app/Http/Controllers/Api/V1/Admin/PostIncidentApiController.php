<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostIncidentRequest;
use App\Http\Requests\UpdatePostIncidentRequest;
use App\Http\Resources\Admin\PostIncidentResource;
use App\Models\PostIncident;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostIncidentApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('post_incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userId = auth('api')->user()->id;
        $teamId = auth('api')->user()->teams->first()->id;

        return new PostIncidentResource(PostIncident::select('id', 'due_date', 'complete_date', 'post_incident_schedule_id', 'report_file_id', 'site_id', 'assigned_to_id')
            ->with([
                'post_incident_schedule:id,name,type_incident_id,incident_id,active_date,period_id',
                'post_incident_schedule.type_incident:id,name',
                'post_incident_schedule.incident:id,type_incident_id,site_id,incident_date,description,signature_file_id,incident_file_id',
                'assigned_to:id,name,surname,email,mobile_number',
                'report_file:id,path,type_file_id,row',
                'post_incident_details'
            ])
            //->where('assigned_to_id', $userId)
            //->where('site_id', $teamId)
            ->whereBetween('post_incidents.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get());
    }

    public function store(StorePostIncidentRequest $request)
    {
        $postIncident = PostIncident::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'id' => $request->id,
                'post_incident_schedule_id' => $request->post_incident_schedule_id,
            ],
            [
                'complete_date' => $request->complete_date,
            ]
        );

        return (new PostIncidentResource($postIncident))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PostIncident $postIncident)
    {
        abort_if(Gate::denies('post_incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostIncidentResource($postIncident->load(['post_incident_schedule', 'report_file', 'post_incident_detail']));
    }

    public function update(UpdatePostIncidentRequest $request, PostIncident $postIncident)
    {
        $postIncident->update($request->all());

        return (new PostIncidentResource($postIncident))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PostIncident $postIncident)
    {
        abort_if(Gate::denies('post_incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncident->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
