<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TypeBool;
use App\Models\TypeDegradation;
use App\Models\TypeDrill;
use App\Models\TypeFile;
use App\Models\TypeIncident;
use App\Models\TypeInspection;
use App\Models\TypeQuestion;

class ImportTypes extends Command
{
    // Define the signature and description for the command
    protected $signature = 'app:item-types';
    protected $description = 'Import items and their details into the database';

    // The constructor for the command
    public function __construct()
    {
        parent::__construct();
    }

    // The handle method will contain the logic to perform when the command is executed
    public function handle()
    {
        TypeInspection::create(['name' => 'Monthly Safety Inspection']); // safety inspection
        TypeInspection::create(['name' => 'Annual Safety Inspection']); // safety inspection        
        TypeInspection::create(['name' => 'PPE Inspections']); // equipment inspection

        TypeDrill::create(['name' => 'Fire Fighting']);
        TypeDrill::create(['name' => 'Evacuation']);
        TypeDrill::create(['name' => 'CPR']);
        TypeDrill::create(['name' => 'Rescue']);
        TypeDrill::create(['name' => 'Belay to Ground']);


        TypeBool::create(['name' => 'Yes']);
        TypeBool::create(['name' => 'No']);
        TypeBool::create(['name' => 'N/A']);

        TypeIncident::create(['name' => 'Near Miss']);
        TypeIncident::create(['name' => 'Minor Injury']);
        TypeIncident::create(['name' => 'Major Injury']);

        TypeDegradation::create(['name' => 'Worn but OK']);
        TypeDegradation::create(['name' => 'Failed and Removed']);

        TypeQuestion::create(['name' => 'Why/How did we allow the accident to happen?']);
        TypeQuestion::create(['name' => 'What lessons can be learnt?']);
        TypeQuestion::create(['name' => 'What new protocols (if any) are to be put in place to help prevent this sort of accident happening again?']);
        TypeQuestion::create(['name' => 'Has a proper debriefing session taken place with ALL the operational staff?']);
        TypeQuestion::create(['name' => 'Was any additional input learnt from this?']);

        TypeFile::create(['name' => 'teams']);
        TypeFile::create(['name' => 'drill_signature']);
        TypeFile::create(['name' => 'incident_signature']);
        TypeFile::create(['name' => 'inspection_signature']);
        TypeFile::create(['name' => 'incidents']);
        TypeFile::create(['name' => 'inspections']);
        TypeFile::create(['name' => 'drills']);
        TypeFile::create(['name' => 'post_incidents']);
        TypeFile::create(['name' => 'safety_inspection_details']);
        TypeFile::create(['name' => 'equipment_inspection_details']);
        TypeFile::create(['name' => 'certificate']);

        // Final message when the import is complete
        $this->info('Import completed!');
    }
}
