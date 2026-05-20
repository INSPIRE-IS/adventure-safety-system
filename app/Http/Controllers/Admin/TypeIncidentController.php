<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyTypeIncidentRequest;
use App\Http\Requests\StoreTypeIncidentRequest;
use App\Http\Requests\UpdateTypeIncidentRequest;
use App\Models\TypeIncident;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class TypeIncidentController extends Controller
{
    public function index(Request $request)
    {

        /** @disregard P1013 */
        abort_if(!auth()->user()->can('type_incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeIncidents = TypeIncident::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('TypeIncident/Index', [
            'typeIncidents' => $typeIncidents,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'type_incident_show' => false, //auth()->user()->can('type_incident_show'),
                'type_incident_create' => auth()->user()->can('type_incident_create'),
                'type_incident_edit' => auth()->user()->can('type_incident_edit'),
                'type_incident_delete' => auth()->user()->can('type_incident_delete'),

            ],
        ]);
    }

    public function store(StoreTypeIncidentRequest $request)
    {
        $typeIncident = TypeIncident::create($request->all());

        return redirect()->route('type-incidents.index');
    }

    public function update(UpdateTypeIncidentRequest $request, TypeIncident $typeIncident)
    {
        $typeIncident->update($request->all());

        return redirect()->route('type-incidents.index');
    }

    public function show(TypeIncident $typeIncident)
    {
        //abort_if(Gate::denies('type_incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.typeIncidents.show', compact('typeIncident'));
    }

    public function destroy(TypeIncident $typeIncident)
    {
        //abort_if(Gate::denies('type_incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $typeIncident->delete();

        return back();
    }
}
