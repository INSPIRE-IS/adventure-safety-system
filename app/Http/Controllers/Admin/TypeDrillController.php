<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeDrillRequest;
use App\Http\Requests\StoreTypeDrillRequest;
use App\Http\Requests\UpdateTypeDrillRequest;
use App\Models\TypeDrill;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeDrillController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_drill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeDrills = TypeDrill::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);
        //dd(method_exists($user, 'can'));
        //dd($user->can('view permission'));

        /** @disregard P1013 */
        return Inertia::render('TypeDrill/Index', [
            'typeDrills' => $typeDrills,
            'can' => [
                'type_drill_show' => auth()->user()->can('type_drill_show'),
                'type_drill_edit' => auth()->user()->can('type_drill_edit'),
                'type_drill_delete' => auth()->user()->can('type_drill_delete'),
                'type_drill_create' => auth()->user()->can('type_drill_create'),
            ],
        ]);
    }

    public function store(StoreTypeDrillRequest $request)
    {
        $typeDrill = TypeDrill::create($request->all());

        return redirect()->route('type-drills.index');
    }


    public function update(UpdateTypeDrillRequest $request, TypeDrill $typeDrill)
    {
        $typeDrill->update($request->all());

        return redirect()->route('type-drills.index');
    }

    public function show(TypeDrill $typeDrill)
    {
        //abort_if(Gate::denies('type_drill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('typeDrills.show', compact('typeDrill'));
    }

    public function destroy(TypeDrill $typeDrill)
    {
        //abort_if(Gate::denies('type_drill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeDrill->delete();

        return back();
    }
}
