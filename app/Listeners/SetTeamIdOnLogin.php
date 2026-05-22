<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SetTeamIdOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;
        Log::info('Login Listener');
        if ($user) {
            Log::info('Login Listener ' . $user->id);

            //dd($user->load('teams'));
            // Set the team_id in the session
            // session(['team_id' => $user->currentTeam->id]); // Adjust based on your team logic
            //session(['team_id' => 1]); // Adjust based on your team logic

            /** @disregard P1013 */
            $user->loadMissing('teams');

            $teamIds = $user->teams->pluck('id')->values();

            // Prefer an existing session team_id (if it belongs to this user), otherwise choose a safe default.
            $team_id = (int) session()->get('team_id', 0);
            if ($team_id <= 0 || ($teamIds->isNotEmpty() && !$teamIds->contains($team_id))) {
                $team_id = (int) ($teamIds->first() ?? 0);
            }

            // If the chosen team has no roles for this user, fall back to the first team where they DO have roles.
            if ($team_id > 0 && $teamIds->count() > 1) {
                $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
                $modelHasPermissionsTable = config('permission.table_names.model_has_permissions', 'model_has_permissions');
                $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
                $morphKey = config('permission.column_names.model_morph_key', 'model_id');

                $rolesTable = config('permission.table_names.roles', 'roles');
                $hasRolesInChosenTeam = DB::table($modelHasRolesTable)
                    ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
                    ->where($morphKey, $user->getKey())
                    ->where('model_type', $user->getMorphClass())
                    ->where("{$modelHasRolesTable}.{$teamKey}", $team_id)
                    ->where(function ($q) use ($rolesTable, $teamKey, $team_id) {
                        $q->whereNull("{$rolesTable}.{$teamKey}")
                            ->orWhere("{$rolesTable}.{$teamKey}", $team_id);
                    })
                    ->exists();

                $hasDirectPermissionsInChosenTeam = DB::table($modelHasPermissionsTable)
                    ->where($morphKey, $user->getKey())
                    ->where('model_type', $user->getMorphClass())
                    ->where($teamKey, $team_id)
                    ->exists();

                if (!$hasRolesInChosenTeam && !$hasDirectPermissionsInChosenTeam) {
                    $fallbackTeamId = DB::table($modelHasRolesTable)
                        ->join($rolesTable, "{$rolesTable}.id", '=', "{$modelHasRolesTable}.role_id")
                        ->where("{$modelHasRolesTable}.{$morphKey}", $user->getKey())
                        ->where("{$modelHasRolesTable}.model_type", $user->getMorphClass())
                        ->whereIn("{$modelHasRolesTable}.{$teamKey}", $teamIds->all())
                        ->where(function ($q) use ($rolesTable, $teamKey, $modelHasRolesTable) {
                            $q->whereNull("{$rolesTable}.{$teamKey}")
                                ->orWhereColumn("{$rolesTable}.{$teamKey}", "{$modelHasRolesTable}.{$teamKey}");
                        })
                        ->orderBy("{$modelHasRolesTable}.{$teamKey}")
                        ->value("{$modelHasRolesTable}.{$teamKey}");

                    if (!$fallbackTeamId) {
                        $fallbackTeamId = DB::table($modelHasPermissionsTable)
                            ->where("{$modelHasPermissionsTable}.{$morphKey}", $user->getKey())
                            ->where("{$modelHasPermissionsTable}.model_type", $user->getMorphClass())
                            ->whereIn("{$modelHasPermissionsTable}.{$teamKey}", $teamIds->all())
                            ->orderBy("{$modelHasPermissionsTable}.{$teamKey}")
                            ->value("{$modelHasPermissionsTable}.{$teamKey}");
                    }

                    if ($fallbackTeamId) {
                        $team_id = (int) $fallbackTeamId;
                    }
                }
            }

            if ($team_id > 0) {
                session(['team_id' => $team_id]);
                setPermissionsTeamId($team_id);
            } else {
                session()->forget('team_id');
                setPermissionsTeamId(null);
            }
        }
    }
}
