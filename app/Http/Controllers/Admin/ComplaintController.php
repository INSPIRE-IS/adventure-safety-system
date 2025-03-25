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
        // "id" => 5
        //           "name" => "Denver Doe"
        //           "phone" => "123-456-7890"
        //           "email" => "denver@doe.com"
        //           "complaint_date" => "2025-03-07 05:16:31"
        //           "complaint" => "The bedding was wet"
        //           "resolved" => 0
        //           "user_id" => 1
        //           "site_id" => 3
        //           "created_at" => "2025-03-07 05:16:31"
        //           "updated_at" => "2025-03-07 05:16:31"
        //           "deleted_at" => null
        /** @disregard P1013 */
        return Inertia::render('Complaint/Index', [
            'complaints' => $complaints,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'complaint_show' => true, //auth()->user()->can('type_complaint_show'),
                'complaint_create' => false, //auth()->user()->can('type_complaint_create'),
                'complaint_edit' => false, //auth()->user()->can('type_complaint_edit'),
                'complaint_delete' => false, //auth()->user()->can('type_complaint_delete'),

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

        $complaint->load('site:id,name', 'user:id,name,surname');

        return Inertia::render('Complaint/Show', [
            'complaint' => $complaint,
            'can' => [
                'complaint_show' => true, //auth()->user()->can('type_complaint_show'),
                'complaint_create' => false, //auth()->user()->can('type_complaint_create'),
                'complaint_edit' => false, //auth()->user()->can('type_complaint_edit'),
                'complaint_delete' => false, //auth()->user()->can('type_complaint_delete'),

            ],
        ]);
    }

    public function destroy(Complaint $complaint)
    {
        //abort_if(Gate::denies('type_complaint_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complaint->delete();

        return back();
    }
}
