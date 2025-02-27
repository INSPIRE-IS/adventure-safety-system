<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\PostIncident;
use App\Models\PostIncidentSchedule;
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
            $this->info('postincident: ' . $postincident->id);
        }

        // foreach ($postincidentSchedule as $schedule) {
        //     //Log::info('PostIncidentSchedule: ' . $schedule->id);
        //     $this->info('PostIncidentSchedule: ' . $schedule);
        // }
    }
}
