<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Http\Resources\Admin\InspectionResource;
use App\Models\Inspection;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class InspectionsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userId = auth('api')->user()->id;
        $teamId = auth('api')->user()->teams->first()->id;

        return new InspectionResource(Inspection::select('id', 'due_date', 'complete_date', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id')
            ->with(
                [
                    'inspection_schedule:id,name,type_activity_id,type_inspection_id,active_date,period_id',
                    'inspection_schedule.type_inspection:id,name',
                    'inspection_schedule.type_activity:id,name',
                    'safety_inspection_details',
                    'equipment_inspection_details',
                    'assigned_to:id,name,surname,email,mobile_number',
                    'photo_file:id,path,type_file_id,row',
                    'signature_file:id,path,type_file_id,row'
                ]
            )
            //->where('assigned_to_id', $userId)
            //->where('site_id', $teamId)
            ->whereBetween('inspections.due_date', [Carbon::now(), Carbon::now()->addMonths(3)])
            ->get());
    }

    public function store(StoreInspectionRequest $request)
    {

        //$inspection = Inspection::create($request->all());

        $inspection = Inspection::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'id' => $request->id,
                'inspection_schedule_id' => $request->inspection_schedule_id,
            ],
            [
                'complete_date' => $request->complete_date,
                'closing_comment' => $request->closing_comment,
            ]
        );

        return (new InspectionResource($inspection))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new InspectionResource($inspection->load(['inspection_schedule', 'assigned_to', 'photo_file', 'signature_file']));
    }

    public function update(UpdateInspectionRequest $request, Inspection $inspection)
    {
        //dd($request);
        $request->merge(['complete_date' => Carbon::parse($request->complete_date)->format('Y-m-d H:i:s')]);
        try {
            $inspection->update($request->all());
        } catch (\Exception $e) {
            //return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }

        return response()->json($inspection, 202);
        // return (new InspectionResource($inspection))
        //     ->response()
        //     ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
