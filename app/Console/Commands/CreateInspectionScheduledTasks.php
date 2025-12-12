<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use Carbon\Carbon;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\TypeInspection;
use App\Models\User;
use App\Models\UserAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CreateInspectionScheduledTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:create-inspection-scheduled';

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
        $debug = 1;
        // TEST 
        // php artisan app:create-inspection-scheduled-tasks
        // php artisan tasks:create-inspection-scheduled

        if ($debug == 1) Log::info('InspectionSchedule tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            '1' => 'None',
            '2' => 'Monthly',
            '3' => 'Bi-Monthly', // (every 2 months)
            '4' => 'Quarterly', // (every 3 months)
            '5' => '6 Monthly', // (every 6 months)
            '6' => 'Annually',
        ];
        // Fetch all InspectionSchedules for today with the same month and day then every month same day moving forward
        $inspectionSchedules = InspectionSchedule::whereDate('active_date', $today)
            ->whereNull('deleted_at')
            ->get();
        if ($debug == 1) Log::info("Found {$inspectionSchedules->count()} schedule(s) due today ({$today})");

        //$this->info('Scheduled tasks created at ' . now());
        foreach ($inspectionSchedules as $schedule) {
            if ($debug == 1) Log::info('InspectionSchedule: ' . $schedule->id);
            // Get the period ID and the corresponding period
            $period = $periods[$schedule->period_id] ?? 'None';
            $activeDate = Carbon::parse($schedule->active_date);
            $dueDate = $activeDate->copy();

            // Calculate due date based on period
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
            //         Log::info("Schedule {$schedule->id} has no period. Skipping.");
            //         continue 2; // skip to next schedule
            // }
            // $this->info('inspection: ' . $dueDate);
            // die();
            // If the period is 'None', skip the due date calculation
            if ($period == 'None') {
                continue;
            }

            // Check if an inspection already exists for this schedule
            $inspection = Inspection::where('inspection_schedule_id', $schedule->id)
                ->where('due_date', $dueDate->addMonth())
                ->where('assigned_to_id', $schedule->assigned_to_id)
                ->whereNull('deleted_at')
                ->first();
            //$this->info('inspection: ' . $inspection->id);
            // If no inspection exists, create a new one

            if (!$inspection) {
                $inspection = new Inspection();
                $inspection->inspection_schedule_id = $schedule->id;
                $inspection->type_inspection_id = $schedule->type_inspection_id;
                $inspection->assigned_to_id = $schedule->assigned_to_id;
                $inspection->due_date = $dueDate->addMonth();
                $inspection->site_id = $schedule->site_id;
                $inspection->save();

                if ($debug == 1) Log::info("Created new inspection ID {$inspection->id}");

                $typeInspection = TypeInspection::find($inspection->type_inspection_id);
                $name = $typeInspection->name ?? "Inspection";
                // this link is set as menu names in app do not change it will break
                $alert_link = "(/monthly_safety)";
                if ($inspection->type_inspection_id == 2) {
                    $alert_link = "(/anual_safety)";
                }
                if ($inspection->type_inspection_id == 3) {
                    $alert_link = "(/ppe)";
                }
                $subject = "You have been assigned to a " . $name . ".";
                $message = "Please note that you have been assigned to a " . $name . ". <br/> Please complete the inspection before " . $inspection->due_date . ".";
                $notification = "Please note that you have been assigned to a " . $name . ".";

                $users = array_filter([$inspection->assigned_to_id]); // [4, 5, 6];
                if (!empty($users)) {
                    $recipients = User::whereIn('id', $users)->get();

                    foreach ($recipients as $user) {
                        Log::info("Sending message to: {$user->id}");
                        //dd($message);
                        SendMessageJob::dispatch($user->id, $inspection->site_id, $subject, $message);
                    }
                }
                $userAlert = UserAlert::firstOrCreate(
                    ['alert_param_id' => $inspection->id],
                    [
                        'alert_text' => $notification,
                        'alert_link' => $alert_link,
                    ]
                );

                $userAlert->users()->syncWithoutDetaching($users);
            }
            // else {
            //     // If the inspection exists, update the due date
            //     $inspection->due_date = $dueDate;
            //     $inspection->save();
            //     if ($debug == 1) Log::info("Updated existing inspection ID {$inspection->id}");
            // }
            if ($debug == 1) Log::info('inspection: ' . $inspection->id);
            // Update schedule's active_date for next occurrence
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
                case '6 Monthly':
                    $nextActiveDate->addMonths(6);
                    break;
                case 'Annually':
                    $nextActiveDate->addYear();
                    break;
            }
            $schedule->active_date = $nextActiveDate;
            $schedule->save();

            if ($debug == 1) Log::info("Updated schedule ID {$schedule->id} next active_date to {$nextActiveDate}");
        }
        if ($debug == 1) Log::info("---- Finished Inspection Scheduler ----");
    }
}
