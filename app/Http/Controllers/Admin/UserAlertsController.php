<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAlertRequest;
use App\Http\Requests\UpdateUserAlertRequest;
use App\Models\User;
use App\Models\UserAlert;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class UserAlertsController extends Controller
{
    public function index(Request $request)
    {
        dd('here');
        abort_if(Gate::denies('user_alert_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        /** @disregard P1013 */
        //abort_if(!auth()->user()->can('type_bool_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $userAlerts = UserAlert::with(['users'])
            ->where('name', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);
        /** @disregard P1013 */
        return Inertia::render('UserAlert/Index', [
            'userAlerts' => $userAlerts,
            'can' => [
                'type_bool_show' => false, //auth()->user()->can('type_bool_show'),
                'type_bool_create' => false, //auth()->user()->can('type_bool_create'),
                'type_bool_edit' => false, //auth()->user()->can('type_bool_edit'),
                'type_bool_delete' => false, //auth()->user()->can('type_bool_delete'),

            ],
        ]);
    }

    public function create()
    {
        abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $users = User::pluck('name', 'id');

        return view('admin.userAlerts.create', compact('users'));
    }

    public function store(StoreUserAlertRequest $request)
    {
        $userAlert = UserAlert::create($request->all());
        $userAlert->users()->sync($request->input('users', []));

        return redirect()->route('admin.user-alerts.index');
    }

    public function edit(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->load('users');

        return view('admin.userAlerts.edit', compact('userAlert'));
    }

    public function update(UpdateUserAlertRequest $request, UserAlert $userAlert)
    {
        $userAlert->update($request->all());

        return redirect()->route('admin.user-alerts.index');
    }

    public function show(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->load('users');

        return view('admin.userAlerts.show', compact('userAlert'));
    }

    public function destroy(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->delete();

        return back();
    }

    public function read(Request $request)
    {
        $alerts = \Auth::user()->userUserAlerts()->where('read', false)->get();
        foreach ($alerts as $alert) {
            $pivot       = $alert->pivot;
            $pivot->read = true;
            $pivot->save();
        }
    }
}
