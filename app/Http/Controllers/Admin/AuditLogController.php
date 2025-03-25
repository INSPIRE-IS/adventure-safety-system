<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        /** @disregard P1013 */
        abort_if(!auth()->user()->can('audit_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $searchTerm = $request->get('search', '');
        $sortColumn = $request->get('sort', 'description'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        $auditLogs = AuditLog::where('description', 'like', '%' . $searchTerm . '%')
            ->orderBy($sortColumn, $sortDirection)
            ->paginate(25);

        /** @disregard P1013 */
        return Inertia::render('AuditLog/Index', [
            'auditLogs' => $auditLogs,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
            'can' => [
                'audit_log_show' => auth()->user()->can('audit_log_show'),
                'audit_log_create' => false, // auth()->user()->can('audit_log_create'),
                'audit_log_edit' => false, // auth()->user()->can('audit_log_edit'),
                'audit_log_delete' => false, // auth()->user()->can('audit_log_delete'),

            ],
        ]);
    }

    public function show(AuditLog $auditLog)
    {
        //abort_if(Gate::denies('audit_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        //return view('admin.auditLogs.show', compact('auditLog'));
        //dd($auditLog);
        return Inertia::render('AuditLog/show', [
            //'auth' => auth()->user(),
            'auditLog' => $auditLog,
        ]);
    }
}
