<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Resources\Admin\FileResource;
use App\Models\File;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Str;
use Symfony\Component\HttpFoundation\Response;
use App\Jobs\ProcessUploadedFile;
use Illuminate\Support\Facades\Auth;

class FilesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new FileResource(File::with(['site', 'type_file'])->get());
    }

    public function store(StoreFileRequest $request)
    {
        $userId = auth()->id();
        $siteId = auth()->user()->teams->first()->id;

        $uploadedFile = $request->file('file');
        $fileName = $request->input('file_name') ?? $uploadedFile->getClientOriginalName();
        $storagePath = $uploadedFile->storeAs('images', $fileName, 'public');
        $fileUrl = Storage::url($storagePath);

        // Build file metadata for the job
        $data = [
            'file_name' => $fileName,
            'file_type' => $request->input('file_type'),
            'path' => $fileUrl,
            'table_name' => $request->input('table_name'),
            'table_row_id' => $request->input('table_row_id'),
            'type_file_id' => $request->input('type_file_id'),
        ];

        dispatch(new ProcessUploadedFile($data, $userId, $siteId));

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
