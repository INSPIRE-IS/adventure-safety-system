<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroySafetyInspectionDetailRequest;
use App\Http\Requests\StoreSafetyInspectionDetailRequest;
use App\Http\Requests\UpdateSafetyInspectionDetailRequest;
use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use App\Models\File;
use App\Models\Inspection;
use App\Models\InspectionUserAssigned;
use App\Models\SafetyInspectionDetail;
use App\Models\Team;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class SafetyInspectionDetailController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('safety_inspection_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $safetyInspectionDetails = SafetyInspectionDetail::with(['site', 'inspection', 'category_item', 'category_detail'])
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(15);
        //dd(method_exists($user, 'can'));
        //dd($user->can('view permission'));
        $sites = Team::select('name as label', 'id as value')->get();
        //$inspections = Inspection::select('inspection_schedule_id as label', 'id as value')->get();
        $inspections = Inspection::with(['inspection_schedule' => function ($query) {
            $query->select('id', 'name'); // Select only the necessary fields
        }])
            ->select('id', 'inspection_schedule_id') // Select fields from the main Inspection model
            ->get();
        $category_items = CategoryItem::select('name as label', 'id as value')->get();
        $category_details = CategoryDetail::select('name as label', 'id as value')->get();


        /** @disregard P1013 */
        return Inertia::render('SafetyInspectionDetail/Index', [
            'safetyInspectionDetail' => $safetyInspectionDetails,
            'sites' => $sites,
            'inspections' => $inspections,
            'categoryItems' => $category_items,
            'categoryDetails' => $category_details,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'safety_inspection_detail_show' => auth()->user()->can('safety_inspection_detail_show'),
                'safety_inspection_detail_edit' => auth()->user()->can('safety_inspection_detail_edit'),
                'safety_inspection_detail_delete' => auth()->user()->can('safety_inspection_detail_delete'),
                'safety_inspection_detail_create' => auth()->user()->can('safety_inspection_detail_create'),
            ],
        ]);
    }


    public function store(StoreSafetyInspectionDetailRequest $request)
    {
        $safetyInspectionDetail = SafetyInspectionDetail::create($request->all());

        return redirect()->route('safety-inspection-details.index');
    }


    public function update(UpdateSafetyInspectionDetailRequest $request, SafetyInspectionDetail $safetyInspectionDetail)
    {
        $safetyInspectionDetail->update($request->all());

        return redirect()->route('safety-inspection-details.index');
    }

    public function show(SafetyInspectionDetail $safetyInspectionDetail)
    {
        //abort_if(Gate::denies('safety_inspection_detail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $safetyInspectionDetail->load('category_detail', 'inspection', 'site', 'team');

        return view('admin.safetyInspectionDetails.show', compact('safetyInspectionDetail'));
    }

    public function destroy(SafetyInspectionDetail $safetyInspectionDetail)
    {
        //abort_if(Gate::denies('safety_inspection_detail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $safetyInspectionDetail->delete();

        return back();
    }
}
