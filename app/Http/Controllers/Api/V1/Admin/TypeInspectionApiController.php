<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeInspectionRequest;
use App\Http\Requests\UpdateTypeInspectionRequest;
use App\Http\Resources\Admin\TypeInspectionResource;
use App\Models\TypeInspection;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TypeInspectionApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeInspectionResource(TypeInspection::all());
    }

    public function store(StoreTypeInspectionRequest $request)
    {
        $typeInspection = TypeInspection::create($request->all());

        return (new TypeInspectionResource($typeInspection))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TypeInspection $typeInspection)
    {
        abort_if(Gate::denies('type_inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeInspectionResource($typeInspection);
    }

    public function update(UpdateTypeInspectionRequest $request, TypeInspection $typeInspection)
    {
        $typeInspection->update($request->all());

        return (new TypeInspectionResource($typeInspection))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TypeInspection $typeInspection)
    {
        abort_if(Gate::denies('type_inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeInspection->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
