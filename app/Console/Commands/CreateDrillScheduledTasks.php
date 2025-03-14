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

        //Log::info('Scheduled tasks created at ' . now());
        $this->info('Scheduled tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            1 => 'None',
            2 => '3 Month',
            3 => '4 Month',
            4 => '6 Month',
            5 => '12 Month'
        ];

        // Fetch all DrillSchedules for today with the same month and day
        $drillSchedules = DrillSchedule::whereMonth('active_date', $today->month)
            ->whereDay('active_date', $today->day)
            ->get();
        //$this->info('Scheduled tasks created at ' . now());
        foreach ($drillSchedules as $drillSchedule) {
            $this->info('DrillSchedule: ' . $drillSchedule->id);
            // Get the period ID and the corresponding period
            $periodId = $drillSchedule->period_id;
            $period = $periods[$periodId] ?? 'None';
            $activeDate = Carbon::parse($drillSchedule['active_date']);
            $dueDate = clone $activeDate;

            // Calculate the due date based on the period
            //$dueDate = null;
            if ($period == '3 Month') {
                $dueDate->addMonths(3);
            } elseif ($period == '4 Month') {
                $dueDate->addMonths(4);
            } elseif ($period == '6 Month') {
                $dueDate->addMonths(6);
            } elseif ($period == '12 Month') {
                $dueDate->addMonths(12);
            }
            // $this->info('drill: ' . $dueDate);
            // die();
            // If the period is 'None', skip the due date calculation
            if ($period == 'None') {
                continue;
            }

            // Check if an drill already exists for this schedule
            $drill = Drill::where('drill_schedule_id', $drillSchedule->id)
                ->where('assigned_to_id', $drillSchedule->assigned_to_id)
                ->first();
            //$this->info('drill: ' . $drill->id);
            // If no drill exists, create a new one
            if (!$drill) {
                $drill = new Drill();
                $drill->assigned_to_id = $drillSchedule->assigned_to_id;
                $drill->due_date = $dueDate;
                $drill->site_id = $drillSchedule->site_id;
                $drill->drill_schedule_id = $drillSchedule->id;
                $drill->type_activity_id = $drillSchedule->type_activity_id;
                $drill->type_drill_id = $drillSchedule->type_drill_id;
                $drill->save();
            } else {
                // If the drill exists, update the due date
                $drill->due_date = $dueDate;
                $drill->save();
            }

            $typeInspection = TypeDrill::find($drill->type_drill_id);
            $name = $typeInspection->name ?? "Safety Drill";

            $subject = "You have been assigned to a " . $name . ".";
            $message = "Please note that you have been assigned to a " . $name . ". <br/> Please complete the safety drill before " . $drill->due_date . ".";
            $notification = "Please note that you have been assigned to a " . $name . ".";

            $users = array_filter([$drill->assigned_to_id]); // [4, 5, 6];
            if (!empty($users)) {
                $recipients = User::whereIn('id', $users)->get();

                foreach ($recipients as $user) {
                    Log::info("Sending message to: {$user->id}");
                    //dd($message);
                    SendMessageJob::dispatch($user->id, $drill->site_id, $subject, $message);
                }
            }
            $userAlert = UserAlert::create([
                'alert_text' => $notification,
                'alert_link' => '/(rescue)',
                'alert_param_id' => $drill->id,
            ]);
            $userAlert->users()->sync($users);

            $this->info('drill: ' . $drill->id);
        }

        // foreach ($drillSchedule as $schedule) {
        //     //Log::info('DrillSchedule: ' . $schedule->id);
        //     $this->info('DrillSchedule: ' . $schedule);
        // }
    }
}
