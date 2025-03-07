<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeActivityRequest;
use App\Http\Requests\UpdateTypeActivityRequest;
use App\Models\TypeActivity;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeActivityController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_activities_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeActivitys = TypeActivity::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('TypeActivity/Index', [
            'typeActivitys' => $typeActivitys,
            'can' => [
                'type_bool_show' => false, //auth()->user()->can('type_bool_show'),
                'type_bool_create' => auth()->user()->can('type_bool_create'),
                'type_bool_edit' => auth()->user()->can('type_bool_edit'),
                'type_bool_delete' => auth()->user()->can('type_bool_delete'),

            ],
        ]);
    }

    public function store(StoreTypeActivityRequest $request)
    {
        $typeActivity = TypeActivity::create($request->all());

        return redirect()->route('type-bools.index');
    }

    public function update(UpdateTypeActivityRequest $request, TypeActivity $typeActivity)
    {
        $typeActivity->update($request->all());

        return redirect()->route('type-bools.index');
    }

    public function show(TypeActivity $typeActivity)
    {
        //abort_if(Gate::denies('type_bool_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeActivitys.show', compact('typeActivity'));
    }

    public function destroy(TypeActivity $typeActivity)
    {
        //abort_if(Gate::denies('type_bool_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeActivity->delete();

        return back();
    }
}
