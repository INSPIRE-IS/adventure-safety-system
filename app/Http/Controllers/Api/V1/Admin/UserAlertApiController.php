<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\UserAlertResource;
use App\Models\UserAlert;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class UserAlertApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userAlert = UserAlert::updateOrCreate(
            [
                'id' => $request->id,
                'alert_param_id' => $request->alert_param_id,
            ],
            [
                'updated_at' => $request->updated_at,
            ]
        );

        $userId = $request->user_id;

        if ($userId) {
            $userAlert->users()->syncWithoutDetaching([
                $userId => ['read' => true]
            ]);
        }

        return (new UserAlertResource($userAlert))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
