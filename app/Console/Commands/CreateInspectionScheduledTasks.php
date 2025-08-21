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
        $debug = 0;
        // TEST 
        // php artisan app:create-inspection-scheduled-tasks
        // php artisan tasks:create-inspection-scheduled

        if ($debug == 1) Log::info('InspectionSchedule tasks created at ' . now());
        // Get today's date for comparison
        $today = Carbon::today();

        // Define the period mappings (from 'period_id' => 'description')
        $periods = [
            1 => 'None',
            2 => 'Monthly',
            3 => 'Bi-Monthly', // (every 2 months)
            4 => 'Quarterly', // (every 3 months)
            5 => 'Semi-Annually', // (every 6 months)
            6 => 'Annually'
        ];

        // Fetch all InspectionSchedules for today with the same month and day then every month same day moving forward
        $inspectionSchedules = InspectionSchedule::where('active_date', '<=', $today)
            ->whereDay('active_date', $today->day)
            ->get();
        if ($debug == 1) Log::info('Scheduled tasks created at ' . $today->year);
        if ($debug == 1) Log::info('Scheduled tasks created at ' . $today->month);
        if ($debug == 1) Log::info('Scheduled tasks created at ' . $today->day);
        if ($debug == 1) Log::info($inspectionSchedules);

        //$this->info('Scheduled tasks created at ' . now());
        foreach ($inspectionSchedules as $inspectionSchedule) {
            if ($debug == 1) Log::info('InspectionSchedule: ' . $inspectionSchedule->id);
            // Get the period ID and the corresponding period
            $periodId = $inspectionSchedule->period_id;
            $period = $periods[$periodId] ?? 'None';
            $activeDate = Carbon::parse($inspectionSchedule['active_date']);
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
            // $this->info('inspection: ' . $dueDate);
            // die();
            // If the period is 'None', skip the due date calculation
            if ($period == 'None') {
                continue;
            }

            // Check if an inspection already exists for this schedule
            $inspection = Inspection::where('inspection_schedule_id', $inspectionSchedule->id)
                ->where('assigned_to_id', $inspectionSchedule->assigned_to_id)
                ->first();
            //$this->info('inspection: ' . $inspection->id);
            // If no inspection exists, create a new one

            if (!$inspection) {
                $inspection = new Inspection();
                $inspection->inspection_schedule_id = $inspectionSchedule->id;
                $inspection->type_inspection_id = $inspectionSchedule->type_inspection_id;
                $inspection->assigned_to_id = $inspectionSchedule->assigned_to_id;
                $inspection->due_date = $dueDate;
                $inspection->site_id = $inspectionSchedule->site_id;
                $inspection->save();

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
            } else {
                // If the inspection exists, update the due date
                $inspection->due_date = $dueDate;
                $inspection->save();
            }
            if ($debug == 1) Log::info('inspection: ' . $inspection->id);
        }

        // foreach ($inspectionSchedule as $schedule) {
        //     //Log::info('InspectionSchedule: ' . $schedule->id);
        //     $this->info('InspectionSchedule: ' . $schedule);
        // }
    }
}
