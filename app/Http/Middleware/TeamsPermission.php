<?php

namespace App\Http\Middleware;

use App\Models\Team;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\PermissionRegistrar;

class TeamsPermission
{

    public function handle($request, \Closure $next)
    {
        $debug = 0;

        if ($debug == 1) {
            Log::info('TeamsPermission do nothing');
            Log::info('Request method: ' . $request->method());
            Log::info('Request request: ' . $request);
            Log::info('auth()->check(): ' . auth()->check());
            if (auth()->check()) {
                Log::info('auth()->user()->teams->isNotEmpty(): ' . auth()->user()->teams->isNotEmpty());
                Log::info('auth()->user()->teams: ' . auth()->user()->teams);
                $teamId = auth()->user()->teams->first()->id;
                Log::info('User team ID web : ' . $teamId);
            } else {
                Log::info('No auth user');
            }
            if (!empty(auth('api')->user())) {
                Log::info("auth('api')->user(): " . auth('api')->user());
                Log::info('auth("api")->user()->teams: ' . auth('api')->user()->teams);
                $teamId = auth('api')->user()->teams->first()->id;
                Log::info('User team ID api : ' . $teamId);
            } else {
                Log::info('No api user');
            }
        }




        if (auth()->check() && auth()->user()->teams->isNotEmpty()) {
            // Retrieve the team ID from the first team (assuming a user can belong to multiple teams)
            $teamId = auth()->user()->teams->first()->id;
            // Set the team ID to the session (ensure this value is used for team-based permission logic)
            //session(['team_id' => $teamId]);
            // Call your custom function to set the permissions based on the team
            // setPermissionsTeamId($teamId);
        }
        // other custom ways to get team_id
        // dd(auth('api')->user());
        if (!empty(auth('api')->user())) {
            // example of custom method for getting the set team_id 
            //$user = auth('api')->user()->getTeamIdFromToken();

            // basically we need to set the team so we need to pass this with the api call
            // or we can get if from user gerAllTeamNames and set the team_id first.
            //$user = auth('api')->user();
            //$user = auth()->user();
            $team_id = $request->only('team_id');
            //dd($user, $team_id);
            //dd(method_exists($user, 'can'));
            //dd($user->can('type_bool_access'));
            if ($team_id != null) {
                setPermissionsTeamId($team_id["team_id"]);
            } else {
                setPermissionsTeamId(null);
            }

            //$user->load('teams', 'permissions', 'roles.permissions');
            //dd($user);
        }
        return $next($request);
    }
}
