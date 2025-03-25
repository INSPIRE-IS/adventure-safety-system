<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeDegradationRequest;
use App\Http\Requests\UpdateTypeDegradationRequest;
use App\Models\TypeDegradation;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeDegradationController extends Controller
{
    public function index(Request $request)
    {

        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_degradation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //dd(method_exists($user, 'can'));
        //dd($user->can('view permission'));

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeDegradations = TypeDegradation::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        //dd('here');
        /** @disregard P1013 */
        return Inertia::render('TypeDegradation/Index', [
            'typeDegradations' => $typeDegradations,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'type_degradation_show' => false, //auth()->user()->can('type_degradation_show'),
                'type_degradation_create' => auth()->user()->can('type_degradation_create'),
                'type_degradation_edit' => auth()->user()->can('type_degradation_edit'),
                'type_degradation_delete' => auth()->user()->can('type_degradation_delete'),

            ],
        ]);
    }

    public function store(StoreTypeDegradationRequest $request)
    {
        $typeDegradation = TypeDegradation::create($request->all());

        return redirect()->route('type-degradations.index');
    }

    public function update(UpdateTypeDegradationRequest $request, TypeDegradation $typeDegradation)
    {
        $typeDegradation->update($request->all());

        return redirect()->route('type-degradations.index');
    }

    public function show(TypeDegradation $typeDegradation)
    {
        //abort_if(Gate::denies('type_degradation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeDegradations.show', compact('typeDegradation'));
    }

    public function destroy(TypeDegradation $typeDegradation)
    {
        //abort_if(Gate::denies('type_degradation_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeDegradation->delete();

        return back();
    }
}
