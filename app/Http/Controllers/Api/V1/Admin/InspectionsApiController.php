<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Http\Requests\VerifyInspectionUploadRequest;
use App\Http\Resources\Admin\InspectionResource;
use App\Models\File;
use App\Models\Inspection;
use App\Models\SafetyInspectionDetail;
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

    public function verifyUpload(VerifyInspectionUploadRequest $request, Inspection $inspection)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], Response::HTTP_UNAUTHORIZED);
        }

        $requestedTeamId = $request->input('team_id');
        $teamId = $user->teams->first()->id;

        if ($requestedTeamId !== null && $requestedTeamId !== '') {
            $requestedTeamId = (int) $requestedTeamId;

            if ($user->teams->contains('id', $requestedTeamId)) {
                $teamId = $requestedTeamId;
            } else {
                abort(Response::HTTP_FORBIDDEN, 'Invalid team_id for user');
            }
        }

        if ((int) $inspection->site_id !== (int) $teamId) {
            abort(Response::HTTP_FORBIDDEN, 'Inspection does not belong to user team');
        }

        $detailKeys = $request->input('detail_keys', []);
        $fileKeys = $request->input('file_keys', []);

        $details = SafetyInspectionDetail::query()
            ->where('site_id', $teamId)
            ->where('inspection_id', $inspection->id)
            ->get(['id', 'category_item_id', 'category_detail_id']);

        $detailsByKey = [];
        foreach ($details as $detail) {
            $detailsByKey[$detail->category_item_id . '-' . $detail->category_detail_id] = $detail;
        }

        $missingDetailKeys = [];
        foreach ($detailKeys as $key) {
            $lookupKey = ((int) $key['category_item_id']) . '-' . ((int) $key['category_detail_id']);
            if (!array_key_exists($lookupKey, $detailsByKey)) {
                $missingDetailKeys[] = [
                    'category_item_id' => (int) $key['category_item_id'],
                    'category_detail_id' => (int) $key['category_detail_id'],
                ];
            }
        }

        $missingFileKeys = [];
        $expectedFilePairs = [];
        $expectedDetailIds = [];
        $expectedTypeFileIds = [];

        foreach ($fileKeys as $key) {
            $lookupKey = ((int) $key['category_item_id']) . '-' . ((int) $key['category_detail_id']);

            if (!array_key_exists($lookupKey, $detailsByKey)) {
                $missingFileKeys[] = [
                    'category_item_id' => (int) $key['category_item_id'],
                    'category_detail_id' => (int) $key['category_detail_id'],
                    'type_file_id' => (int) $key['type_file_id'],
                    'reason' => 'missing_detail',
                ];
                continue;
            }

            $detailId = (int) $detailsByKey[$lookupKey]->id;
            $typeFileId = (int) $key['type_file_id'];
            $pairKey = $detailId . '-' . $typeFileId;

            $expectedFilePairs[$pairKey] = [
                'category_item_id' => (int) $key['category_item_id'],
                'category_detail_id' => (int) $key['category_detail_id'],
                'type_file_id' => $typeFileId,
            ];

            $expectedDetailIds[$detailId] = true;
            $expectedTypeFileIds[$typeFileId] = true;
        }

        $expectedDetailIdsList = array_map('intval', array_keys($expectedDetailIds));
        $expectedTypeFileIdsList = array_map('intval', array_keys($expectedTypeFileIds));

        $existingFiles = [];
        if (count($expectedFilePairs) > 0) {
            $existingFiles = File::query()
                ->where('site_id', $teamId)
                ->where('table_name', 'safety_inspection_details')
                ->whereIn('table_row_id', $expectedDetailIdsList)
                ->whereIn('type_file_id', $expectedTypeFileIdsList)
                ->get(['table_row_id', 'type_file_id'])
                ->toArray();
        }

        $existingFilePairs = [];
        foreach ($existingFiles as $file) {
            $existingFilePairs[((int) $file['table_row_id']) . '-' . ((int) $file['type_file_id'])] = true;
        }

        foreach ($expectedFilePairs as $pairKey => $meta) {
            if (!array_key_exists($pairKey, $existingFilePairs)) {
                $missingFileKeys[] = $meta + ['reason' => 'missing_file'];
            }
        }

        $expectedDetailsCount = $request->input('expected_details_count');
        $expectedFilesCount = $request->input('expected_files_count');

        $detailsOk = count($missingDetailKeys) === 0;
        $filesOk = count($missingFileKeys) === 0;

        if ($expectedDetailsCount !== null && $expectedDetailsCount !== '') {
            $detailsOk = $detailsOk && ((int) $expectedDetailsCount === (int) $details->count());
        }

        if ($expectedFilesCount !== null && $expectedFilesCount !== '') {
            $filesOk = $filesOk && ((int) $expectedFilesCount === (int) count($existingFilePairs));
        }

        $ok = $detailsOk && $filesOk;
        $pending = !$ok && ($inspection->complete_date !== null);

        return response()->json([
            'ok' => $ok,
            'pending' => $pending,
            'inspection_id' => (int) $inspection->id,
            'site_id' => (int) $teamId,
            'header' => [
                'complete_date' => $inspection->complete_date,
            ],
            'details' => [
                'expected_count' => $expectedDetailsCount,
                'actual_count' => (int) $details->count(),
                'missing_keys' => $missingDetailKeys,
            ],
            'files' => [
                'expected_count' => $expectedFilesCount,
                'expected_pairs_count' => (int) count($expectedFilePairs),
                'actual_pairs_count' => (int) count($existingFilePairs),
                'missing_keys' => $missingFileKeys,
            ],
        ], Response::HTTP_OK);
    }

    public function destroy(Inspection $inspection)
    {
        abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
