<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CategoryDetail;
use App\Models\CategoryItem;
use App\Models\Complaint;
use App\Models\DrillSchedule;
use App\Models\Incident;
use App\Models\InspectionSchedule;
use App\Models\PostIncidentSchedule;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\TypeBool;
use App\Models\TypeDegradation;
use App\Models\TypeDrill;
use App\Models\TypeFile;
use App\Models\TypeIncident;
use App\Models\TypeInspection;
use App\Models\TypeQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // create_permissions
        $permissions = [
            // Admin Panel Permissions
            'admin_panel_show',
            'site_panel_show',
            'header_panel_show',
            'staff_panel_show',
            'equipment_panel_show',
            'safety_panel_show',
            'incident_panel_show',
            'files_panel_show',

            // Permission Management
            'permission_access',
            'permission_show',
            'permission_create',
            'permission_edit',
            'permission_delete',

            // Role Management
            'role_access',
            'role_show',
            'role_create',
            'role_edit',
            'role_delete',

            // User Management
            'user_access',
            'user_show',
            'user_create',
            'user_edit',
            'user_delete',

            // Site Management
            'site_access',
            'site_show',
            'site_create',
            'site_edit',
            'site_delete',

            // Audit Log Management
            'audit_log_access',
            'audit_log_show',
            'audit_log_create',
            'audit_log_edit',
            'audit_log_delete',

            // User Alert Management
            'user_alert_access',
            'user_alert_show',
            'user_alert_create',
            'user_alert_edit',
            'user_alert_delete',

            // Type Bool Permissions
            'type_bool_access',
            'type_bool_show',
            'type_bool_create',
            'type_bool_edit',
            'type_bool_delete',

            // Type Inspection Permissions
            'type_inspection_access',
            'type_inspection_show',
            'type_inspection_create',
            'type_inspection_edit',
            'type_inspection_delete',

            // Question Type Permissions
            'type_question_access',
            'type_question_show',
            'type_question_create',
            'type_question_edit',
            'type_question_delete',

            // File Permissions
            'file_access',
            'file_show',
            'file_create',
            'file_edit',
            'file_delete',

            // Type File Permissions
            'type_file_access',
            'type_file_show',
            'type_file_create',
            'type_file_edit',
            'type_file_delete',

            // Category Item Permissions
            'category_item_access',
            'category_item_show',
            'category_item_create',
            'category_item_edit',
            'category_item_delete',

            // Category Detail Permissions
            'category_detail_access',
            'category_detail_show',
            'category_detail_create',
            'category_detail_edit',
            'category_detail_delete',

            // Safety Inspection Permissions
            // 'safety_inspection_detail_access',
            // 'safety_inspection_detail_show',
            // 'safety_inspection_detail_create',
            // 'safety_inspection_detail_edit',
            // 'safety_inspection_detail_delete',

            // Inspection Permissions
            'inspection_access',
            'inspection_show',
            'inspection_create',
            'inspection_edit',
            'inspection_delete',
            'inspection_edit_reminder',
            'inspection_edit_inspector',
            'inspection_edit_date',
            'inspection_resolve',
            'inspection_esculate',

            // Inspection Schedule Permissions
            'inspection_schedule_access',
            'inspection_schedule_show',
            'inspection_schedule_create',
            'inspection_schedule_edit',
            'inspection_schedule_delete',

            // Equipment Inspection Permissions
            'equipment_inspection_access',
            'equipment_inspection_show',
            'equipment_inspection_create',
            'equipment_inspection_edit',
            'equipment_inspection_delete',
            'equipment_inspection_edit_reminder',
            'equipment_inspection_edit_inspector',
            'equipment_inspection_edit_date',

            // Equipment Inspection Permissions
            // 'equipment_inspection_detail_access',
            // 'equipment_inspection_detail_show',
            // 'equipment_inspection_detail_create',
            // 'equipment_inspection_detail_edit',
            // 'equipment_inspection_detail_delete',

            // Type degradation
            // 'type_degradation_access',
            // 'type_degradation_show',
            // 'type_degradation_create',
            // 'type_degradation_edit',
            // 'type_degradation_delete',

            // Type Drill Permissions
            'type_drill_access',
            'type_drill_show',
            'type_drill_create',
            'type_drill_edit',
            'type_drill_delete',

            // Drill Schedule Permissions
            'drill_schedule_access',
            'drill_schedule_show',
            'drill_schedule_create',
            'drill_schedule_edit',
            'drill_schedule_delete',

            // Drill Permissions
            'drill_access',
            'drill_show',
            'drill_create',
            'drill_edit',
            'drill_delete',
            'drill_edit_reminder',
            'drill_edit_inspector',
            'drill_edit_date',

            // Drill Permissions
            // 'drill_detail_access',
            // 'drill_detail_show',
            // 'drill_detail_create',
            // 'drill_detail_edit',
            // 'drill_detail_delete',

            // Drill Member Permissions
            // 'drill_member_access',
            // 'drill_member_show',
            // 'drill_member_create',
            // 'drill_member_edit',
            // 'drill_member_delete',

            // Type Incident Permisions
            'type_incident_access',
            'type_incident_show',
            'type_incident_create',
            'type_incident_edit',
            'type_incident_delete',

            // Incident Permissions
            'incident_access',
            'incident_show',
            'incident_create',
            'incident_edit',
            'incident_delete',
            'incident_edit_reminder',
            'incident_edit_inspector',
            'incident_edit_date',
            'incident_resolve',
            'incident_esculate',

            // post Indident Schedule Permissions
            'post_incident_schedule_access',
            'post_incident_schedule_show',
            'post_incident_schedule_create',
            'post_incident_schedule_edit',
            'post_incident_schedule_delete',

            // Post Incident Permissions
            'post_incident_access',
            'post_incident_show',
            'post_incident_create',
            'post_incident_edit',
            'post_incident_delete',

            // Post Incident Detail Permissions
            'post_incident_detail_access',
            'post_incident_detail_show',
            'post_incident_detail_create',
            'post_incident_detail_edit',
            'post_incident_detail_delete',

            // Post Incident Detail Permissions
            'complaint_access',
            'complaint_show',
            'complaint_create',
            'complaint_edit',
            'complaint_delete',
            'complaint_edit_reminder',
            'complaint_edit_inspector',
            'complaint_edit_date',

            // System Calendar Permissions
            // 'system_calendar_access',
            // 'system_calendar_show',
            // 'system_calendar_create',
            // 'system_calendar_edit',
            // 'system_calendar_delete',

            // Team Member Permissions
            // 'team_member_access',
            // 'team_member_show',
            // 'team_member_create',
            // 'team_member_edit',
            // 'team_member_delete',
        ];

        // Create roles and assign permissions
        // $superRole = Role::create(['name' => 'super']);
        // $adminRole = Role::create(['name' => 'admin']);
        // $authorRole = Role::create(['name' => 'author']);
        // $managerRole = Role::create(['name' => 'manager']);
        // $directorRole = Role::create(['name' => 'director']);
        // $userRole = Role::create(['name' => 'user']);

        $csvFile = database_path('seeders/permissions.csv');
        $csvData = array_map('str_getcsv', file($csvFile));

        if (empty($csvData)) {
            die("CSV file is empty.");
        }

        $expectedColumns = count($csvData[0]); // Get the number of columns from the first row
        $errors = [];

        foreach ($csvData as $index => $row) {
            $actualColumns = count($row);

            if ($actualColumns < $expectedColumns) {
                $errors[] = "Row " . ($index + 1) . " has $actualColumns columns instead of $expectedColumns: " . implode(", ", $row);
            }
        }

        if (!empty($errors)) {
            echo "❌ Issues found in CSV:\n";
            foreach ($errors as $error) {
                echo $error . "\n";
            }
        } else {
            echo "✅ CSV is properly formatted!";
        }

        $roles = array_slice($csvData[0], 1); // Extract role names (super, admin, etc.)

        // Ensure roles exist in the system
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach (array_slice($csvData, 1) as $row) {
            $permissionName = $row[0]; // First column is permission name
            $values = array_slice($row, 1);

            // Insert permission
            //$permissionId = DB::table('permissions')->insertGetId(['name' => $permissionName,'created_at' => now(),'updated_at' => now(),]);
            $permission = Permission::firstOrCreate(['name' => $permissionName]);

            // Assign permissions to roles
            foreach ($roles as $index => $roleName) {
                if ($values[$index] == 1) {
                    $role = Role::where('name', $roleName)->first();
                    if ($role) {
                        $role->givePermissionTo($permission);
                    }
                }
            }
        }

        TypeInspection::create(['name' => 'Monthly Safety Inspection']); // safety inspection
        TypeInspection::create(['name' => 'Annual Safety Inspection']); // safety inspection        
        TypeInspection::create(['name' => 'PPE Inspections']); // equipment inspection

        TypeDrill::create(['name' => 'Fire Fighting']);
        TypeDrill::create(['name' => 'Evacuation']);
        TypeDrill::create(['name' => 'CPR']);
        TypeDrill::create(['name' => 'Rescue']);
        TypeDrill::create(['name' => 'Belay to Ground']);

        $type_activities1 = TypeActivity::create(['name' => 'Zipline']);
        $type_activities2 = TypeActivity::create(['name' => 'Rope Adventure']);


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

        $user1 = User::create(['name' => 'super user', 'email' => 'jason@whitehart.co.za', 'password' => bcrypt('P@ssw0rd1234'),]);
        $user2 = User::create(['name' => 'admin user', 'email' => 'admin@canopytour.co.za', 'password' => bcrypt('P@ssw0rd1234'),]);
        $user3 = User::create(['name' => 'author', 'email' => 'author@canopytour.co.za', 'password' => bcrypt('P@ssw0rd1234'),]);
        $user4 = User::create(['name' => 'Mark Brown', 'email' => 'mark@canopytour.co.za', 'password' => bcrypt('P@ssw0rd1234'),]);

        $team1 = Team::create(['name' => 'uShaka', 'description' => ' uShaka Marine World’s Chimp & Zee Rope Adventure Park', 'location' => '1 King Shaka Avenue Point, Durban 4001', 'phone' => '123-456-7890', 'email' => 'ushaka@chimpandzee.co.za', 'website' => 'https://ushakamarine.com/chimpnzee/', 'image' => 'https://cdn.bitrix24.com/b14850221/landing/79b/79b5ade99934012d4fa012b663252719/uSh_Chimp_Zee_logos_v1_1x.png']);
        $team2 = Team::create(['name' => 'Sun City', 'description' => 'Chimp & Zee Rope Adventure Park Sun City Resort', 'location' => 'Kuriftu Resort Entoto Ethiopia', 'phone' => '123-456-7890', 'email' => 'chimp@chimppe.com', 'website' => 'https://www.suninternational.com/sun-city/activities/adventure/chimp-and-zee/', 'image' => 'https://www.suninternational.com/content/dam/suninternational/sun-city-resort/sport-recreation/valley-of-waves/chimp-and-zee-logo.jpg.sunimage.300.300.png']);
        // attach() – Adds new records to the pivot table without removing existing ones.
        $team1->type_activities()->attach([$type_activities1->id]); // Adds teams 1, 2, and 3// Adds teams 1, 2, and 3 // Adds team 4 without removing the previous ones
        $team2->type_activities()->attach([$type_activities2->id]); // Adds teams 1, 2, and 3// Adds teams 1, 2, and 3 // Adds team 4 without removing the previous ones
        // sync() – Replaces existing pivot records with the given ones (removes anything not included). 
        // Assign team to the user           
        $user1->teams()->sync([$team1->id, $team2->id]);
        $user2->teams()->sync([$team1->id, $team2->id]);
        $user3->teams()->sync([$team1->id, $team2->id]);
        $user4->teams()->sync([$team1->id, $team2->id]);

        // Assign a role to the user for specific teams
        setPermissionsTeamId($team1->id);
        $user1->assignRole('super'); // Assign 'super' role for Team 1
        $user2->assignRole('admin'); // Assign 'admin' role for Team 1    
        $user3->assignRole('author'); // Assign 'admin' role for Team 1    
        $user4->assignRole('director'); // Assign 'admin' role for Team 1    
        setPermissionsTeamId($team2->id);
        $user1->assignRole('super'); // Assign 'super' role for Team 1
        $user2->assignRole('admin'); // Assign 'admin' role for Team 1    
        $user3->assignRole('author'); // Assign 'admin' role for Team 1    
        $user4->assignRole('director'); // Assign 'admin' role for Team 1         

        // //monthly
        // CategoryItem::create(['name' => 'Launch Platform 1', 'type_inspection_id' => 1, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Element 1', 'type_inspection_id' => 1, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Launch Platform 2', 'type_inspection_id' => 1, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Element 2', 'type_inspection_id' => 1, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // // drill
        // CategoryItem::create(['name' => 'Trolleys', 'type_inspection_id' => 3, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Lanyards', 'type_inspection_id' => 3, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Carabiners', 'type_inspection_id' => 3, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Harnesses', 'type_inspection_id' => 3, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Maillons', 'type_inspection_id' => 3, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // // annual
        // CategoryItem::create(['name' => 'Launch Platform 1', 'type_inspection_id' => 2, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Element 1', 'type_inspection_id' => 2, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Launch Platform 2', 'type_inspection_id' => 2, 'site_id' => 2,  'certificate_recieved_id' => 2]);
        // CategoryItem::create(['name' => 'Element 2', 'type_inspection_id' => 2, 'site_id' => 2,  'certificate_recieved_id' => 2]);

        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Concrete Stay Blocks', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Stay Cables and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Gumpole Posts', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Steel Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Platform Timber Frame', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Platform Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Zipline and Safety Cable and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 1, 'name' => 'Fixings and Fastenings', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 2, 'name' => 'Combi Rope', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 2, 'name' => 'Support & Belay Cables', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 2, 'name' => 'Spreader Bars and Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 2, 'name' => 'Timber and Poles', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 2, 'name' => 'Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Concrete Stay Blocks', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Stay Cables and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Gumpole Posts', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Steel Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Platform Timber Frame', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Platform Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Zipline and Safety Cable and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 3, 'name' => 'Fixings and Fastenings', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 4, 'name' => 'Combi Rope', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 4, 'name' => 'Support & Belay Cables', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 4, 'name' => 'Spreader Bars and Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 4, 'name' => 'Timber and Poles', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 4, 'name' => 'Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 5, 'name' => '#001', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 5, 'name' => '#002', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 6, 'name' => '#001', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 6, 'name' => '#002', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 6, 'name' => '#003', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 7, 'name' => '#001', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 7, 'name' => '#002', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 7, 'name' => '#003', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 7, 'name' => '#004', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 8, 'name' => '#001', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 8, 'name' => '#002', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 8, 'name' => '#003', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 8, 'name' => '#004', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 8, 'name' => '#005', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 9, 'name' => '#001', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 9, 'name' => '#002', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);


        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Concrete Stay Blocks', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Stay Cables and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Gumpole Posts', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Steel Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Platform Timber Frame', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Platform Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Zipline and Safety Cable and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 10, 'name' => 'Fixings and Fastenings', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 11, 'name' => 'Combi Rope', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 11, 'name' => 'Support & Belay Cables', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 11, 'name' => 'Spreader Bars and Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 11, 'name' => 'Timber and Poles', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 11, 'name' => 'Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Concrete Stay Blocks', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Stay Cables and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Gumpole Posts', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Steel Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Platform Timber Frame', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Platform Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Zipline and Safety Cable and Rigging', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 12, 'name' => 'Fixings and Fastenings', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // CategoryDetail::create(['category_item_id' => 13, 'name' => 'Combi Rope', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 13, 'name' => 'Support & Belay Cables', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 13, 'name' => 'Spreader Bars and Brackets', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 13, 'name' => 'Timber and Poles', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);
        // CategoryDetail::create(['category_item_id' => 13, 'name' => 'Decking', 'in_use_date' => '2023-01-01', 'out_use_date' => '']);

        // // period 1 none,2 3month,3 4 month,4 6 month,5 12 month 
        // InspectionSchedule::create(['name' => 'Rock climbing', 'site_id' => $team2->id, 'type_inspection_id' => 1, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // // InspectionSchedule::create(['name' => 'Zipline', 'site_id' => $team2->id, 'type_inspection_id' => 1, 'type_activity_id' => 2, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // InspectionSchedule::create(['name' => 'Rope climbing', 'site_id' => $team2->id, 'type_inspection_id' => 2, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // InspectionSchedule::create(['name' => 'Bunge Jumping', 'site_id' =>  $team2->id, 'type_inspection_id' => 3, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // InspectionSchedule::create(['name' => 'Zipline', 'site_id' =>  $team3->id, 'type_inspection_id' => 1, 'type_activity_id' => 2, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user9->id, 'assigned_by_id' => $user8->id,]);
        // InspectionSchedule::create(['name' => 'Zipline', 'site_id' =>  $team3->id, 'type_inspection_id' => 2, 'type_activity_id' => 2, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user9->id, 'assigned_by_id' => $user8->id,]);
        // InspectionSchedule::create(['name' => 'Big Swing', 'site_id' =>  $team3->id, 'type_inspection_id' => 3, 'type_activity_id' => 2, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user9->id, 'assigned_by_id' => $user8->id,]);
        // InspectionSchedule::create(['name' => 'Suspension Bridge', 'site_id' =>  $team4->id, 'type_inspection_id' => 1, 'type_activity_id' => 3, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user13->id, 'assigned_by_id' => $user12->id,]);
        // InspectionSchedule::create(['name' => 'Skypark', 'site_id' => $team4->id, 'type_inspection_id' => 2, 'type_activity_id' => 3, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user13->id, 'assigned_by_id' => $user12->id,]);


        // DrillSchedule::create(['name' => 'Zipline', 'site_id' => $team2->id, 'type_drill_id' => 1, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // //DrillSchedule::create(['name' => 'Zipline', 'site_id' => $team2->id, 'type_drill_id' => 2, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // //DrillSchedule::create(['name' => 'Zipline', 'site_id' => $team2->id, 'type_drill_id' => 3, 'type_activity_id' => 1, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user5->id, 'assigned_by_id' => $user4->id,]);
        // DrillSchedule::create(['name' => 'Zipline', 'site_id' => $team3->id, 'type_drill_id' => 2, 'type_activity_id' => 2, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user9->id, 'assigned_by_id' => $user8->id,]);
        // DrillSchedule::create(['name' => 'Zipline', 'site_id' => $team4->id, 'type_drill_id' => 3, 'type_activity_id' => 3, 'active_date' => now(), 'period_id' => 5, 'assigned_to_id' => $user13->id, 'assigned_by_id' => $user12->id,]);


        // $incident1 = Incident::create(['type_incident_id' => 1, 'site_id' => 2, 'incident_date' => now(), 'description' => 'client grazed hand on the railing', 'signature_file_id' => null, 'incident_file_id' => null,]);
        // $incident2 = Incident::create(['type_incident_id' => 2, 'site_id' => 2, 'incident_date' => now(), 'description' => 'client slipped on the wet road,scrapped her knee', 'signature_file_id' => null, 'incident_file_id' => null,]);
        // $incident3 = Incident::create(['type_incident_id' => 2, 'site_id' => 3, 'incident_date' => now(), 'description' => 'client tripped on step and fell, grazed knee and hand', 'signature_file_id' => null, 'incident_file_id' => null,]);
        // $incident4 = Incident::create(['type_incident_id' => 3, 'site_id' => 3, 'incident_date' => now(), 'description' => 'client slipped on the wet road, broke leg', 'signature_file_id' => null, 'incident_file_id' => null,]);

        // $incident1->instructors()->sync([$user6->id, $user7->id]);
        // $incident2->instructors()->sync([$user6->id, $user7->id]);
        // $incident3->instructors()->sync([$user10->id, $user11->id]);
        // $incident4->instructors()->sync([$user10->id, $user11->id]);

        // // period 1 none,2 2 days
        // PostIncidentSchedule::create(['name' => 'Incident 1', 'site_id' => 2, 'type_incident_id' => 1, 'incident_id' => 1, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user6->id, 'assigned_by_id' => $user4->id,]);
        // PostIncidentSchedule::create(['name' => 'Incident 2', 'site_id' => 2, 'type_incident_id' => 2, 'incident_id' => 2, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user6->id, 'assigned_by_id' => $user4->id,]);
        // PostIncidentSchedule::create(['name' => 'Incident 3', 'site_id' => 3, 'type_incident_id' => 2, 'incident_id' => 3, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user10->id, 'assigned_by_id' => $user8->id,]);
        // PostIncidentSchedule::create(['name' => 'Incident 4', 'site_id' => 3, 'type_incident_id' => 3, 'incident_id' => 4, 'active_date' => now(), 'period_id' => 2, 'assigned_to_id' => $user10->id, 'assigned_by_id' => $user8->id,]);


        // // complaints
        // Complaint::create(['name' => 'John Doe', 'phone' => '123-456-7890', 'email' => 'john@doe.com', 'complaint_date' => now(), 'title' => 'Food', 'description' => 'The food was cold', 'user_id' => 1, 'site_id' => 2,]);
        // Complaint::create(['name' => 'Jane Doe', 'phone' => '123-456-7890', 'email' => 'jane@doe.com', 'complaint_date' => now(), 'title' => 'Path', 'description' => 'The walk way is slipper after it rained', 'user_id' => 1, 'site_id' => 2,]);
        // Complaint::create(['name' => 'John Smith', 'phone' => '123-456-7890', 'email' => 'john@smith.com', 'complaint_date' => now(), 'title' => 'Broken', 'description' => 'The toilet is broken', 'user_id' => 1, 'site_id' => 2,]);
        // Complaint::create(['name' => 'Jane Smith', 'phone' => '123-456-7890', 'email' => 'jane@smith.com', 'complaint_date' => now(), 'title' => 'Damaged', 'description' => 'The cart is damaged', 'user_id' => 1, 'site_id' => 2,]);
        // Complaint::create(['name' => 'Denver Doe', 'phone' => '123-456-7890', 'email' => 'denver@doe.com', 'complaint_date' => now(), 'title' => 'Wet', 'description' => 'The bedding was wet', 'user_id' => 1, 'site_id' => 3,]);
    }
}
