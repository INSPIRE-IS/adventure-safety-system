<?php

namespace App\Console\Commands;

use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use Illuminate\Console\Command;

class ImportItems extends Command
{
    // Define the signature and description for the command
    protected $signature = 'import:items
        {--site= : Team/site ID to import into (required)}
        {--type=1 : type_inspection_id to import into}
        {--dataset= : Legacy dataset to use (site-1|site-2). Defaults based on --site=1/2}';
    protected $description = 'LEGACY: Import initial seed items/details (hardcoded dataset). Prefer the CSV import UI; confirm legacy data before re-using.';

    // The constructor for the command
    public function __construct()
    {
        parent::__construct();
    }

    // The handle method will contain the logic to perform when the command is executed
    public function handle()
    {
        $siteId = (int) ($this->option('site') ?? 0);
        if ($siteId <= 0) {
            $this->error('Missing required --site=ID (team/site id).');
            return self::FAILURE;
        }

        $typeInspectionId = (int) ($this->option('type') ?? 1);
        if ($typeInspectionId <= 0) {
            $this->error('Invalid --type (type_inspection_id).');
            return self::FAILURE;
        }

        $dataset = trim((string) ($this->option('dataset') ?? ''));
        $certificateRecievedId = 2;

        // Define the array with item names and related details 33 194
        $itemsData1 = [
            [
                'item_name' => 'Wobbly Walkway',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Decking', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Plank Bridge',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Plank Bridge', 'Belay System']
            ],
            [
                'item_name' => 'Swinging Platform',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Swinging Platforms', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Stepping Poles',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Poles', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => 'Floating Rafts',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Floating Rafts', 'Belay System', 'Spreader bars']
            ],
            [
                'item_name' => 'Multi Vines',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Belay System', 'Walking Cable', 'Rigging']
            ],
            [
                'item_name' => 'Burma Bridge',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => '35m Zip Line',
                'details' => ['Pole', 'Platform', 'Zipline Cable', 'Belay System', 'Stay Cables and Anchors']
            ],
            [
                'item_name' => 'Wobbling Log',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Wobbling log', 'Belay System']
            ],
            [
                'item_name' => 'Flying Surfboards',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Surfboards', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => '25m Zip Line',
                'details' => ['Pole', 'Platform', 'Zip Line Cable', 'Stay Cables and Anchors', 'Belay System']
            ],
            [
                'item_name' => 'Swinging Tyres',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Tyres', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Vertical net',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => 'Hanging Logs',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Hanging Logs', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Step Across',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Step Across Platforms', 'Belay System']
            ],
            [
                'item_name' => 'Indi Bridge',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Decline Log',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Log and attachment', 'Belay System']
            ],
            [
                'item_name' => 'Incline Log',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Log and attachment', 'Belay System']
            ],
            [
                'item_name' => 'Net Walk with Fenders',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Fenders', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => '20m Zip Line',
                'details' => ['Pole', 'Platform', 'Zip Line Cable', 'Stay Cables and Anchors', 'Belay System']
            ],
            [
                'item_name' => 'Flying Skateboards',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Skateboards', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Zig Zag',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Zig Zags', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => 'Flea Jumps',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Flea Jump Poles', 'Belay System']
            ],
            [
                'item_name' => 'Criss Cross',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Criss Crosses', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => 'Burma Bridge',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Belay System', 'Rigging', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Chopsticks',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Poles and Timber', 'Belay System', 'Rigging']
            ],
            [
                'item_name' => 'Snake Bridge',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Snake Bridge', 'Belay System']
            ],
            [
                'item_name' => 'Spiders Web',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Rigging', 'Belay System', 'Staircase']
            ],
            [
                'item_name' => 'Organ Pipes',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Poles', 'Belay System', 'Spreader bars', 'Rigging']
            ],
            [
                'item_name' => 'Burma Logs',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Burma logs', 'Belay System', 'Spreader bars']
            ],
            [
                'item_name' => 'Catwalk',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Walkway', 'Belay System', 'Staircase']
            ],
            [
                'item_name' => 'Walking Deck',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Deck', 'Belay System']
            ],
            [
                'item_name' => 'Big Zip Line',
                'details' => ['Pole', 'Platform', 'Combi Rope', 'Stay Cables and Anchor Points', 'Belay System', 'Exit Staircase']
            ]
        ];
        // 40 264
        $itemsData2 = [
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

        $itemsData = null;
        if ($dataset !== '') {
            $datasetKey = strtolower($dataset);
            if (in_array($datasetKey, ['site-1', 'site1', '1'], true)) {
                $itemsData = $itemsData1;
            } elseif (in_array($datasetKey, ['site-2', 'site2', '2'], true)) {
                $itemsData = $itemsData2;
            }
        }

        if ($itemsData === null) {
            if ($siteId === 1) {
                $itemsData = $itemsData1;
            } elseif ($siteId === 2) {
                $itemsData = $itemsData2;
            }
        }

        if ($itemsData === null) {
            $this->error('Unknown legacy dataset. Use --dataset=site-1 or --dataset=site-2 (or pass --site=1/2).');
            return self::FAILURE;
        }

        foreach ($itemsData as $itemData) {
            // Insert the item name
            //$item = CategoryItem::updateOrCreate(['name' => $itemData['item_name'],'type_inspection_id' => 1,'site_id' => 2],['certificate_recieved_id' => 2]);
            $item = CategoryItem::withTrashed()->firstOrCreate(
                [
                    'name' => $itemData['item_name'],
                    'type_inspection_id' => $typeInspectionId,
                    'site_id' => $siteId,
                ],
                [
                    'certificate_recieved_id' => $certificateRecievedId,
                ]
            );

            if ($item->deleted_at !== null) {
                $item->deleted_at = null;
            }
            $item->certificate_recieved_id = $certificateRecievedId;
            $item->save();

            $detailsCount = 0;

            // Loop through each of the item's details and insert them into the 'item_details' table
            foreach ($itemData['details'] as $detail) {
                //CategoryDetail::updateOrCreate(['category_item_id' => $item->id,'name' => $detail,],['in_use_date' => now(),'out_use_date' => '']);
                $detailModel = CategoryDetail::withTrashed()->firstOrCreate(
                    [
                        'category_item_id' => $item->id,
                        'name' => $detail,
                    ],
                    [
                        'in_use_date' => now()->toDateTimeString(),
                        'out_use_date' => null,
                    ]
                );

                if ($detailModel->deleted_at !== null) {
                    $detailModel->deleted_at = null;
                    $detailModel->save();
                }
                $detailsCount++;
                $totalDetails++;
            }

            $totalItems++;

            // Output a success message for this item with detail count
            $this->info("Item '{$itemData['item_name']}' and its {$detailsCount} details have been imported.");
        }

        // Output summary
        $this->info("Import completed: {$totalItems} items and {$totalDetails} total details imported.");

        // Final message when the import is complete
        $this->info('Import completed!');
    }
}
