<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeFileRequest;
use App\Http\Requests\UpdateTypeFileRequest;
use App\Http\Resources\Admin\TypeFileResource;
use App\Models\TypeFile;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeFilesApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeFileResource(TypeFile::all());
    }

    public function store(StoreTypeFileRequest $request)
    {
        $typeFile = TypeFile::create($request->all());

        return (new TypeFileResource($typeFile))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TypeFile $typeFile)
    {
        abort_if(Gate::denies('type_file_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeFileResource($typeFile);
    }

    public function update(UpdateTypeFileRequest $request, TypeFile $typeFile)
    {
        $typeFile->update($request->all());

        return (new TypeFileResource($typeFile))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TypeFile $typeFile)
    {
        abort_if(Gate::denies('type_file_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeFile->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
