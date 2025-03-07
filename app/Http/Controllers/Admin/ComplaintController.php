<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Response;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        //abort_if(!auth()->user()->can('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $complaints = Complaint::where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('Complaint/Index', [
            'complaints' => $complaints,
            'can' => [
                'complaint_show' => false, //auth()->user()->can('type_complaint_show'),
                'complaint_create' => auth()->user()->can('type_complaint_create'),
                'complaint_edit' => auth()->user()->can('type_complaint_edit'),
                'complaint_delete' => auth()->user()->can('type_complaint_delete'),

            ],
        ]);
    }

    public function store(StoreComplaintRequest $request)
    {
        $complaint = Complaint::create($request->all());

        return redirect()->route('complaints.index');
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->all());

        return redirect()->route('complaints.index');
    }

    public function show(Complaint $complaint)
    {
        //abort_if(Gate::denies('type_complaint_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.complaints.show', compact('complaint'));
    }

    public function destroy(Complaint $complaint)
    {
        //abort_if(Gate::denies('type_complaint_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complaint->delete();

        return back();
    }
}
