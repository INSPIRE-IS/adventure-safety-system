<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Team;
use App\Models\TypeActivity;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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

        $type_activities1 = TypeActivity::create(['name' => 'Zipline']);
        $type_activities2 = TypeActivity::create(['name' => 'Rope Adventure']);

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
    }
}
