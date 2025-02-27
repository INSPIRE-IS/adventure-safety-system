<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyDrillMemberRequest;
use App\Http\Requests\StoreDrillMemberRequest;
use App\Http\Requests\UpdateDrillMemberRequest;
use App\Models\Drill;
use App\Models\DrillMember;
use App\Models\Team;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrillMemberController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('drill_member_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillMembers = DrillMember::with(['site', 'drill', 'user'])->get();

        $teams = Team::get();

        $drills = Drill::get();

        $users = User::get();

        return view('admin.drillMembers.index', compact('drillMembers', 'drills', 'teams', 'users'));
    }

    public function create()
    {
        abort_if(Gate::denies('drill_member_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sites = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $drills = Drill::pluck('complete_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.drillMembers.create', compact('drills', 'sites', 'users'));
    }

    public function store(StoreDrillMemberRequest $request)
    {
        $drillMember = DrillMember::create($request->all());

        return redirect()->route('admin.drill-members.index');
    }

    public function edit(DrillMember $drillMember)
    {
        abort_if(Gate::denies('drill_member_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sites = Team::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $drills = Drill::pluck('complete_date', 'id')->prepend(trans('global.pleaseSelect'), '');

        $users = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $drillMember->load('site', 'drill', 'user');

        return view('admin.drillMembers.edit', compact('drillMember', 'drills', 'sites', 'users'));
    }

    public function update(UpdateDrillMemberRequest $request, DrillMember $drillMember)
    {
        $drillMember->update($request->all());

        return redirect()->route('admin.drill-members.index');
    }

    public function show(DrillMember $drillMember)
    {
        abort_if(Gate::denies('drill_member_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillMember->load('site', 'drill', 'user');

        return view('admin.drillMembers.show', compact('drillMember'));
    }

    public function destroy(DrillMember $drillMember)
    {
        abort_if(Gate::denies('drill_member_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drillMember->delete();

        return back();
    }
}
