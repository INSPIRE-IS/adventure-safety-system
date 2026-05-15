<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\Admin\FileResource;
use App\Jobs\ProcessUploadedFile;
use App\Models\File;
use Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FilesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FileResource(File::with(['site', 'type_file'])->get());
    }

    public function store(StoreFileRequest $request)
    {
        $user = $request->user();
        $userId = (int) ($user?->id ?? 0);

        $requestedSiteIdRaw = $request->input('site_id');
        $requestedSiteId = ($requestedSiteIdRaw !== null && $requestedSiteIdRaw !== '') ? (int) $requestedSiteIdRaw : 0;

        $siteId = (int) ($user?->teams?->first()?->id ?? 0);
        if ($requestedSiteId > 0) {
            abort_if(!$user || !$user->teams->contains('id', $requestedSiteId), Response::HTTP_FORBIDDEN, 'Invalid site_id for user');
            $siteId = $requestedSiteId;
        }

        $tableName = (string) $request->input('table_name');
        $tableRowId = (int) $request->input('table_row_id');
        $typeFileId = (int) $request->input('type_file_id');

        $uploadedFile = $request->file('file');
        $fileName = $request->input('file_name') ?? $uploadedFile->getClientOriginalName();
        $storagePath = $uploadedFile->storeAs('images', $fileName, 'public');
        $fileUrl = Storage::url($storagePath);

        if (config('app.mobile_upload_debug')) {
            Log::info('mobile.files.upload.start', [
                'auth_user_id' => $userId,
                'requested_site_id' => $requestedSiteId,
                'resolved_site_id' => $siteId,
                'table_name' => $tableName,
                'table_row_id' => $tableRowId,
                'type_file_id' => $typeFileId,
                'file_name' => $fileName,
                'upload_size' => $uploadedFile?->getSize(),
                'upload_mime' => $uploadedFile?->getMimeType(),
            ]);
        }

        // Build file metadata for the job
        $data = [
            'file_name' => $fileName,
            'file_type' => $request->input('file_type'),
            'path' => $fileUrl,
            'table_name' => $tableName,
            'table_row_id' => $tableRowId,
            'type_file_id' => $typeFileId,
        ];

        dispatch(new ProcessUploadedFile($data, $userId, $siteId));

        if (config('app.mobile_upload_debug')) {
            Log::info('mobile.files.upload.queued', [
                'auth_user_id' => $userId,
                'site_id' => $siteId,
                'table_name' => $tableName,
                'table_row_id' => $tableRowId,
                'type_file_id' => $typeFileId,
                'path' => $fileUrl,
            ]);
        }

        return response()->json([
            'message' => 'File upload queued successfully',
        ], 202);
    }

    public function storeOld(StoreFileRequest $request)
    {
        $data = $request->all();

        // Get the base64 string from request
        $base64String = $data['file_base64'];

        // Decode base64 string
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64String));

        // Generate a unique file name
        $fileName = 'images/' . $data['file_name'];

        // Store image in public storage (or use "storage/app/public")
        Storage::disk('public')->put($fileName, $imageData);

        // Get the file URL
        $filePath = Storage::url($fileName);

        // Add site_id
        $data['site_id'] = auth()->user()->teams->first()->id;
        $data['path'] = $filePath;

        // Check if file exists based on the provided criteria, update if exists, else create new
        $file = File::updateOrCreate(
            [
                'site_id' => $data['site_id'],
                'table_name' => $data['table_name'],
                'table_row_id' => $data['table_row_id'],
                'type_file_id' => $data['type_file_id'],
            ],
            [
                'file_name' => $data['file_name'],
                'file_type' => $data['file_type'],
                'path' => $filePath,
            ]
        );

        return (new FileResource($file))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(File $file)
    {
        abort_if(Gate::denies('file_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FileResource($file->load(['site', 'type_file']));
    }

    public function update(UpdateFileRequest $request, File $file)
    {
        $file->update($request->all());

        return (new FileResource($file))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(File $file)
    {
        abort_if(Gate::denies('file_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $file->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}