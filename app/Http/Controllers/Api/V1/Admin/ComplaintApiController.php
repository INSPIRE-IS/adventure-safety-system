<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Http\Resources\Admin\ComplaintResource;
use App\Models\Complaint;
use Gate;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class ComplaintApiController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('complaint_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ComplaintResource(Complaint::with(['site', 'team'])->get());
    }

    public function store(StoreComplaintRequest $request)
    {
        Log::info('ComplaintApiController.store', [
            'auth_user_id' => $request->user()?->id,
            'payload_keys' => array_keys($request->all()),
            'site_id' => $request->input('site_id'),
            'user_id' => $request->input('user_id'),
            'complaint_date' => $request->input('complaint_date'),
            'title_len' => strlen((string) $request->input('title', '')),
            'description_len' => strlen((string) $request->input('description', '')),
        ]);
        $complaint = Complaint::updateOrCreate(
            [
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
                'uuid' => $request->id,
            ],
            [
                'name' => $request->name,
                'phone' => $request->phone ?? ' ',
                'email' => $request->email ?? ' ',
                'title' => $request->title ?? ' ',
                'description' => $request->description ?? ' ',
                'complaint_date' => $request->complaint_date,
            ]
        );

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Complaint $complaint)
    {
        abort_if(Gate::denies('complaint_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ComplaintResource($complaint->load(['site', 'team']));
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
        $complaint->update($request->all());

        return (new ComplaintResource($complaint))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Complaint $complaint)
    {
        abort_if(Gate::denies('complaint_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $complaint->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
