<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Requests\VerifyComplaintUploadRequest;
use App\Http\Resources\Admin\ComplaintResource;
use App\Models\Complaint;
use Gate;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class ComplaintApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ComplaintResource(Complaint::with(['site', 'team'])->get());
    }

    public function store(StoreComplaintRequest $request)
    {
        Log::info('ComplaintApiController.store', [
            'auth_user_id' => $request->user()?->id,
            'payload_keys' => array_keys($request->all()),
            'site_id' => $request->input('site_id'),
            'user_id' => $request->input('user_id'),
            'complaint_date' => $request->input('complaint_date'),
            'title_len' => strlen((string) $request->input('title', '')),
            'description_len' => strlen((string) $request->input('description', '')),
        ]);
        $complaint = Complaint::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
                'uuid' => $request->id,
            ],
            [
                'name' => $request->name,
                'phone' => $request->phone ?? ' ',
                'email' => $request->email ?? ' ',
                'title' => $request->title ?? ' ',
                'description' => $request->description ?? ' ',
                'complaint_date' => $request->complaint_date,
            ]
        );

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function verifyUpload(VerifyComplaintUploadRequest $request, Complaint $complaint)
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

        if ((int) $complaint->site_id !== (int) $teamId) {
            abort(Response::HTTP_FORBIDDEN, 'Complaint does not belong to user team');
        }

        if ((int) $complaint->user_id !== (int) $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Complaint does not belong to user');
        }

        $uuid = (string) $request->input('uuid');
        if ((string) $complaint->uuid !== $uuid) {
            return response()->json([
                'ok' => false,
                'reason' => 'uuid_mismatch',
                'complaint_id' => (int) $complaint->id,
                'site_id' => (int) $teamId,
            ], Response::HTTP_OK);
        }

        $descriptionOk = trim((string) $complaint->description) !== '';
        $dateOk = $complaint->complaint_date !== null;

        $ok = $descriptionOk && $dateOk;

        return response()->json([
            'ok' => $ok,
            'complaint_id' => (int) $complaint->id,
            'site_id' => (int) $teamId,
            'header' => [
                'uuid' => (string) $complaint->uuid,
                'complaint_date' => $complaint->complaint_date,
            ],
            'required' => [
                'description_ok' => $descriptionOk,
                'complaint_date_ok' => $dateOk,
            ],
        ], Response::HTTP_OK);
    }

    public function show(Complaint $complaint)
    {
        abort_if(Gate::denies('complaint_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ComplaintResource($complaint->load(['site', 'team']));
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->all());

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Complaint $complaint)
    {
        abort_if(Gate::denies('complaint_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complaint->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
