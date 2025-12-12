<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use Carbon\Carbon;
use App\Models\PostIncident;
use App\Models\PostIncidentSchedule;
use App\Models\TypeIncident;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreatePostIncidentScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:create-post-incident-scheduled';

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
        $debug = 0;
        // TEST 
        // php artisan app:create-postincident-scheduled-tasks
        // php artisan tasks:create-postincident-scheduled

        if ($debug == 1) Log::info('PostIncidentSchedule tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            1 => 'None',
            2 => 'Once',
        ];

        // Fetch all PostIncidentSchedules for today with the same month and day

        $postIncidentSchedules = PostIncidentSchedule::whereDate('active_date', $today)
            ->whereNull('deleted_at')
            ->get();
        if ($debug == 1) Log::info("Found {$postIncidentSchedules->count()} PostIncidentSchedule(s) due today ({$today})");

        foreach ($postIncidentSchedules as $schedule) {
            if ($debug == 1) Log::info("Processing PostIncidentSchedule ID: {$schedule->id}");
            // Get the period ID and the corresponding period
            $period = $periods[$schedule->period_id] ?? 'None';
            $activeDate = Carbon::parse($schedule->active_date);
            $dueDate = $activeDate->copy();

            // Calculate the due date based on the period
            //$dueDate = null;
            if ($period === 'Once') {
                $dueDate->addDay();
            } elseif ($period === 'None') {
                Log::info("PostIncidentSchedule {$schedule->id} has no period. Skipping.");
                continue;
            }

            // Check if an postincident already exists for this schedule
            $postIncident = PostIncident::where('post_incident_schedule_id', $schedule->id)
                ->where('assigned_to_id', $schedule->assigned_to_id)
                ->where('due_date', $dueDate)
                ->first();
            //$this->info('postincident: ' . $postincident->id);
            // If no postincident exists, create a new one
            if (!$postincident) {
                $postincident = new PostIncident();
                $postIncident->type_incident_id = $schedule->type_incident_id;
                $postIncident->post_incident_schedule_id = $schedule->id;
                $postIncident->assigned_to_id = $schedule->assigned_to_id;
                $postIncident->due_date = $dueDate;
                $postIncident->site_id = $schedule->site_id;
                $postIncident->save();

                $typeIncident = TypeIncident::find($postincident->type_incident_id);
                $name = $typeIncident->name ?? "Inspection";

                $subject = "You have been assigned to a " . $name . ".";
                $message = "Please note that you have been assigned to a " . $name . ". <br/> Please complete the post incident before " . $postincident->due_date . ".";
                $notification = "Please note that you have been assigned to a " . $name . ".";

                $users = array_filter([$postincident->assigned_to_id]); // [4, 5, 6];
                if (!empty($users)) {
                    $recipients = User::whereIn('id', $users)->get();

                    foreach ($recipients as $user) {
                        Log::info("Sending message to: {$user->id}");
                        //dd($message);
                        SendMessageJob::dispatch($user->id, $postincident->site_id, $subject, $message);
                    }
                }
                // the alert link is set as menu names in app do not change it will break
                $userAlert = UserAlert::create([
                    'alert_text' => $notification,
                    'alert_link' => '/(rescue)',
                    'alert_param_id' => $postincident->id,
                ]);
                $userAlert->users()->sync($users);

                $userAlert = UserAlert::firstOrCreate(
                    ['alert_param_id' => $postincident->id],
                    [
                        'alert_text' => $notification,
                        'alert_link' => '/(post_incident)',
                    ]
                );

                $userAlert->users()->syncWithoutDetaching($users);
            }
            // else {
            //     // If the postincident exists, update the due date
            //     $postincident->due_date = $dueDate;
            //     $postincident->save();
            // }
            if ($debug == 1) Log::info('postincident: ' . $postincident->id);


            // we dont need to do anything once it was sent as i is a once off

            // For 'Once', move active_date forward by 1 day for testing (or mark as completed in real app)            
            // if ($period === 'Once') {
            //     $schedule->active_date = $activeDate->addDay();
            //     $schedule->save();
            //     Log::info("Updated PostIncidentSchedule ID {$schedule->id} next active_date to {$schedule->active_date}");
            // }            
        }

        Log::info("---- Finished PostIncident Scheduler ----");
    }
}
