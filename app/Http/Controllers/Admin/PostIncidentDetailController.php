<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostIncidentDetailRequest;
use App\Http\Requests\UpdatePostIncidentDetailRequest;
use App\Models\PostIncident;
use App\Models\PostIncidentDetail;
use App\Models\TypeQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PostIncidentDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('post_incident_detail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'post_incident_id'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $postIncidentDetails = PostIncidentDetail::with(['user', 'question', 'post_incident'])
            ->where('post_incident_id', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

        $postIncidents = PostIncident::select('due_date as label', 'id as value')->get();
        $users = User::select('name as label', 'id as value')->get();
        $typeQuestions = TypeQuestion::select('name as label', 'id as value')->get();


        /** @disregard P1013 */
        return Inertia::render('PostIncidentDetail/Index', [
            'postIncidentDetails' => $postIncidentDetails,
            'postIncidents' => $postIncidents,
            'users' => $users,
            'typeQuestions' => $typeQuestions,
            'can' => [
                'post_incident_detail_show' => false, //auth()->user()->can('post_incident_detail_show'),
                'post_incident_detail_create' => false, //auth()->user()->can('post_incident_detail_create'),
                'post_incident_detail_edit' => auth()->user()->can('post_incident_detail_edit'),
                'post_incident_detail_delete' => auth()->user()->can('post_incident_detail_delete'),

            ],
        ]);
    }


    public function store(StorePostIncidentDetailRequest $request)
    {
        $postIncidentDetail = PostIncidentDetail::create($request->all());

        return redirect()->route('post-incident-details.index');
    }

    public function update(UpdatePostIncidentDetailRequest $request, PostIncidentDetail $postIncidentDetail)
    {
        $postIncidentDetail->update($request->all());

        return redirect()->route('post-incident-details.index');
    }

    public function show(PostIncidentDetail $postIncidentDetail)
    {
        //abort_if(Gate::denies('type_incident_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('post-incident-details.show', compact('postIncidentDetail'));
    }

    public function destroy(PostIncidentDetail $postIncidentDetail)
    {
        //abort_if(Gate::denies('type_incident_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $postIncidentDetail->delete();

        return back();
    }
}
