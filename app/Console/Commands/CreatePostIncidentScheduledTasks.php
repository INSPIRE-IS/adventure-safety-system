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
        // TEST 
        // php artisan app:create-postincident-scheduled-tasks
        // php artisan tasks:create-postincident-scheduled

        //Log::info('Scheduled tasks created at ' . now());
        $this->info('Scheduled tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            1 => 'None',
            2 => 'Once',
        ];

        // Fetch all PostIncidentSchedules for today with the same month and day
        $postIncidentSchedules = PostIncidentSchedule::whereMonth('active_date', $today->month)
            ->whereDay('active_date', $today->day)
            ->get();
        //$this->info('Scheduled tasks created at ' . now());
        foreach ($postIncidentSchedules as $postincidentSchedule) {
            $this->info('PostIncidentSchedule: ' . $postincidentSchedule->id);
            // Get the period ID and the corresponding period
            $periodId = $postincidentSchedule->period_id;
            $period = $periods[$periodId] ?? 'None';
            $activeDate = Carbon::parse($postincidentSchedule['active_date']);
            $dueDate = clone $activeDate;

            // Calculate the due date based on the period
            //$dueDate = null;
            if ($period == 'Once') {
                $dueDate->addDays(1);
            }
            // $this->info('postincident: ' . $dueDate);
            // die();
            // If the period is 'None', skip the due date calculation
            if ($period == 'None') {
                continue;
            }

            // Check if an postincident already exists for this schedule
            $postincident = PostIncident::where('post_incident_schedule_id', $postincidentSchedule->id)
                ->where('assigned_to_id', $postincidentSchedule->assigned_to_id)
                ->first();
            //$this->info('postincident: ' . $postincident->id);
            // If no postincident exists, create a new one
            if (!$postincident) {
                $postincident = new PostIncident();
                $postincident->type_incident_id = $postincidentSchedule->type_incident_id;
                $postincident->post_incident_schedule_id = $postincidentSchedule->id;
                $postincident->assigned_to_id = $postincidentSchedule->assigned_to_id;
                $postincident->due_date = $dueDate;
                $postincident->site_id = $postincidentSchedule->site_id;
                $postincident->save();
            } else {
                // If the postincident exists, update the due date
                $postincident->due_date = $dueDate;
                $postincident->save();
            }

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

            $this->info('postincident: ' . $postincident->id);
        }

        // foreach ($postincidentSchedule as $schedule) {
        //     //Log::info('PostIncidentSchedule: ' . $schedule->id);
        //     $this->info('PostIncidentSchedule: ' . $schedule);
        // }
    }
}
