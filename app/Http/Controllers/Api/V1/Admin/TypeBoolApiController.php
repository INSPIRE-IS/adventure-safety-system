<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeBoolRequest;
use App\Http\Requests\UpdateTypeBoolRequest;
use App\Http\Resources\Admin\TypeBoolResource;
use App\Models\TypeBool;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TypeBoolApiController extends Controller
{
    public function index(Request $request)
    {
        //return response()->json(['message' => 'Test bool type working!']);
        //abort_if(Gate::denies('type_bool_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_bool_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $typeBools = TypeBool::all();
        // Log::info('typeBools: ' . $typeBools);
        // return new TypeBoolResource($typeBools);

        return new TypeBoolResource(TypeBool::all());
    }

    public function store(StoreTypeBoolRequest $request)
    {
        $typeBool = TypeBool::create($request->all());

        return (new TypeBoolResource($typeBool))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(TypeBool $typeBool)
    {
        abort_if(Gate::denies('type_bool_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new TypeBoolResource($typeBool);
    }

    public function update(UpdateTypeBoolRequest $request, TypeBool $typeBool)
    {
        $typeBool->update($request->all());

        return (new TypeBoolResource($typeBool))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(TypeBool $typeBool)
    {
        abort_if(Gate::denies('type_bool_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeBool->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
