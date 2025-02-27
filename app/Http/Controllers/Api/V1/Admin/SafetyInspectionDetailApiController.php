<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSafetyInspectionDetailRequest;
use App\Http\Requests\UpdateSafetyInspectionDetailRequest;
use App\Http\Resources\Admin\SafetyInspectionDetailResource;
use App\Models\SafetyInspectionDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SafetyInspectionDetailApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('safety_inspection_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SafetyInspectionDetailResource(SafetyInspectionDetail::with(['site', 'inspection', 'category_item', 'category_detail'])->get());
    }

    public function store(StoreSafetyInspectionDetailRequest $request)
    {
        //dd($request->all());
        //$safetyInspectionDetail = SafetyInspectionDetail::create($request->all());
        $safetyInspectionDetail = SafetyInspectionDetail::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'inspection_id' => $request->inspection_id,
                'category_item_id' => $request->category_item_id,
                'category_detail_id' => $request->category_detail_id,
            ],
            [
                'condition' => $request->condition,
            ]
        );

        return (new SafetyInspectionDetailResource($safetyInspectionDetail))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(SafetyInspectionDetail $safetyInspectionDetail)
    {
        abort_if(Gate::denies('safety_inspection_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new SafetyInspectionDetailResource($safetyInspectionDetail->load(['site', 'inspection', 'category_item', 'category_detail']));
    }

    public function update(UpdateSafetyInspectionDetailRequest $request, SafetyInspectionDetail $safetyInspectionDetail)
    {
        $safetyInspectionDetail->update($request->all());

        return (new SafetyInspectionDetailResource($safetyInspectionDetail))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(SafetyInspectionDetail $safetyInspectionDetail)
    {
        abort_if(Gate::denies('safety_inspection_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $safetyInspectionDetail->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
