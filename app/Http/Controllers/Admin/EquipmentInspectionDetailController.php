<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyEquipmentInspectionDetailRequest;
use App\Http\Requests\StoreEquipmentInspectionDetailRequest;
use App\Http\Requests\UpdateEquipmentInspectionDetailRequest;
use App\Models\EquipmentInspectionDetail;
use App\Models\InspectionUserAssigned;
use App\Models\Team;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipmentInspectionDetailController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('equipment_inspection_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $equipmentInspectionDetails = EquipmentInspectionDetail::with(['inspection', 'site'])->get();

        return view('admin.equipmentInspectionDetails.index', compact('equipmentInspectionDetails'));
    }


    public function store(StoreEquipmentInspectionDetailRequest $request)
    {
        $equipmentInspectionDetail = EquipmentInspectionDetail::create($request->all());

        return redirect()->route('admin.equipment-inspection-details.index');
    }


    public function update(UpdateEquipmentInspectionDetailRequest $request, EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        $equipmentInspectionDetail->update($request->all());

        return redirect()->route('admin.equipment-inspection-details.index');
    }

    public function show(EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        //abort_if(Gate::denies('equipment_inspection_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $equipmentInspectionDetail->load('inspection', 'site');

        return view('admin.equipmentInspectionDetails.show', compact('equipmentInspectionDetail'));
    }

    public function destroy(EquipmentInspectionDetail $equipmentInspectionDetail)
    {
        //abort_if(Gate::denies('equipment_inspection_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $equipmentInspectionDetail->delete();

        return back();
    }
}
