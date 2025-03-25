<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Models\CategoryItem;
use App\Models\EquipmentInspectionDetail;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\Team;
use App\Models\TypeBool;
use App\Models\TypeInspection;
use App\Models\User;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        //dd($user->hasAnyRole(['admin', 'super']));
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        if ($request->get('filter', '') == 'period') {
            $searchPeriod = $request->get('value', ''); // 1 = Last 30 Days
        }
        $searchMonth = '';
        if ($request->get('filter', '') == 'month') {
            $searchMonth = $request->get('value', ''); // 1 = current month
        }
        $searchShow = '';
        if ($request->get('filter', '') == 'show') {
            $searchShow = $request->get('value', ''); // paginate 25
        }
        $selectedDate = $searchMonth ? Carbon::parse($searchMonth) : null;
        $pages = $searchShow ? $searchShow : 25;

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'due_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeInspection = 3; // PPE Inspections $request->get('typeInspection', 1);

        $inspections = Inspection::select('id', 'due_date', 'complete_date', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id', 'created_at')
            ->with([
                'inspection_schedule:id,name,active_date,period_id,assigned_to_id,assigned_by_id,site_id,type_inspection_id,type_activity_id',
                'inspection_schedule.category_items:id,type_inspection_id,name',  // Add categoryItems here
                'assigned_to:id,name,surname',
                // 'photo_file:id,name,path,type_file_id,row',
                // 'signature_file:id,name,path,type_file_id,row',
            ])
            ->when($selectedDate, function ($query) use ($selectedDate) {
                $query->whereMonth('created_at', $selectedDate->month)
                    ->whereYear('created_at', $selectedDate->year);
            })
            ->whereHas('inspection_schedule', function ($query) use ($typeInspection) {
                $query->where('type_inspection_id', $typeInspection);
            })
            ->where('site_id', $teamId)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate($pages);

        $flattenedData = collect();

        foreach ($inspections as $inspection) {
            //dd($inspection);
            $assigned_to = $inspection->assigned_to->name ?? 'N/A';
            foreach ($inspection->inspection_schedule->category_items as $categoryItem) {
                $flattenedData->push([
                    'inspection_id' => $inspection->id,
                    'due_date' => $inspection->due_date,
                    'complete_date' => $inspection->complete_date,
                    'status' => $inspection->complete_date ? 'Complete' : (now() > $inspection->due_date ? 'Overdue' : 'Upcoming'),
                    'inspector' =>  $assigned_to,
                    'category_item' => $categoryItem->name,
                    'category_id' => $categoryItem->id,
                    'created_at' => $inspection->created_at,
                ]);
            }
        }

        // Create a new paginator instance manually
        $page = request('page', 1);
        $perPage = $pages;
        $pagedData = $flattenedData->slice(($page - 1) * $perPage, $perPage)->values();
        $pagination = new LengthAwarePaginator($pagedData, $flattenedData->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        $sites = Team::select('name as label', 'id as value')->get();
        $typeInspections = TypeInspection::select('name as label', 'id as value')->get();
        $categoryItems = CategoryItem::select('name as label', 'id as value')->get();
        setPermissionsTeamId($teamId);
        $users = User::whereHas('teams', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['manager', 'inspector']);
            })
            ->select('name as label', 'id as value')->get();

        $periods = collect(InspectionSchedule::PERIOD_SELECT);
        $periodFilters = collect(Inspection::PERIOD_SELECT);
        $months = Inspection::getMonths();
        $shows = collect(Inspection::SHOWS_SELECT);

        // $periods = [];
        // foreach ($periodSelects as $value => $label) {
        //     $periods[] = [
        //         'value' => (string)$value,  // Convert value to string
        //         'label' => $label
        //     ];
        // }
        $actives = array(
            [
                'value' => "1",
                'label' => "No",
            ],
            [
                'value' => "2",
                'label' => "Yes",
            ]
        );

        //add equipment
        $categoryItems = CategoryItem::select('name as label', 'id as value')
            ->with(['site', 'type'])
            ->get();

        $type_bools = TypeBool::select('name as label', 'id as value')->get();
        //dd($pagination);
        /** @disregard P1013 */
        return Inertia::render('Equipment/Index', [
            'inspections' => $pagination,
            'sites' => $sites,
            'typeInspections' => $typeInspections,
            'categoryItems' => $categoryItems,
            'periods' => $periods,
            'periodFilters' => $periodFilters,
            'months' => $months,
            'shows' => $shows,
            'searchPeriod' => $searchPeriod ?? '',
            'searchMonth' => $searchMonth ?? '',
            'searchShow' => $searchShow ?? '',
            'actives' => $actives,
            'users' => $users,
            'typeBools' => $type_bools,
            'can' => [
                'inspection_show' => auth()->user()->can('inspection_show'),
                'inspection_edit' => false, //auth()->user()->can('inspection_edit'),
                'inspection_delete' => false, //auth()->user()->can('inspection_delete'),
                'inspection_create' => auth()->user()->can('inspection_create'),
                'inspection_edit_reminder' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_inspector' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_date' => true, //auth()->user()->can('inspection_create'),
            ],
        ]);
    }


    public function store(StoreInspectionRequest $request)
    {
        $inspection = Inspection::create($request->all());

        return redirect()->route('admin.equipments.index');
    }

    public function update(UpdateInspectionRequest $request, Inspection $inspection)
    {
        $inspection->update($request->all());

        return redirect()->route('admin.equipments.index');
    }

    public function show(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection_id = request('inspection_id');
        //dd($inspection_id);
        $category_id = request('category_id');
        $user = auth()->user();
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        $inspection = Inspection::where('id', $inspection_id)->with('assigned_to:id,name,surname')->first();
        $category_item = CategoryItem::where('id', $category_id)->first();
        //dd($category_item);
        $inspection_details = EquipmentInspectionDetail::where('inspection_id', $inspection_id)
            ->where('category_item_id', $category_id)
            ->with([
                'category_item:id,name',
                'category_detail'
            ])->get();
        // dd($inspection_details);
        setPermissionsTeamId($teamId);
        $users = User::whereHas('teams', function ($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['manager', 'inspector']);
            })
            ->select('name as label', 'id as value')->get();

        /** @disregard P1013 */
        return Inertia::render('Equipment/Show', [
            'inspection' => $inspection,
            'inspection_details' => $inspection_details,
            'category_item' => $category_item,
            'users' => $users,
            'category_id' => $category_id,
            'inspection_id' => $inspection_id,
            'can' => [
                'inspection_show' => auth()->user()->can('inspection_show'),
                'inspection_edit' => false, //auth()->user()->can('inspection_edit'),
                'inspection_delete' => false, //auth()->user()->can('inspection_delete'),
                'inspection_create' => false, //auth()->user()->can('inspection_create'),
                'inspection_edit_reminder' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_inspector' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_date' => true, //auth()->user()->can('inspection_create'),
            ],
        ]);
    }




    public function equipmentsReplace(Request $request)
    {

        $data = $request->all();
        dd($data);
    }

    public function destroy(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->delete();

        return back();
    }
}
