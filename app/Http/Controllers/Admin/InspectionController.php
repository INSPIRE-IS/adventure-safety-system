<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyInspectionRequest;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use App\Jobs\SendMessageJob;
use App\Models\BulkMessage;
use App\Models\BulkMessageLog;
use App\Models\CategoryItem;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\SafetyInspectionDetail;
use App\Models\Team;
use App\Models\TypeBool;
use App\Models\TypeInspection;
use App\Models\User;
use App\Models\File;
use App\Models\UserAlert;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        //dd($request->all());
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('inspection_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $user = auth()->user();
        //dd($user->hasAnyRole(['admin', 'super']));
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());

        if ($request->get('filter', '') == 'period') {
            $searchPeriod = $request->get('value', '');
        }
        if ($request->get('filter', '') == 'period') {
            $searchMonth = $request->get('value', '');
        }
        if ($request->get('filter', '') == 'period') {
            $searchPeriod = $request->get('value', '');
        }
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'due_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $typeInspection = [1, 2]; // Monthly Safety Inspection Annual Safety Inspection  $request->get('typeInspection', 1);

        $inspections = Inspection::select('id', 'due_date', 'complete_date', 'inspection_schedule_id', 'assigned_to_id', 'photo_file_id', 'signature_file_id', 'site_id', 'created_at')
            ->with([
                'inspection_schedule:id,name,active_date,period_id,assigned_to_id,assigned_by_id,site_id,type_inspection_id,type_activity_id',
                'inspection_schedule.category_items:id,type_inspection_id,name',  // Add categoryItems here
                'assigned_to:id,name,surname',
                // 'photo_file:id,name,path,type_file_id,row',
                // 'signature_file:id,name,path,type_file_id,row',
            ])
            ->where('due_date', 'like', '%' . $searchTerm . '%')
            ->whereHas('inspection_schedule', function ($query) use ($typeInspection) {
                $query->whereIn('type_inspection_id', $typeInspection);
            })
            ->where('site_id', $teamId)
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

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
        $perPage = 10;
        $pagedData = $flattenedData->slice(($page - 1) * $perPage, $perPage)->values();
        $pagination = new LengthAwarePaginator($pagedData, $flattenedData->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
        $inspection_schedules = InspectionSchedule::select('name as label', 'id as value')->get();

        $assigned_tos = User::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');
        //$photo_files = File::pluck('path', 'id')->prepend(trans('global.pleaseSelect'), '');

        //$signature_files = File::pluck('path', 'id')->prepend(trans('global.pleaseSelect'), '');


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

        $periodSelects = InspectionSchedule::PERIOD_SELECT;
        $periods = [];
        foreach ($periodSelects as $value => $label) {
            $periods[] = [
                'value' => (string)$value,  // Convert value to string
                'label' => $label
            ];
        }
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


        /** @disregard P1013 */
        return Inertia::render('Inspection/Index', [
            'inspections' => $pagination,
            'sites' => $sites,
            'typeInspections' => $typeInspections,
            'categoryItems' => $categoryItems,
            'users' => $users,
            'typeBools' => $type_bools,
            'can' => [
                'inspection_show' => auth()->user()->can('inspection_show'),
                'inspection_edit' => false, //auth()->user()->can('inspection_edit'),
                'inspection_delete' => false, //auth()->user()->can('inspection_delete'),
                'inspection_create' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_reminder' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_inspector' => true, //auth()->user()->can('inspection_create'),
                'inspection_edit_date' => true, //auth()->user()->can('inspection_create'),
            ],
        ]);
    }

    public function store(StoreInspectionRequest $request)
    {
        $inspection = Inspection::create($request->all());

        return redirect()->route('admin.inspections.index');
    }

    public function update(UpdateInspectionRequest $request, Inspection $inspection)
    {
        $inspection->update($request->all());

        return redirect()->route('admin.inspections.index');
    }

    public function manage(Request $request)
    {
        //dd($request->all());
        $inspection = Inspection::find($request->input('inspection_id'));
        $category = CategoryItem::find($request->input('category_id'));

        if ($request->input('mode') == "change") {
            $inspection->update([
                'due_date' => $request->input('date'),
                'complete_date' => null,
                'assigned_to_id' => $request->input('assign'),
            ]);
            $category->update([
                'assigned_to_id' => $request->input('assign'),
            ]);
        }
        if ($request->input('mode') == "assign") {
            $inspection->update([
                'assigned_to_id' => $request->input('assign'),
            ]);
            $category->update([
                'assigned_to_id' => $request->input('assign'),
                'status' => "Active",
            ]);
        }

        if ($request->input('mode') == "reminder") {
            //$inspection->update([
            //'assigned_to_id' => $request->input('assign'),
            //]);
            $category->update([
                'assigned_to_id' => $request->input('assign'),
                'status' => "Active",
            ]);
        }
        // Assign users (same as step 2)
        // $message = BulkMessage::create([
        //     'subject' => 'Important Update',
        //     'body' => 'Here is an important update for you.',
        //     'site_id' => $inspection->site_id,
        //     'user_id' => $inspection->user_id,
        // ]);
        $subject = "You have been assigned to a monthly safety inspection.";
        $message = "Plese note that you have been assigned to a monthly safety inspection. Please complete the inspection as soon as possible.";

        // Dispatch a job for each recipient
        $users = [4, 5, 6];
        $recipients = User::whereIn('id', $users)->get();
        //dd($recipients);
        foreach ($recipients as $user) {
            Log::info("Sending message to: {$user}");
            SendMessageJob::dispatch($user->id, $inspection->site_id, $subject, $message);
        }
        $userAlert = UserAlert::create([
            'alert_text' => $subject,
            'alert_link' => '/(monthly_safety_inspection)',
        ]);
        $userAlert->users()->sync($users);
        return redirect()->back()->with('success', 'Inspection updated successfully.');
    }

    public function show(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $category = request('category');
        $user = auth()->user();
        $teamId = Session::get('team_id'); //setPermissionsTeamId(auth('api')->user()->getTeamIdFromToken());
        $inspection = Inspection::where('id', $inspection->id)->with('assigned_to:id,name,surname')->first();
        $inspection_details = SafetyInspectionDetail::where('inspection_id', $inspection->id)
            ->where('category_item_id', $category)
            ->with([
                'files',
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
        return Inertia::render('Inspection/Show', [
            'inspection' => $inspection,
            'inspection_details' => $inspection_details,
            'users' => $users,
            'category_id' => $category,
            'inspection_id' => $inspection->id,
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

    public function destroy(Inspection $inspection)
    {
        //abort_if(Gate::denies('inspection_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $inspection->delete();

        return back();
    }
}
