<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
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
        // TEST 
        // php artisan app:create-inspection-scheduled-tasks
        // php artisan tasks:create-inspection-scheduled

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

        // Fetch all InspectionSchedules for today with the same month and day
        $inspectionSchedules = InspectionSchedule::whereMonth('active_date', $today->month)
            ->whereDay('active_date', $today->day)
            ->get();
        //$this->info('Scheduled tasks created at ' . now());
        foreach ($inspectionSchedules as $inspectionSchedule) {
            $this->info('InspectionSchedule: ' . $inspectionSchedule->id);
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
                $inspection->assigned_to_id = $inspectionSchedule->assigned_to_id;
                $inspection->due_date = $dueDate;
                $inspection->site_id = $inspectionSchedule->site_id;
                $inspection->save();
            } else {
                // If the inspection exists, update the due date
                $inspection->due_date = $dueDate;
                $inspection->save();
            }
            $this->info('inspection: ' . $inspection->id);
        }

        // foreach ($inspectionSchedule as $schedule) {
        //     //Log::info('InspectionSchedule: ' . $schedule->id);
        //     $this->info('InspectionSchedule: ' . $schedule);
        // }
    }
}
