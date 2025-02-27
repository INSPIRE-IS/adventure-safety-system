<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeActivityRequest;
use App\Http\Requests\StoreTypeActivityRequest;
use App\Http\Requests\UpdateTypeActivityRequest;
use App\Models\TypeActivity;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeActivityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_activity_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeActivities = TypeActivity::all();

        return view('admin.typeActivities.index', compact('typeActivities'));
    }

    public function create()
    {
        abort_if(Gate::denies('type_activity_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeActivities.create');
    }

    public function store(StoreTypeActivityRequest $request)
    {
        $typeActivity = TypeActivity::create($request->all());

        return redirect()->route('admin.type-activities.index');
    }

    public function edit(TypeActivity $typeActivity)
    {
        abort_if(Gate::denies('type_activity_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeActivities.edit', compact('typeActivity'));
    }

    public function update(UpdateTypeActivityRequest $request, TypeActivity $typeActivity)
    {
        $typeActivity->update($request->all());

        return redirect()->route('admin.type-activities.index');
    }

    public function show(TypeActivity $typeActivity)
    {
        abort_if(Gate::denies('type_activity_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeActivities.show', compact('typeActivity'));
    }

    public function destroy(TypeActivity $typeActivity)
    {
        abort_if(Gate::denies('type_activity_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeActivity->delete();

        return back();
    }
}
