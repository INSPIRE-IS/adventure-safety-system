<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use Carbon\Carbon;
use App\Models\Drill;
use App\Models\DrillSchedule;
use App\Models\TypeDrill;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateDrillScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:create-drill-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates scheduled tasks every day at 6 AM';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // TEST 
        // php artisan app:create-drill-scheduled-tasks
        // php artisan tasks:create-drill-scheduled

        $debug = 0;
        Log::info('DrillSchedule tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            1 => 'None',
            2 => 'Monthly',
            3 => 'Bi-Monthly', // (every 2 months)
            4 => 'Quarterly', // (every 3 months)
            5 => '6 Monthly', // (every 6 months)
            6 => 'Annually'
        ];


        // Fetch all DrillSchedules for today with the same month and day

        $drillSchedules = DrillSchedule::whereDate('active_date', $today)
            ->whereNull('deleted_at')
            ->get();

        if ($debug == 1) Log::info("Found {$drillSchedules->count()} drill schedule(s) due today ({$today})");

        //$this->info('Scheduled tasks created at ' . now());
        foreach ($drillSchedules as $schedule) {
            $this->info("Processing DrillSchedule ID: {$schedule->id}");
            // Get the period ID and the corresponding period
            $period = $periods[$schedule->period_id] ?? 'None';
            $activeDate = Carbon::parse($schedule->active_date);
            $dueDate = $activeDate->copy();

            // Calculate the due date based on the period
            //$dueDate = null;
            // switch ($period) {
            //     case 'Monthly':
            //         $dueDate->addMonth();
            //         break;
            //     case 'Bi-Monthly':
            //         $dueDate->addMonths(2);
            //         break;
            //     case 'Quarterly':
            //         $dueDate->addMonths(3);
            //         break;
            //     case '6 Monthly':
            //         $dueDate->addMonths(6);
            //         break;
            //     case 'Annually':
            //         $dueDate->addYear();
            //         break;
            //     case 'None':
            //     default:
            //         Log::info("DrillSchedule {$schedule->id} has no period. Skipping.");
            //         continue 2;
            // }
            // $this->info('drill: ' . $dueDate);
            // die();
            // If the period is 'None', skip the due date calculation
            if ($period == 'None') {
                continue;
            }

            // Check if an drill already exists for this schedule
            $drill = Drill::where('drill_schedule_id', $schedule->id)
                ->where('due_date', $dueDate->addMonth())
                ->where('assigned_to_id', $schedule->assigned_to_id)
                ->first();
            //$this->info('drill: ' . $drill->id);
            // If no drill exists, create a new one
            if (!$drill) {
                $drill = new Drill();
                $drill->assigned_to_id = $schedule->assigned_to_id;
                $drill->due_date = $dueDate->addMonth();
                $drill->site_id = $schedule->site_id;
                $drill->drill_schedule_id = $schedule->id;
                $drill->type_activity_id = $schedule->type_activity_id;
                $drill->type_drill_id = $schedule->type_drill_id;
                $drill->save();
                Log::info("Created new Drill ID {$drill->id}");
                try {

                    $typeDrill = TypeDrill::find($drill->type_drill_id);
                    $name = $typeDrill->name ?? "Safety Drill";

                    $subject = "You have been assigned to a " . $name . ".";
                    $message = "Please note that you have been assigned to a " . $name . ". <br/> Please complete the safety drill before " . $drill->due_date . ".";
                    $notification = "Please note that you have been assigned to a " . $name . ".";

                    $users = array_filter([$drill->assigned_to_id]); // [4, 5, 6];
                    if (!empty($users)) {
                        $recipients = User::whereIn('id', $users)->get();

                        foreach ($recipients as $user) {
                            $this->info("Sending message to: {$user->id}");
                            //dd($message);
                            SendMessageJob::dispatch($user->id, $drill->site_id, $subject, $message);
                        }
                    }
                    // the alert link is set as menu names in app do not change it will break
                    $userAlert = UserAlert::firstOrCreate(
                        ['alert_param_id' => $drill->id],
                        [
                            'alert_text' => $notification,
                            'alert_link' => '/(rescue)',
                        ]
                    );

                    $userAlert->users()->syncWithoutDetaching($users);
                } catch (\Exception $e) {

                    Log::error("Error sending message to {$drill->assigned_to_id}: " . $e->getMessage());
                }
            }
            // else {
            //     // If the drill exists, update the due date
            //     $drill->due_date = $dueDate;
            //     $drill->save();
            //     if ($debug == 1) Log::info("Updated existing Drill ID {$drill->id}");
            // }
            if ($debug == 1) Log::info('type_drill_id: ' . $drill->type_drill_id);
            $nextActiveDate = $activeDate->copy();
            switch ($period) {
                case 'Monthly':
                    $nextActiveDate->addMonth();
                    break;
                case 'Bi-Monthly':
                    $nextActiveDate->addMonths(2);
                    break;
                case 'Quarterly':
                    $nextActiveDate->addMonths(3);
                    break;
                case 'Semi-Annually':
                    $nextActiveDate->addMonths(6);
                    break;
                case 'Annually':
                    $nextActiveDate->addYear();
                    break;
            }
            $schedule->active_date = $nextActiveDate;
            $schedule->save();

            if ($debug == 1) Log::info("Updated DrillSchedule ID {$schedule->id} next active_date to {$nextActiveDate}");
        }
        Log::info("---- Finished Drill Scheduler ----");
    }
}
