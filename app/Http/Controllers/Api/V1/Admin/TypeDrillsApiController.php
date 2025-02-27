<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeDrillRequest;
use App\Http\Requests\UpdateTypeDrillRequest;
use App\Http\Resources\Admin\TypeDrillResource;
use App\Models\TypeDrill;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeDrillsApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeDrillResource(TypeDrill::all());
    }

    public function store(StoreTypeDrillRequest $request)
    {
        $typeDrill = TypeDrill::create($request->all());

        return (new TypeDrillResource($typeDrill))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TypeDrill $typeDrill)
    {
        abort_if(Gate::denies('type_drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeDrillResource($typeDrill);
    }

    public function update(UpdateTypeDrillRequest $request, TypeDrill $typeDrill)
    {
        $typeDrill->update($request->all());

        return (new TypeDrillResource($typeDrill))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TypeDrill $typeDrill)
    {
        abort_if(Gate::denies('type_drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeDrill->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
