<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrillRequest;
use App\Http\Requests\UpdateDrillRequest;
use App\Http\Requests\VerifyDrillUploadRequest;
use App\Http\Resources\Admin\DrillResource;
use App\Models\Drill;
use App\Models\DrillDetail;
use App\Models\DrillMember;
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
        $drill = Drill::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'drill_schedule_id' => $request->drill_schedule_id,
            ],
            [
                'complete_date' => $request->complete_date,
            ]
        );

        return (new DrillResource($drill))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function verifyUpload(VerifyDrillUploadRequest $request, Drill $drill)
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

        if ((int) $drill->site_id !== (int) $teamId) {
            abort(Response::HTTP_FORBIDDEN, 'Drill does not belong to user team');
        }

        $instructorId = (int) $request->input('instructor_id');

        $detail = DrillDetail::query()
            ->where('site_id', $teamId)
            ->where('drill_id', $drill->id)
            ->where('instructor_id', $instructorId)
            ->first();

        $detailOk = $detail !== null;

        $memberUuids = $request->input('member_uuids', []);
        $missingMemberUuids = [];

        if (is_array($memberUuids) && count($memberUuids) > 0) {
            $existing = DrillMember::query()
                ->where('site_id', $teamId)
                ->where('drill_id', $drill->id)
                ->whereIn('uuid', $memberUuids)
                ->pluck('uuid')
                ->toArray();

            $existingSet = [];
            foreach ($existing as $uuid) {
                $existingSet[(string) $uuid] = true;
            }

            foreach ($memberUuids as $uuid) {
                if (!array_key_exists((string) $uuid, $existingSet)) {
                    $missingMemberUuids[] = (string) $uuid;
                }
            }
        }

        $expectedMembersCount = $request->input('expected_members_count');
        $actualMembersCount = DrillMember::query()
            ->where('site_id', $teamId)
            ->where('drill_id', $drill->id)
            ->count();

        $membersOk = count($missingMemberUuids) === 0;
        if ($expectedMembersCount !== null && $expectedMembersCount !== '') {
            $membersOk = $membersOk && ((int) $actualMembersCount >= (int) $expectedMembersCount);
        }

        $ok = $detailOk && $membersOk;

        return response()->json([
            'ok' => $ok,
            'drill_id' => (int) $drill->id,
            'site_id' => (int) $teamId,
            'header' => [
                'complete_date' => $drill->complete_date,
            ],
            'detail' => [
                'instructor_id' => $instructorId,
                'ok' => $detailOk,
            ],
            'members' => [
                'expected_count' => $expectedMembersCount,
                'actual_count' => (int) $actualMembersCount,
                'missing_uuids' => $missingMemberUuids,
                'ok' => $membersOk,
            ],
        ], Response::HTTP_OK);
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
