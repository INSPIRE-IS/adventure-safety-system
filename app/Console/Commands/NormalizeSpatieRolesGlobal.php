<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class NormalizeSpatieRolesGlobal extends Command
{
    protected $signature = 'spatie:roles-normalize-global
        {--apply : Apply changes (default is dry-run)}
        {--delete-legacy : Delete merged team-scoped role rows after rewiring pivots}';

    protected $description = 'Normalize Spatie team-scoped roles into global roles (team_id NULL) and rewire pivot assignments safely.';

    public function handle(): int
    {
        if (!config('permission.teams')) {
            $this->warn('Spatie teams mode is disabled (config(permission.teams) = false); nothing to normalize.');
            return self::SUCCESS;
        }

        $apply = (bool) $this->option('apply');
        $deleteLegacy = (bool) $this->option('delete-legacy');

        if ($deleteLegacy && !$apply) {
            $this->error('`--delete-legacy` requires `--apply`.');
            return self::FAILURE;
        }

        $teamKey = config('permission.column_names.team_foreign_key', 'team_id');
        $rolesTable = config('permission.table_names.roles', 'roles');
        $modelHasRolesTable = config('permission.table_names.model_has_roles', 'model_has_roles');
        $roleHasPermissionsTable = config('permission.table_names.role_has_permissions', 'role_has_permissions');
        $pivotRole = config('permission.column_names.role_pivot_key', 'role_id');
        $pivotPermission = config('permission.column_names.permission_pivot_key', 'permission_id');
        $morphKey = config('permission.column_names.model_morph_key', 'model_id');

        /** @var \Illuminate\Support\Collection<int, object> $allRoles */
        $allRoles = DB::table($rolesTable)
            ->select(['id', 'name', 'guard_name', $teamKey])
            ->orderBy('id')
            ->get();

        if ($allRoles->isEmpty()) {
            $this->info('No roles found; nothing to normalize.');
            return self::SUCCESS;
        }

        /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, object>> $groups */
        $groups = $allRoles->groupBy(fn (object $r) => "{$r->name}|{$r->guard_name}");

        $this->line($apply ? 'Mode: APPLY' : 'Mode: DRY-RUN');
        $this->line("Roles: {$allRoles->count()} (groups: {$groups->count()})");
        $this->newLine();

        $updatedCanonical = 0;
        $rewiredAssignments = 0;
        $mergedRolePermissions = 0;
        $deletedLegacyRoles = 0;

        foreach ($groups as $groupKey => $roles) {
            /** @var object|null $canonical */
            $canonical = $roles->first(fn (object $r) => $r->{$teamKey} === null);
            if (!$canonical) {
                $canonical = $roles->first();
            }

            if (!$canonical) {
                continue;
            }

            $canonicalId = (int) $canonical->id;
            $canonicalTeamId = $canonical->{$teamKey};
            $roleName = (string) $canonical->name;
            $guardName = (string) $canonical->guard_name;

            $others = $roles->filter(fn (object $r) => (int) $r->id !== $canonicalId)->values();
            $hasCanonicalUpdate = $canonicalTeamId !== null;

            if (!$hasCanonicalUpdate && $others->isEmpty()) {
                continue;
            }

            $this->line("Role '{$roleName}' [{$guardName}] => canonical id {$canonicalId}" . ($hasCanonicalUpdate ? " (will set {$teamKey}=NULL)" : '') . ($others->isNotEmpty() ? " (merge {$others->count()} legacy)" : ''));

            if (!$apply) {
                continue;
            }

            DB::transaction(function () use (
                $hasCanonicalUpdate,
                $rolesTable,
                $teamKey,
                $canonicalId,
                $others,
                $modelHasRolesTable,
                $pivotRole,
                $morphKey,
                $roleHasPermissionsTable,
                $pivotPermission,
                $deleteLegacy,
                &$updatedCanonical,
                &$rewiredAssignments,
                &$mergedRolePermissions,
                &$deletedLegacyRoles
            ): void {
                if ($hasCanonicalUpdate) {
                    DB::table($rolesTable)
                        ->where('id', $canonicalId)
                        ->update([$teamKey => null]);
                    $updatedCanonical++;
                }

                foreach ($others as $legacyRole) {
                    $legacyRoleId = (int) $legacyRole->id;

                    // Rewire model_has_roles by inserting the canonical row (ignore duplicates) then deleting the legacy row.
                    $legacyAssignments = DB::table($modelHasRolesTable)
                        ->where($pivotRole, $legacyRoleId)
                        ->get([$teamKey, $morphKey, 'model_type']);

                    foreach ($legacyAssignments as $assignment) {
                        $inserted = DB::table($modelHasRolesTable)->insertOrIgnore([[
                            $teamKey => (int) $assignment->{$teamKey},
                            $pivotRole => $canonicalId,
                            $morphKey => (int) $assignment->{$morphKey},
                            'model_type' => (string) $assignment->model_type,
                        ]]);

                        // Always delete the legacy row, since the canonical row now exists (or already existed).
                        DB::table($modelHasRolesTable)->where([
                            $teamKey => (int) $assignment->{$teamKey},
                            $pivotRole => $legacyRoleId,
                            $morphKey => (int) $assignment->{$morphKey},
                            'model_type' => (string) $assignment->model_type,
                        ])->delete();

                        $rewiredAssignments += ($inserted ? 1 : 0);
                    }

                    // Merge role_has_permissions by inserting missing canonical rows (ignore duplicates).
                    $legacyPermissions = DB::table($roleHasPermissionsTable)
                        ->where($pivotRole, $legacyRoleId)
                        ->get([$pivotPermission]);

                    foreach ($legacyPermissions as $rp) {
                        $inserted = DB::table($roleHasPermissionsTable)->insertOrIgnore([[
                            $pivotPermission => (int) $rp->{$pivotPermission},
                            $pivotRole => $canonicalId,
                        ]]);

                        $mergedRolePermissions += ($inserted ? 1 : 0);
                    }

                    if ($deleteLegacy) {
                        DB::table($roleHasPermissionsTable)->where($pivotRole, $legacyRoleId)->delete();
                        DB::table($rolesTable)->where('id', $legacyRoleId)->delete();
                        $deletedLegacyRoles++;
                    }
                }
            });
        }

        if ($apply) {
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        $this->newLine();
        $this->info('Summary:');
        $this->line("- Canonical roles updated to global: {$updatedCanonical}");
        $this->line("- model_has_roles rewired (new canonical rows inserted): {$rewiredAssignments}");
        $this->line("- role_has_permissions merged (new canonical rows inserted): {$mergedRolePermissions}");
        $this->line("- Legacy roles deleted: {$deletedLegacyRoles}");

        if ($apply) {
            $this->newLine();
            $this->line('Next: run `php artisan permission:cache-reset` if you see stale permission behaviour.');
        }

        return self::SUCCESS;
    }
}
