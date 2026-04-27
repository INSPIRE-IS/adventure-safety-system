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

class ImportItemsInspectionSeeder extends Seeder
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
                $this->command->warn('No teams found to seed inspection items for.');
            }
            return;
        }

        $typeInspectionId = TypeInspection::query()
            ->where('name', 'Monthly Safety Inspection')
            ->value('id') ?? 1;

        $certificateRecievedId = TypeBool::query()
            ->where('name', 'No')
            ->value('id') ?? 2;

        $items = [
            [
                "item_name" => "Decline catwalk",
                "details" => [
                    "Pole",
                    "Platform",
                    "Steel pipe",
                    "Belay System",
                    "Spreader bars"
                ]
            ],
            [
                "item_name" => "1st Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zipline cable",
                    "Stays and anchors",
                    "Belay System"
                ]
            ],
            [
                "item_name" => "Swinging Platform",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Swinging Platforms",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Vertical net",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Poles",
                    "Belay System",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Burma log",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Log",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "2nd Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zipline cable",
                    "Belay System",
                    "Stays and achors"
                ]
            ],
            [
                "item_name" => "Burma Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Plank Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Rigging",
                    "Belay System",
                    "Stay Cables and Anchors",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "3rd Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zipline cable",
                    "Stays and anchors",
                    "Belay System"
                ]
            ],
            [
                "item_name" => "Multi vines",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Foot cable",
                    "Belay System",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Flat platform",
                "details" => [
                    "Pole",
                    "Platform",
                    "Decking",
                    "Stay Cables and Anchors",
                    "Belay System"
                ]
            ],
            [
                "item_name" => "Cat walk",
                "details" => [
                    "Pole",
                    "Platform",
                    "Belay System",
                    "Spreader bar",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "River raft",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Belay System",
                    "Rigging",
                    "Round small poles",
                    "Spreader bar",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Train tracks",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Tree",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Mine charts",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Decking",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Spreader bars",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Organ pipes",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Stay cables and anchors",
                    "Logs",
                    "Spreader bars",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "4th Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Stays and anchors",
                    "Zipline Cable",
                    "Belay System"
                ]
            ],
            [
                "item_name" => "Step across",
                "details" => [
                    "Pole",
                    "Platform",
                    "Stays and anchors",
                    "Belay System",
                    "Spreader bar",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Mixed Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Logs",
                    "Belay System",
                    "Spreader Bar",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => " 5th Zip Line",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zip Line Cable",
                    "Stay Cables and Anchors",
                    "Belay System"
                ]
            ],
            [
                "item_name" => "Indi Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Stays and anchors",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Loops postmans walk",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Foot cable",
                    "Belay System",
                    "Stepladder",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => " Swinging Tyres",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Tyres",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Spider net",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Spreader bar",
                    "Belay System",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "6th Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Spreader bars",
                    "Belay System",
                    "Zipline cable",
                    "Stays and anchors"
                ]
            ],
            [
                "item_name" => "Zig zag",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Timber",
                    "Belay System",
                    "Rigging",
                    "Spreader bars",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Snake Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Snake Bridge",
                    "Belay System",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Burma Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Rigging",
                    "Belay System",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "7th Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Stays and anchors",
                    "Zipline cable",
                    "Belay System",
                    "Spreader bars"
                ]
            ],
            [
                "item_name" => "Hanging logs",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Logs",
                    "Belay System",
                    "Spreader bars",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Walking deck",
                "details" => [
                    "Pole",
                    "Platform",
                    "Decking wood",
                    "Belay System",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Burma logs",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Logs",
                    "Belay System",
                    "Spreader bars",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Chop sticks",
                "details" => [
                    "Poles",
                    "Platform",
                    "Combi Rope",
                    "Spreader bars",
                    "Belay System",
                    "Rigging",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "8th Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zipline cable",
                    "Stays and anchors",
                    "Belay system"
                ]
            ],
            [
                "item_name" => "Indi Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Combi Rope",
                    "Stays and anchors",
                    "Belay System",
                    "Spreader bars",
                    "Rigging"
                ]
            ],
            [
                "item_name" => "9th Zipline",
                "details" => [
                    "Pole",
                    "Platform",
                    "Zipline cable",
                    "Stays and anchors",
                    "Rigging",
                    "Belay system",
                    "Spreader bars"
                ]
            ],
            [
                "item_name" => "Plank bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Spreader bars",
                    "Stays and anchors",
                    "Belay system",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Indi Bridge",
                "details" => [
                    "Pole",
                    "Platform",
                    "Spreader bars",
                    "Rigging",
                    "Combi rope",
                    "logs",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Walking deck",
                "details" => [
                    "Pole",
                    "Platform",
                    "Belay system",
                    "Steel pipe"
                ]
            ],
            [
                "item_name" => "Main platform",
                "details" => [
                    "Poles",
                    "Decking ",
                    "Gates",
                    "Belay system",
                    "Combi rope",
                    "Harness wrack",
                    "Steel pipe"
                ]
            ],
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
            $this->command->info("Seeded {$totalItems} inspection items and {$totalDetails} item details.");
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
