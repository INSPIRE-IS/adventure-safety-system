<?php

use App\Jobs\SendMessageJob;
use App\Models\Drill;
use App\Models\DrillSchedule;
use App\Models\Inspection;
use App\Models\InspectionSchedule;
use App\Models\PostIncident;
use App\Models\PostIncidentSchedule;
use App\Models\Team;
use App\Models\TypeActivity;
use App\Models\TypeDrill;
use App\Models\TypeIncident;
use App\Models\TypeInspection;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
    Carbon::setTestNow(Carbon::parse('2026-05-20 06:00:00'));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('inspection generator creates one inspection and advances active_date', function () {
    $team = Team::create(['name' => 'Site 1']);
    $activity = TypeActivity::create(['name' => 'Activity 1']);
    $typeInspection = TypeInspection::create(['name' => 'Monthly Safety', 'due_days' => 7]);
    $user = User::factory()->create();

    $schedule = InspectionSchedule::create([
        'name' => 'Monthly Safety Schedule',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '2', // Monthly
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_inspection_id' => $typeInspection->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    $this->artisan('tasks:create-inspection-scheduled')->assertExitCode(0);

    $this->assertDatabaseCount('inspections', 1);
    $this->assertDatabaseHas('inspections', [
        'inspection_schedule_id' => $schedule->id,
        'site_id' => $team->id,
        'assigned_to_id' => $user->id,
        'type_inspection_id' => $typeInspection->id,
        'due_date' => '2026-05-27 00:00:00',
    ]);

    $schedule->refresh();
    expect($schedule->active_date->toDateString())->toBe('2026-06-20');

    Queue::assertPushed(SendMessageJob::class, 1);
});

test('inspection generator is idempotent when inspection already exists', function () {
    $team = Team::create(['name' => 'Site 1']);
    $activity = TypeActivity::create(['name' => 'Activity 1']);
    $typeInspection = TypeInspection::create(['name' => 'Monthly Safety', 'due_days' => 14]);
    $user = User::factory()->create();

    $schedule = InspectionSchedule::create([
        'name' => 'Monthly Safety Schedule',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '2', // Monthly
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_inspection_id' => $typeInspection->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    Inspection::create([
        'inspection_schedule_id' => $schedule->id,
        'type_inspection_id' => $typeInspection->id,
        'assigned_to_id' => $user->id,
        'due_date' => '2026-06-03 00:00:00',
        'site_id' => $team->id,
    ]);

    $this->artisan('tasks:create-inspection-scheduled')->assertExitCode(0);

    $this->assertDatabaseCount('inspections', 1);
    $schedule->refresh();
    expect($schedule->active_date->toDateString())->toBe('2026-06-20');
});

test('inspection generator does not create inspections for period None', function () {
    $team = Team::create(['name' => 'Site 1']);
    $activity = TypeActivity::create(['name' => 'Activity 1']);
    $typeInspection = TypeInspection::create(['name' => 'Monthly Safety', 'due_days' => 14]);
    $user = User::factory()->create();

    InspectionSchedule::create([
        'name' => 'One-off (disabled)',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '1', // None
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_inspection_id' => $typeInspection->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    $this->artisan('tasks:create-inspection-scheduled')->assertExitCode(0);
    $this->assertDatabaseCount('inspections', 0);
    Queue::assertNotPushed(SendMessageJob::class);
});

test('drill generator creates one drill and advances active_date', function () {
    $team = Team::create(['name' => 'Site 1']);
    $activity = TypeActivity::create(['name' => 'Activity 1']);
    $typeDrill = TypeDrill::create(['name' => 'Fire Drill', 'due_days' => 10]);
    $user = User::factory()->create();

    $schedule = DrillSchedule::create([
        'name' => 'Quarterly Drill Schedule',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '2', // 3 Months
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_drill_id' => $typeDrill->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    $this->artisan('tasks:create-drill-scheduled')->assertExitCode(0);

    $this->assertDatabaseCount('drills', 1);
    $this->assertDatabaseHas('drills', [
        'drill_schedule_id' => $schedule->id,
        'site_id' => $team->id,
        'assigned_to_id' => $user->id,
        'type_activity_id' => $activity->id,
        'type_drill_id' => $typeDrill->id,
        'due_date' => '2026-05-30',
    ]);

    $schedule->refresh();
    expect($schedule->active_date)->toBe('2026-08-20');

    Queue::assertPushed(SendMessageJob::class, 1);
});

test('drill generator is idempotent when drill already exists', function () {
    $team = Team::create(['name' => 'Site 1']);
    $activity = TypeActivity::create(['name' => 'Activity 1']);
    $typeDrill = TypeDrill::create(['name' => 'Fire Drill', 'due_days' => 14]);
    $user = User::factory()->create();

    $schedule = DrillSchedule::create([
        'name' => 'Quarterly Drill Schedule',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '2', // 3 Months
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_drill_id' => $typeDrill->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    Drill::create([
        'drill_schedule_id' => $schedule->id,
        'assigned_to_id' => $user->id,
        'due_date' => '2026-06-03',
        'site_id' => $team->id,
        'type_activity_id' => $activity->id,
        'type_drill_id' => $typeDrill->id,
    ]);

    $this->artisan('tasks:create-drill-scheduled')->assertExitCode(0);

    $this->assertDatabaseCount('drills', 1);
    $schedule->refresh();
    expect($schedule->active_date)->toBe('2026-08-20');
});

test('post-incident generator creates one post-incident and is idempotent', function () {
    $team = Team::create(['name' => 'Site 1']);
    $typeIncident = TypeIncident::create(['name' => 'Incident Followup', 'due_days' => 5]);
    $user = User::factory()->create();

    $schedule = PostIncidentSchedule::create([
        'name' => 'Post Incident Once',
        'active_date' => Carbon::today()->toDateString(),
        'period_id' => '2', // Once
        'site_id' => $team->id,
        'type_incident_id' => $typeIncident->id,
        'assigned_to_id' => $user->id,
        'assigned_by_id' => $user->id,
    ]);

    $this->artisan('tasks:create-post-incident-scheduled')->assertExitCode(0);
    $this->assertDatabaseCount('post_incidents', 1);
    $this->assertDatabaseHas('post_incidents', [
        'post_incident_schedule_id' => $schedule->id,
        'site_id' => $team->id,
        'assigned_to_id' => $user->id,
        'type_incident_id' => $typeIncident->id,
        'due_date' => '2026-05-25',
    ]);

    // Run again; should not create duplicates.
    $this->artisan('tasks:create-post-incident-scheduled')->assertExitCode(0);
    $this->assertDatabaseCount('post_incidents', 1);
});
