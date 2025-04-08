<?php

namespace App\Console\Commands;

use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use Illuminate\Console\Command;
use App\Models\Item; // Your Item model
use App\Models\ItemDetail; // Your ItemDetail model

class ImportItems extends Command
{
    // Define the signature and description for the command
    protected $signature = 'import:items';
    protected $description = 'Import items and their details into the database';

    // The constructor for the command
    public function __construct()
    {
        parent::__construct();
    }

    // The handle method will contain the logic to perform when the command is executed
    public function handle()
    {
        // Define the array with item names and related details
        $itemsData = [
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

        // Loop through each item and insert into the database
        foreach ($itemsData as $itemData) {
            // Insert the item name
            //$item = CategoryItem::updateOrCreate(['name' => $itemData['item_name'],'type_inspection_id' => 1,'site_id' => 2],['certificate_recieved_id' => 2]);
            $item = CategoryItem::create(['name' => $itemData['item_name'], 'type_inspection_id' => 1, 'site_id' => 2, 'certificate_recieved_id' => 2]);

            // Loop through each of the item's details and insert them into the 'item_details' table
            foreach ($itemData['details'] as $detail) {
                //CategoryDetail::updateOrCreate(['category_item_id' => $item->id,'name' => $detail,],['in_use_date' => now(),'out_use_date' => '']);
                CategoryDetail::updateOrCreate(['category_item_id' => $item->id, 'name' => $detail, 'in_use_date' => now(), 'out_use_date' => '']);
            }

            // Output a success message for this item
            $this->info("Item '{$itemData['item_name']}' and its details have been imported.");
        }

        // Final message when the import is complete
        $this->info('Import completed!');
    }
}
