<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeBoolRequest;
use App\Http\Requests\StoreTypeBoolRequest;
use App\Http\Requests\UpdateTypeBoolRequest;
use App\Models\TypeBool;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeBoolController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_bool_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeBools = TypeBool::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('TypeBool/Index', [
            'typeBools' => $typeBools,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'type_bool_show' => false, //auth()->user()->can('type_bool_show'),
                'type_bool_create' => auth()->user()->can('type_bool_create'),
                'type_bool_edit' => auth()->user()->can('type_bool_edit'),
                'type_bool_delete' => auth()->user()->can('type_bool_delete'),

            ],
        ]);
    }

    public function store(StoreTypeBoolRequest $request)
    {
        $typeBool = TypeBool::create($request->all());

        return redirect()->route('type-bools.index');
    }

    public function update(UpdateTypeBoolRequest $request, TypeBool $typeBool)
    {
        $typeBool->update($request->all());

        return redirect()->route('type-bools.index');
    }

    public function show(TypeBool $typeBool)
    {
        //abort_if(Gate::denies('type_bool_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeBools.show', compact('typeBool'));
    }

    public function destroy(TypeBool $typeBool)
    {
        //abort_if(Gate::denies('type_bool_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeBool->delete();

        return back();
    }
}
