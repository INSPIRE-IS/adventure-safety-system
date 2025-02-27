<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillRequest;
use App\Http\Requests\UpdateDrillRequest;
use App\Http\Resources\Admin\DrillResource;
use App\Models\Drill;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DrillsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userId = auth('api')->user()->id;
        $teamId = auth('api')->user()->teams->first()->id;

        return new DrillResource(Drill::select('id', 'due_date', 'complete_date', 'drill_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id')
            ->with([
                'drill_schedule:id,name,type_activity_id,type_drill_id,active_date,period_id',
                'drill_schedule.type_drill:id,name',
                'drill_schedule.type_activity:id,name',
                'assigned_to:id,name,surname,email,mobile_number',
                'photo_file:id,path,type_file_id,row',
                'signature_file:id,path,type_file_id,row',
                'drill_details'
            ])
            //->where('assigned_to_id', $userId)
            //->where('site_id', $teamId)
            ->whereBetween('drills.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get());
    }

    public function store(StoreDrillRequest $request)
    {
        $drill = Drill::create($request->all());

        return (new DrillResource($drill))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Drill $drill)
    {
        abort_if(Gate::denies('drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new DrillResource($drill->load(['drill_schedule', 'assigned_to', 'photo_file', 'signature_file']));
    }
    //UpdateDrill
    public function update(Request $request, Drill $drill)
    {
        $drill->update($request->all());

        return (new DrillResource($drill))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Drill $drill)
    {
        abort_if(Gate::denies('drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $drill->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
