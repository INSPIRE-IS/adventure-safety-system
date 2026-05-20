<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use Inertia\Inertia;
use Session;

class HomeController extends Controller
{
    //
    public function index(Request $request)
    {

        // /** @disregard P1013 */
        // abort_if(!auth()->user()->can('file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $teamId = (int) Session::get('team_id');
        if ($teamId <= 0) {
            $teamId = (int) (optional($user)->teams->first()?->id ?? 0);
            if ($teamId > 0) {
                Session::put('team_id', $teamId);
            }
        }

        setPermissionsTeamId($teamId > 0 ? $teamId : null);
        /** @disregard P1013 */
        $user->unsetRelation('roles')->unsetRelation('permissions');
        /** @disregard P1013 */
        $is_admin = $user->hasAnyRole(['admin', 'super', 'author', 'director']);
        $searchTerm = $request->get('search', $request->get('name', ''));
        $sortColumn = $request->get('sort', 'name'); // Default to 'name' if no sort is provided
        $sortDirection = $request->get('direction', 'asc');
        if ($is_admin) {
            $sites = Team::select('id', 'name')->with([
                'inspections:id,type_inspection_id,due_date,complete_date,site_id,assigned_to_id',
                'inspections.type_inspection:id,name',
                'drills:id,due_date,complete_date,type_drill_id,site_id,assigned_to_id',
                'drills.type_drill:id,name',
                'post_incidents',
                'post_incidents.type_incident:id,name', //.id,due_date,type_incident_id,site_id,assigned_to_id
            ])
                ->where('teams.name', 'like', '%' . $searchTerm . '%')
                ->orderBy($sortColumn, $sortDirection)
                ->get();
            // dd($sites);


            // Transform Data (Group by Site)
            $siteCards = $sites->map(function ($site) {
                $entries = collect();

                // Map Inspections
                foreach ($site->inspections as $inspection) {
                    $entries->push([
                        'name' => optional($inspection->type_inspection)->name ?? 'Unknown Inspection',
                        'due_date' => $inspection->due_date,
                        'complete_date' => $inspection->complete_date,
                        'type' => 'Inspection',
                        'assigned_to' => $inspection->assigned_to_id
                    ]);
                }

                // Map Drills
                foreach ($site->drills as $drill) {
                    $entries->push([
                        'name' => optional($drill->type_drill)->name ?? 'Unknown Drill',
                        'due_date' => $drill->due_date,
                        'complete_date' => $drill->complete_date,
                        'type' => 'Drill',
                        'assigned_to' => $drill->assigned_to_id
                    ]);
                }

                // Map Post Incidents
                foreach ($site->post_incidents as $incident) {
                    $entries->push([
                        'name' => optional($incident->type_incident)->name ?? 'Unknown Incident',
                        'due_date' => $incident->due_date,
                        'complete_date' => null, // No complete_date for incidents
                        'type' => 'Incident',
                        'assigned_to' => null
                    ]);
                }

                // Sort entries within the site by date (newest first)
                $sortedEntries = $entries->sortByDesc(function ($entry) {
                    return $entry['complete_date'] ?? $entry['due_date'];
                })->values();

                return [
                    'site_id' => $site->id,
                    'site_name' => $site->name,
                    'entries' => $sortedEntries
                ];
            });
        } else {
            $siteCards = Team::select('id', 'name')->with([
                'inspections:id,type_inspection_id,due_date,complete_date,site_id,assigned_to_id',
                'inspections.type_inspection:id,name',
                'drills:id,due_date,complete_date,type_drill_id,site_id,assigned_to_id',
                'drills.type_drill:id,name',
                'post_incidents:id,due_date,complete_date,type_incident_id,site_id,assigned_to_id',
                'post_incidents.type_incident:id,name',
            ])
                ->where('teams.id', $teamId)
                ->orderBy($sortColumn, $sortDirection)
                ->get()
                ->map(function ($site) {
                    return [
                        'site_name' => $site->name,
                        'categories' => [
                            [
                                'title' => 'Safety Inspections Management',
                                'icon'  => 'sim',
                                'entries' => $site->inspections->map(function ($inspection) {
                                    return [
                                        'name' => $inspection->type_inspection->name,
                                        'description' => 'Inspection conducted',
                                        'date' => $inspection->complete_date ?? $inspection->due_date,
                                        'status' => $inspection->complete_date ? 'Completed' : 'Pending',
                                    ];
                                }),
                            ],
                            [
                                'title' => 'Safety Drills Management',
                                'icon'  => 'sdm',
                                'entries' => $site->drills->map(function ($drill) {
                                    return [
                                        'name' => $drill->type_drill->name,
                                        'description' => 'Drill conducted',
                                        'date' => $drill->complete_date ?? $drill->due_date,
                                        'status' => $drill->complete_date ? 'Completed' : 'Pending',
                                    ];
                                }),
                            ],
                            [
                                'title' => 'PPE Inspection Management',
                                'icon'  => 'pim',
                                'entries' => $site->post_incidents->map(function ($incident) {
                                    return [
                                        'name' => $incident->type_incident->name,
                                        'description' => 'Incident recorded',
                                        'date' => $incident->due_date,
                                        'status' => 'Recorded',
                                    ];
                                }),
                            ],
                        ],
                    ];
                });
        }
        //dd($siteCards);

        /** @disregard P1013 */
        return Inertia::render('Dashboard', [
            'cardData' => $siteCards,
            'searchTerm' => $searchTerm,
            'sortColumn' => $sortColumn,
            'sortDirection' => $sortDirection,
        ]);
    }
}
