<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillDetailRequest;
use App\Http\Requests\UpdateDrillDetailRequest;
use App\Http\Resources\Admin\DrillDetailResource;
use App\Models\DrillDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrillDetailApiController extends Controller
{
    public function index()
    {
        // return response()->json([
        //     'message' => 'Drill Details',
        // ], Response::HTTP_OK);
        //    abort_if(Gate::denies('drill_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DrillDetailResource(DrillDetail::with(['site', 'instructor', 'signature_file', 'type_drill'])->get());
    }

    public function store(StoreDrillDetailRequest $request)
    {
        //dd($request->all());
        $drillDetail = DrillDetail::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'drill_id' => $request->drill_id,
                'instructor_id' => $request->instructor_id,
            ],
            [
                'complete_date' => $request->complete_date,
                'competent' => $request->competent,
            ]
        );

        return (new DrillDetailResource($drillDetail))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(DrillDetail $drillDetail)
    {
        abort_if(Gate::denies('drill_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DrillDetailResource($drillDetail->load(['site', 'instructor', 'signature_file', 'type_drill']));
    }

    public function update(UpdateDrillDetailRequest $request, DrillDetail $drillDetail)
    {
        $drillDetail->update($request->all());

        return (new DrillDetailResource($drillDetail))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(DrillDetail $drillDetail)
    {
        abort_if(Gate::denies('drill_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillDetail->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
