<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostIncidentDetailRequest;
use App\Http\Requests\UpdatePostIncidentDetailRequest;
use App\Http\Resources\Admin\PostIncidentDetailResource;
use App\Models\PostIncidentDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostIncidentDetailApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('equipment_inspection_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostIncidentDetailResource(PostIncidentDetail::with(['site', 'inspection', 'category_item', 'category_detail'])->get());
    }

    public function store(StorePostIncidentDetailRequest $request)
    {
        $postIncidentDetail = PostIncidentDetail::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'post_incident_id' => $request->post_incident_id,
                'question_id' => $request->question_id,

            ],
            [
                'answer' => $request->answer,
            ]
        );

        return (new PostIncidentDetailResource($postIncidentDetail))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(PostIncidentDetail $postIncidentDetail)
    {
        abort_if(Gate::denies('equipment_inspection_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new PostIncidentDetailResource($postIncidentDetail->load(['site', 'inspection', 'category_item', 'category_detail']));
    }

    public function update(UpdatePostIncidentDetailRequest $request, PostIncidentDetail $postIncidentDetail)
    {
        $postIncidentDetail->update($request->all());

        return (new PostIncidentDetailResource($postIncidentDetail))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(PostIncidentDetail $postIncidentDetail)
    {
        abort_if(Gate::denies('equipment_inspection_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncidentDetail->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
