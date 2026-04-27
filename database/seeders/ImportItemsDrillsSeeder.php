<?php

namespace Database\Seeders;

use App\Models\TypeDrill;
use Illuminate\Database\Seeder;

class ImportItemsDrillsSeeder extends Seeder
{
    public function run(): void
    {
        $drills = [
            ['name' => 'Normal Client Belay'],
            ['name' => 'Lever Hoist Belay'],
            ['name' => 'Self Belay'],
            ['name' => 'Zipline Retrieval'],
        ];

        $total = 0;

        foreach ($drills as $drill) {
            $name = trim((string) ($drill['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            TypeDrill::withTrashed()->updateOrCreate(
                ['name' => $name],
                ['deleted_at' => null]
            );
            $total++;
        }

        if ($this->command) {
            $this->command->info("Seeded {$total} drill types.");
        }
    }
}
