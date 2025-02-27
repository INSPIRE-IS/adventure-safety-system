<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyPostIncidentRequest;
use App\Http\Requests\StorePostIncidentRequest;
use App\Http\Requests\UpdatePostIncidentRequest;
use App\Models\PostIncident;
use App\Models\PostIncidentSchedule;
use App\Models\Team;
use App\Models\TypeQuestion;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PostIncidentController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('post_incident_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'due_date'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $postIncidents = PostIncident::with(['post_incident_schedule.incident', 'report_file', 'assigned_to']) // 'site',
            ->where('due_date', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

        $post_incident_schedules = PostIncidentSchedule::select('name as label', 'id as value')->get();
        $sites = Team::select('name as label', 'id as value')->get();
        $users = User::select('name as label', 'id as value')->get();


        /** @disregard P1013 */
        return Inertia::render('PostIncident/Index', [
            'postIncidents' => $postIncidents,
            'post_incident_schedules' => $post_incident_schedules,
            'sites' => $sites,
            'users' => $users,
            'can' => [
                'post_incident_show' => false, //auth()->user()->can('post_incident_show'),
                'post_incident_create' => false, //auth()->user()->can('post_incident_create'),
                'post_incident_edit' => auth()->user()->can('post_incident_edit'),
                'post_incident_delete' => auth()->user()->can('post_incident_delete'),

            ],
        ]);
    }

    public function create()
    {
        //abort_if(Gate::denies('post_incident_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post_incident_schedules = PostIncidentSchedule::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $questions = TypeQuestion::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.postIncidents.create', compact('post_incident_schedules', 'questions'));
    }

    public function store(StorePostIncidentRequest $request)
    {
        $postIncident = PostIncident::create($request->all());

        return redirect()->route('admin.post-incidents.index');
    }

    public function edit(PostIncident $postIncident)
    {
        //abort_if(Gate::denies('post_incident_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $post_incident_schedules = PostIncidentSchedule::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $questions = TypeQuestion::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $postIncident->load('post_incident_schedule', 'report_file', 'team');

        return view('admin.postIncidents.edit', compact('postIncident', 'post_incident_schedules', 'questions'));
    }

    public function update(UpdatePostIncidentRequest $request, PostIncident $postIncident)
    {
        $postIncident->update($request->all());

        return redirect()->route('admin.post-incidents.index');
    }

    public function show(PostIncident $postIncident)
    {
        //abort_if(Gate::denies('post_incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncident->load('post_incident_schedule', 'report_file', 'team');

        return view('admin.postIncidents.show', compact('postIncident'));
    }

    public function destroy(PostIncident $postIncident)
    {
        //abort_if(Gate::denies('post_incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncident->delete();

        return back();
    }
}
