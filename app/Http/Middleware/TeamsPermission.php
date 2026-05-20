<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamsPermission
{
    public function handle(Request $request, Closure $next)
    {
        // WEB: scope permissions by the current session site/team.
        /** @var \App\Models\User|null $webUser */
        $webUser = auth()->user();
        if ($webUser) {
            $teamId = (int) $request->session()->get('team_id', 0);
            if ($teamId <= 0) {
                $teamId = (int) (optional($webUser)->teams->first()?->id ?? 0);
                if ($teamId > 0) {
                    $request->session()->put('team_id', $teamId);
                }
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
