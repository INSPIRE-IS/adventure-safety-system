<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

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

            $team_id = optional($user)->teams->first()->id ?? 0;
            session(['team_id' => $team_id]);
            setPermissionsTeamId($team_id);
        }
    }
}
