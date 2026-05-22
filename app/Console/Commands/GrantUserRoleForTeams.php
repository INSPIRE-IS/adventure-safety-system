<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class GrantUserRoleForTeams extends Command
{
    /**
     * Examples:
     * - php artisan users:grant-role 10 operation
     * - php artisan users:grant-role 10 operation --team=2 --team=3
     * - php artisan users:grant-role 10 operation --dry-run
     *
     * Notes:
     * - This app uses Spatie teams mode, so role assignments are scoped by the global team context.
     */
    protected $signature = 'users:grant-role
        {user_id : The user id}
        {role : Role name (or id)}
        {--team=* : Team ids (defaults to all teams the user belongs to)}
        {--dry-run : Do not write; only print what would change}';

    protected $description = 'Grant a role to a user for one or more teams (Spatie teams mode).';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $role = $this->argument('role');

        /** @var \App\Models\User|null $user */
        $user = User::query()->with('teams')->find($userId);
        if (!$user) {
            $this->error("User {$userId} not found.");
            return self::FAILURE;
        }

        $teamIds = $this->option('team');
        $teamIds = \is_array($teamIds) ? $teamIds : [];
        $teamIds = array_values(array_filter(array_map(static fn ($id) => (int) $id, $teamIds), static fn (int $id) => $id > 0));

        if (empty($teamIds)) {
            $teamIds = $user->teams->pluck('id')->map(static fn ($id) => (int) $id)->all();
        }

        if (empty($teamIds)) {
            $this->warn("User {$user->id} has no teams; nothing to do.");
            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $this->line("User: {$user->id} {$user->email}");
        $this->line('Teams: ' . implode(', ', $teamIds));
        $this->newLine();

        $changes = 0;

        foreach ($teamIds as $teamId) {
            setPermissionsTeamId($teamId);
            /** @disregard P1013 */
            $user->unsetRelation('roles')->unsetRelation('permissions');

            $currentRoles = $user->getRoleNames();
            $currentRolesList = $currentRoles->isEmpty() ? '(none)' : $currentRoles->implode(', ');

            if ($user->hasRole($role)) {
                $this->line("Team {$teamId}: already has role '{$role}' (current: {$currentRolesList})");
                continue;
            }

            if ($dryRun) {
                $this->warn("Team {$teamId}: would grant role '{$role}' (current: {$currentRolesList})");
                $changes++;
                continue;
            }

            try {
                $user->assignRole($role);
            } catch (RoleDoesNotExist $e) {
                $this->error("Team {$teamId}: {$e->getMessage()}");
                $this->error("Hint: check `roles` table / `database/seeders/permissions.csv` for valid role names.");
                return self::FAILURE;
            }

            /** @disregard P1013 */
            $user->unsetRelation('roles')->unsetRelation('permissions');
            $afterRoles = $user->getRoleNames();
            $this->info("Team {$teamId}: granted role '{$role}' (now: " . ($afterRoles->isEmpty() ? '(none)' : $afterRoles->implode(', ')) . ')');
            $changes++;
        }

        $this->newLine();
        $this->info($dryRun ? "Dry-run complete ({$changes} team(s) would change)." : "Done ({$changes} team(s) changed).");

        return self::SUCCESS;
    }
}

