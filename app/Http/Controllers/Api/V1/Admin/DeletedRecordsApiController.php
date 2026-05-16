<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeletedRecordRequest;
use App\Http\Resources\Admin\DeletedRecordResource;
use App\Models\DeletedRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class DeletedRecordsApiController extends Controller
{
    public function store(StoreDeletedRecordRequest $request)
    {
        $user = $request->user();
        $userId = (int) ($user?->id ?? 0);

        $requestedTeamIdRaw = $request->input('team_id');
        $requestedTeamId = ($requestedTeamIdRaw !== null && $requestedTeamIdRaw !== '') ? (int) $requestedTeamIdRaw : 0;

        $siteId = (int) ($user?->teams?->first()?->id ?? 0);
        if ($requestedTeamId > 0) {
            abort_if(!$user || !$user->teams->contains('id', $requestedTeamId), Response::HTTP_FORBIDDEN, 'Invalid team_id for user');
            $siteId = $requestedTeamId;
        }

        $deviceDeletedAt = null;
        $deletedAtRaw = $request->input('deleted_at');
        if ($deletedAtRaw !== null && $deletedAtRaw !== '') {
            try {
                $deviceDeletedAt = Carbon::parse((string) $deletedAtRaw);
            } catch (\Throwable $e) {
                $deviceDeletedAt = null;
            }
        }

        if (config('app.mobile_upload_debug')) {
            Log::info('mobile.deleted_records.store', [
                'auth_user_id' => $userId,
                'site_id' => $siteId,
                'module' => (string) $request->input('module'),
                'record_id' => $request->input('record_id'),
                'detail_id' => $request->input('detail_id'),
                'file_count' => $request->input('file_count'),
                'device_deleted_at' => $deviceDeletedAt?->toIso8601String(),
            ]);
        }

        $deletedRecord = DeletedRecord::create([
            'module' => (string) $request->input('module'),
            'record_id' => $request->input('record_id') !== null ? (int) $request->input('record_id') : null,
            'detail_id' => $request->input('detail_id') !== null ? (int) $request->input('detail_id') : null,
            'file_count' => (int) ($request->input('file_count') ?? 0),
            'device_deleted_at' => $deviceDeletedAt,
            'payload' => $request->input('payload'),
            'user_id' => $userId > 0 ? $userId : null,
            'site_id' => $siteId > 0 ? $siteId : null,
        ]);

        return (new DeletedRecordResource($deletedRecord))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}