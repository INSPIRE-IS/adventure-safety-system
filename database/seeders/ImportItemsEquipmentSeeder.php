<?php

namespace Database\Seeders;

use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use App\Models\Team;
use App\Models\TypeBool;
use App\Models\TypeInspection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportItemsEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $teamId = env('SEED_TEAM_ID');

        $teamsQuery = Team::query()->select('id');
        if ($teamId !== null && $teamId !== '') {
            $teamsQuery->whereKey((int) $teamId);
        }

        $teams = $teamsQuery->get();
        if ($teams->isEmpty()) {
            if ($this->command) {
                $this->command->warn('No teams found to seed equipment items for.');
            }
            return;
        }

        $typeInspectionId = TypeInspection::query()
            ->where('name', 'PPE Inspections')
            ->value('id') ?? 3;

        $certificateRecievedId = TypeBool::query()
            ->where('name', 'No')
            ->value('id') ?? 2;

        $items = [
            ['item_name' => 'Trolleys', 'details' => []],
            ['item_name' => 'Lanyards', 'details' => []],
            ['item_name' => 'Carabiners', 'details' => []],
            ['item_name' => 'Harnesses', 'details' => []],
            ['item_name' => 'Maillons', 'details' => []],
        ];

        $totalItems = 0;
        $totalDetails = 0;

        foreach ($teams as $team) {
            $seenNames = [];

            foreach ($items as $itemData) {
                $baseName = trim((string) ($itemData['item_name'] ?? ''));
                if ($baseName === '') {
                    continue;
                }

                $name = $this->uniqueName($baseName, $seenNames);

                $categoryItem = CategoryItem::withTrashed()->updateOrCreate(
                    [
                        'site_id' => $team->id,
                        'type_inspection_id' => $typeInspectionId,
                        'name' => $name,
                    ],
                    [
                        'certificate_recieved_id' => $certificateRecievedId,
                        'deleted_at' => null,
                    ]
                );
                $totalItems++;

                $details = Arr::wrap($itemData['details'] ?? []);
                foreach ($details as $detail) {
                    $detailName = trim((string) $detail);
                    if ($detailName === '') {
                        continue;
                    }

                    CategoryDetail::withTrashed()->updateOrCreate(
                        [
                            'category_item_id' => $categoryItem->id,
                            'name' => $detailName,
                        ],
                        [
                            'in_use_date' => now()->toDateTimeString(),
                            'out_use_date' => null,
                            'deleted_at' => null,
                        ]
                    );
                    $totalDetails++;
                }
            }
        }

        if ($this->command) {
            $this->command->info("Seeded {$totalItems} equipment items and {$totalDetails} item details.");
            $this->command->info('Tip: set SEED_TEAM_ID to seed a single team.');
        }
    }

    private function uniqueName(string $baseName, array &$seenNames): string
    {
        $key = Str::lower($baseName);
        $seenNames[$key] = ($seenNames[$key] ?? 0) + 1;

        if ($seenNames[$key] === 1) {
            return $baseName;
        }

        return $baseName . ' (' . $seenNames[$key] . ')';
    }
}
