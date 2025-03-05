<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillMemberRequest;
use App\Http\Requests\UpdateDrillMemberRequest;
use App\Http\Resources\Admin\DrillMemberResource;
use App\Models\DrillMember;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrillMembersApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('drill_member_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DrillMemberResource(DrillMember::with(['site', 'drill_detail', 'user'])->get());
    }

    public function store(StoreDrillMemberRequest $request)
    {
        $drillMember = DrillMember::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'drill_id' => $request->drill_id,
                'user_id' => $request->user_id,
                'uuid' => $request->id,
            ],
            [
                'member_name' => $request->member_name,
                'competent' => $request->competent,
            ]
        );

        return (new DrillMemberResource($drillMember))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(DrillMember $drillMember)
    {
        abort_if(Gate::denies('drill_member_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DrillMemberResource($drillMember->load(['site', 'drill_detail', 'user']));
    }

    public function update(UpdateDrillMemberRequest $request, DrillMember $drillMember)
    {
        $drillMember->update($request->all());

        return (new DrillMemberResource($drillMember))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(DrillMember $drillMember)
    {
        abort_if(Gate::denies('drill_member_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillMember->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
