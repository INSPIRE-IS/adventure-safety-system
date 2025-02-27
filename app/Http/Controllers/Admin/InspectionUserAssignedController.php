<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionUserAssignedRequest;
use App\Http\Requests\StoreInspectionUserAssignedRequest;
use App\Http\Requests\UpdateInspectionUserAssignedRequest;
use App\Models\File;
use App\Models\Inspection;
use App\Models\InspectionUserAssigned;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InspectionUserAssignedController extends Controller
{
    public function index()
    {
        //abort_if(Gate::denies('inspection_user_assigned_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionUserAssigneds = InspectionUserAssigned::with(['inspection', 'assigned_to', 'photo', 'signature', 'report_file'])->get();

        return view('admin.inspectionUserAssigneds.index', compact('inspectionUserAssigneds'));
    }

    public function create()
    {
        //abort_if(Gate::denies('inspection_user_assigned_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspections = Inspection::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $photos = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signatures = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $report_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.inspectionUserAssigneds.create', compact('assigned_tos', 'inspections', 'photos', 'report_files', 'signatures'));
    }

    public function store(StoreInspectionUserAssignedRequest $request)
    {
        $inspectionUserAssigned = InspectionUserAssigned::create($request->all());

        return redirect()->route('admin.inspection-user-assigneds.index');
    }

    public function edit(InspectionUserAssigned $inspectionUserAssigned)
    {
        //abort_if(Gate::denies('inspection_user_assigned_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspections = Inspection::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $photos = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $signatures = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $report_files = File::pluck('file_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $inspectionUserAssigned->load('inspection', 'assigned_to', 'photo', 'signature', 'report_file');

        return view('admin.inspectionUserAssigneds.edit', compact('assigned_tos', 'inspectionUserAssigned', 'inspections', 'photos', 'report_files', 'signatures'));
    }

    public function update(UpdateInspectionUserAssignedRequest $request, InspectionUserAssigned $inspectionUserAssigned)
    {
        $inspectionUserAssigned->update($request->all());

        return redirect()->route('admin.inspection-user-assigneds.index');
    }

    public function show(InspectionUserAssigned $inspectionUserAssigned)
    {
        //abort_if(Gate::denies('inspection_user_assigned_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionUserAssigned->load('inspection', 'assigned_to', 'photo', 'signature', 'report_file');

        return view('admin.inspectionUserAssigneds.show', compact('inspectionUserAssigned'));
    }

    public function destroy(InspectionUserAssigned $inspectionUserAssigned)
    {
        //abort_if(Gate::denies('inspection_user_assigned_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspectionUserAssigned->delete();

        return back();
    }
}
