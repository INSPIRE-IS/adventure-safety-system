<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TeamsPermission
{
    public function handle(Request $request, Closure $next)
    {
        // WEB: scope permissions by the current session site/team.
        /** @var \App\Models\User|null $webUser */
        $webUser = auth()->user();
        if ($webUser) {
            /** @disregard P1013 */
            $webUser->loadMissing('teams');

            $teamIds = $webUser->teams->pluck('id')->values();
            $teamId = (int) $request->session()->get('team_id', 0);

            if ($teamId <= 0 || ($teamIds->isNotEmpty() && !$teamIds->contains($teamId))) {
                $teamId = (int) ($teamIds->first() ?? 0);
            }

            // If the chosen team has no roles/permissions, fall back to a team where the user *does* have access.
            if ($teamId > 0 && $teamIds->count() > 1) {
                $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
                $modelHasPermissionsTable = config('permission.table_names.model_has_permissions', 'model_has_permissions');
                $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
                $morphKey = config('permission.column_names.model_morph_key', 'model_id');

                $hasRolesInChosenTeam = DB::table($modelHasRolesTable)
                    ->where($morphKey, $webUser->getKey())
                    ->where('model_type', $webUser->getMorphClass())
                    ->where($teamKey, $teamId)
                    ->exists();

                $hasDirectPermissionsInChosenTeam = DB::table($modelHasPermissionsTable)
                    ->where($morphKey, $webUser->getKey())
                    ->where('model_type', $webUser->getMorphClass())
                    ->where($teamKey, $teamId)
                    ->exists();

                if (!$hasRolesInChosenTeam && !$hasDirectPermissionsInChosenTeam) {
                    $fallbackTeamId = DB::table($modelHasRolesTable)
                        ->where($morphKey, $webUser->getKey())
                        ->where('model_type', $webUser->getMorphClass())
                        ->whereIn($teamKey, $teamIds->all())
                        ->orderBy($teamKey)
                        ->value($teamKey);

                    if ($fallbackTeamId) {
                        $teamId = (int) $fallbackTeamId;
                    }
                }
            }

            if ($teamId > 0) {
                $request->session()->put('team_id', $teamId);
            } else {
                $request->session()->forget('team_id');
            }

            setPermissionsTeamId($teamId > 0 ? $teamId : null);
            /** @disregard P1013 */
            $webUser->unsetRelation('roles')->unsetRelation('permissions');
        }

        // API: validate requested team_id belongs to the user (prevents cross-team spoofing).
        /** @var \App\Models\User|null $apiUser */
        $apiUser = auth('api')->user();
        if ($apiUser) {
            $requestedTeamIdRaw = $request->input('team_id');
            $teamId = (int) (optional($apiUser)->teams->first()?->id ?? 0);

            if ($requestedTeamIdRaw !== null && $requestedTeamIdRaw !== '') {
                $requestedTeamId = (int) $requestedTeamIdRaw;
                abort_if(!$apiUser->teams->contains('id', $requestedTeamId), Response::HTTP_FORBIDDEN, 'Invalid team_id for user');
                $teamId = $requestedTeamId;
            } elseif ($teamId > 0) {
                // Backwards compatibility: if the client didn't send team_id, default it so controllers can still use it.
                $request->merge(['team_id' => $teamId]);
            }

            setPermissionsTeamId($teamId > 0 ? $teamId : null);
            /** @disregard P1013 */
            $apiUser->unsetRelation('roles')->unsetRelation('permissions');
        }

        return $next($request);
    }
}
