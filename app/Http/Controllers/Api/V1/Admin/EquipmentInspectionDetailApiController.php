<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEquipmentInspectionDetailRequest;
use App\Http\Requests\UpdateEquipmentInspectionDetailRequest;
use App\Http\Resources\Admin\EquipmentInspectionDetailResource;
use App\Models\EquipmentInspectionDetail;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipmentInspectionDetailApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('equipment_inspection_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EquipmentInspectionDetailResource(EquipmentInspectionDetail::with(['site', 'inspection', 'category_item', 'category_detail'])->get());
    }

    public function store(StoreEquipmentInspectionDetailRequest $request)
    {
        $equipmentInspectionDetail = EquipmentInspectionDetail::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'inspection_id' => $request->inspection_id,
                'category_item_id' => $request->category_item_id,
                'category_detail_id' => $request->category_detail_id,
                'identity' => $request->id,
            ],
            [
                'condition' => $request->condition,
            ]
        );

        return (new EquipmentInspectionDetailResource($equipmentInspectionDetail))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        abort_if(Gate::denies('equipment_inspection_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EquipmentInspectionDetailResource($equipmentInspectionDetail->load(['site', 'inspection', 'category_item', 'category_detail']));
    }

    public function update(UpdateEquipmentInspectionDetailRequest $request, EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        $equipmentInspectionDetail->update($request->all());

        return (new EquipmentInspectionDetailResource($equipmentInspectionDetail))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        abort_if(Gate::denies('equipment_inspection_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $equipmentInspectionDetail->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
