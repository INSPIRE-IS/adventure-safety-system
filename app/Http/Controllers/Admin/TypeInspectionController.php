<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeInspectionRequest;
use App\Http\Requests\StoreTypeInspectionRequest;
use App\Http\Requests\UpdateTypeInspectionRequest;
use App\Models\TypeInspection;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeInspectionController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeInspections = TypeInspection::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('TypeInspection/Index', [
            'typeInspections' => $typeInspections,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'type_inspection_show' => false, //auth()->user()->can('type_inspection_show'),
                'type_inspection_create' => auth()->user()->can('type_inspection_create'),
                'type_inspection_edit' => auth()->user()->can('type_inspection_edit'),
                'type_inspection_delete' => auth()->user()->can('type_inspection_delete'),

            ],
        ]);
    }

    public function store(StoreTypeInspectionRequest $request)
    {
        $typeInspection = TypeInspection::create($request->all());

        return redirect()->route('type-inspections.index');
    }

    public function update(UpdateTypeInspectionRequest $request, TypeInspection $typeInspection)
    {
        $typeInspection->update($request->all());

        return redirect()->route('type-inspections.index');
    }

    public function show(TypeInspection $typeInspection)
    {
        //abort_if(Gate::denies('type_inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeInspections.show', compact('typeInspection'));
    }

    public function destroy(TypeInspection $typeInspection)
    {
        //abort_if(Gate::denies('type_inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeInspection->delete();

        return back();
    }
}
