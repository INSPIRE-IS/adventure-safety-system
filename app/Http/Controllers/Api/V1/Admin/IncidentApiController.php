<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Http\Requests\VerifyIncidentUploadRequest;
use App\Http\Resources\Admin\IncidentResource;
use App\Models\File;
use App\Models\Incident;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidentApiController extends Controller
{

    public function index()
    {
        abort_if(Gate::denies('incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncidentResource(Incident::with(['site', 'type_incident', 'signature_file', 'incident_file', 'team'])->get());
    }

    public function store(StoreIncidentRequest $request)
    {
        $incident = Incident::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
                'type_incident_id' => $request->type_incident_id,
                'uuid' => $request->id,
            ],
            [
                'incident_date' => $request->incident_date,
                'instructor_one_name' => $request->instructor_one_name,
                'instructor_two_name' => $request->instructor_two_name,
                'description' => $request->description,
                // 'incident_file_id' => $request->incident_file_id,
                // 'signature_file_id' => $request->signature_file_id,
            ]
        );

        return (new IncidentResource($incident))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function verifyUpload(VerifyIncidentUploadRequest $request, Incident $incident)
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

        if ((int) $incident->site_id !== (int) $teamId) {
            abort(Response::HTTP_FORBIDDEN, 'Incident does not belong to user team');
        }

        if ((int) $incident->user_id !== (int) $user->id) {
            abort(Response::HTTP_FORBIDDEN, 'Incident does not belong to user');
        }

        $uuid = (string) $request->input('uuid');
        if ((string) $incident->uuid !== $uuid) {
            return response()->json([
                'ok' => false,
                'reason' => 'uuid_mismatch',
                'incident_id' => (int) $incident->id,
                'site_id' => (int) $teamId,
            ], Response::HTTP_OK);
        }

        $descriptionOk = trim((string) $incident->description) !== '';
        $typeOk = (int) $incident->type_incident_id > 0;
        $dateOk = $incident->incident_date !== null;

        $expectedFilesCount = $request->input('expected_files_count');
        $actualFilesCount = File::query()
            ->where('site_id', $teamId)
            ->where('table_name', 'incidents')
            ->where('table_row_id', $incident->id)
            ->count();

        $filesOk = true;
        if ($expectedFilesCount !== null && $expectedFilesCount !== '') {
            $filesOk = (int) $actualFilesCount >= (int) $expectedFilesCount;
        }

        $ok = $descriptionOk && $typeOk && $dateOk && $filesOk;

        return response()->json([
            'ok' => $ok,
            'incident_id' => (int) $incident->id,
            'site_id' => (int) $teamId,
            'header' => [
                'uuid' => (string) $incident->uuid,
                'type_incident_id' => (int) $incident->type_incident_id,
                'incident_date' => $incident->incident_date,
            ],
            'required' => [
                'description_ok' => $descriptionOk,
                'type_ok' => $typeOk,
                'incident_date_ok' => $dateOk,
            ],
            'files' => [
                'expected_count' => $expectedFilesCount,
                'actual_count' => (int) $actualFilesCount,
                'ok' => $filesOk,
            ],
        ], Response::HTTP_OK);
    }

    public function show(Incident $incident)
    {
        abort_if(Gate::denies('incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new IncidentResource($incident->load(['site', 'type_incident', 'signature_file', 'incident_file', 'team']));
    }

    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        $incident->update($request->all());

        return (new IncidentResource($incident))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Incident $incident)
    {
        abort_if(Gate::denies('incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $incident->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
