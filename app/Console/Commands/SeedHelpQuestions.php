<?php

namespace App\Console\Commands;

use App\Models\HelpAnswer;
use App\Models\HelpCategory;
use App\Models\HelpQuestion;
use App\Models\Team;
use Illuminate\Console\Command;

class SeedHelpQuestions extends Command
{
    protected $signature = 'seed:help-questions {--team= : Team ID to seed team-specific content} {--with-samples : Seed questions/answers}';

    protected $description = 'Seed Help Center categories and (optionally) questions/answers. Idempotent.';

    public function handle(): int
    {
        $teamId = $this->option('team');
        $withSamples = (bool) $this->option('with-samples');

        $team = null;
        if ($teamId !== null) {
            $team = Team::query()->find($teamId);
            if (!$team) {
                $this->error("Team not found for --team={$teamId}");
                return self::FAILURE;
            }
        }

        $categories = [
            ['name' => 'Getting Started', 'order_id' => 1, 'role_slugs' => null],
            ['name' => 'Sites', 'order_id' => 2, 'role_slugs' => null],
            ['name' => 'Inspections', 'order_id' => 10, 'role_slugs' => null],
            ['name' => 'PPE', 'order_id' => 20, 'role_slugs' => null],
            ['name' => 'Rescue Drills', 'order_id' => 30, 'role_slugs' => null],
            ['name' => 'Incidents', 'order_id' => 40, 'role_slugs' => null],
            ['name' => 'Complaints', 'order_id' => 50, 'role_slugs' => null],
            ['name' => 'Documents', 'order_id' => 60, 'role_slugs' => null],
            ['name' => 'Staff', 'order_id' => 70, 'role_slugs' => ['super', 'admin', 'director', 'manager']],
            ['name' => 'Admin', 'order_id' => 100, 'role_slugs' => ['super', 'admin', 'author', 'director']],
        ];

        $this->info('Seeding Help Center categories...');
        $categoryModels = [];
        foreach ($categories as $category) {
            $model = HelpCategory::query()->updateOrCreate(
                ['name' => $category['name']],
                [
                    'order_id' => $category['order_id'],
                    'role_slugs' => $category['role_slugs'],
                ]
            );
            $categoryModels[$model->name] = $model;
        }

        if (!$withSamples) {
            $this->info('Done (categories only). Use --with-samples to seed questions/answers.');
            return self::SUCCESS;
        }

        $targetTeamId = $team ? (int) $team->id : null;
        $targetTeamLabel = $team ? $team->name : 'All Teams';

        $this->info('Seeding Help Center questions...');
        $samples = [
            [
                'category' => 'Getting Started',
                'question' => 'What is the Help Center?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
The Help Center is an in-app Q&A library for Chimp Safety.

## Who can see it
- All authenticated users can view it.
- Some categories can be limited by role.

## Where it shows
- If you have the left sidebar (**Admin Panel**), you will see **Help Center** there.
- Otherwise you will see **Help Center** in the top header navigation.

## Content source
This Help Center is seed-driven (no CRUD UI yet).
Current seed scope: **{$targetTeamLabel}**.
MD
            ],
            [
                'category' => 'Getting Started',
                'question' => 'How does Help Center site scoping work?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What you will see
- Questions for your current site (team)
- Plus generic questions (All Teams)

## Override rule (no duplicates)
If a site-specific question exists with the same:
- category, and
- question text

...then the generic version is hidden.

## Seeding site-specific content
```bash
php artisan seed:help-questions --team=TEAM_ID --with-samples
```
MD
            ],
            [
                'category' => 'Getting Started',
                'question' => 'Why do I not see a menu item?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Most common reasons
- You do not have the required permission for that page.
- Your role does not show that panel (header/sidebar).
- You are in the wrong site (team).

## What to do
- Ask an admin to check your role permissions.
- Confirm you are on the correct site (team).
MD
            ],
            [
                'category' => 'Getting Started',
                'question' => 'What is the Dashboard?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
The Dashboard is your quick snapshot of safety work for the current site.

## What you will see
- Upcoming and completed inspections
- Upcoming and completed drills
- Post-incident follow-ups (if enabled for your role)

## Where
- Menu: **Dashboard**
MD
            ],
            [
                'category' => 'Sites',
                'question' => 'How do I change the current Site (team)?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
If you have a sidebar, you can change sites there:
- **Admin Panel** users: open **Sites** and click the site name.
- **Site Panel** users: click the site name under **Choose Site**.

## What it changes
The selected site controls what you see across the app (inspections, drills, incidents, documents, etc).

## Tip
If you do not see a site switcher, you probably only have access to one site.
MD
            ],
            [
                'category' => 'Inspections',
                'question' => 'What are "Element Items" and how are they used?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
Element Items are the things you inspect (courses, lines, platforms, equipment, etc).

## How it works
- Each Element Item can have multiple sub-items (details / identifications).
- Inspection checklist lines are stored against:
  - the inspection
  - the Element Item
  - the Element detail (when applicable)

## Where
- Admin Panel -> **Inspection** -> **Element Items**

## Access
- Requires permission: `category_item_access`
MD
            ],
            [
                'category' => 'Inspections',
                'question' => 'How do I view and filter inspections?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
- Header menu: **Inspection**

## Search and filters
- Use the search bar to find inspections.
- Use **Period**, **Month**, and **Show** to narrow results.

## Access
- Requires permission: `inspection_access`
MD
            ],
            [
                'category' => 'Inspections',
                'question' => 'How do Inspection Schedules generate inspections?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Overview
Inspection Schedules are the automation source for creating inspections on a repeat cycle.

## Where
- Admin Panel -> **Inspection** -> **Schedules**

## Key fields
- **Schedule Run Date**: the date the scheduler looks for (`active_date`).
- **Repeat Period**: Monthly / Bi-Monthly / Quarterly / 6 Monthly / Annually.
- **Assigned User**: who receives the inspection task.

## Background job
- The server runs `php artisan tasks:create-inspection-scheduled` via the Laravel scheduler.
- When a schedule's run date matches today, the app creates an Inspection and notifies the assigned user.

## Access
- Requires permission: `inspection_schedule_access`

<div style="padding:12px;border:1px solid #E4E4E4;border-radius:12px;background:#FBF9F7;">
Raw HTML is allowed in this Help Center (use responsibly).
</div>
MD
            ],
            [
                'category' => 'PPE',
                'question' => 'What is PPE in this app?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
PPE covers equipment items (harnesses, lanyards, hardware, etc) and their inspection history.

## Where
- Header menu: **PPE**
- Admin Panel -> **PPE** -> **Equipment** (item list)
- Admin Panel -> **PPE** -> **Schedules** (automation)
MD
            ],
            [
                'category' => 'PPE',
                'question' => 'How do I view PPE inspections?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
- Header menu: **PPE**

## Search and filters
- Use **Period**, **Month**, and **Show** to narrow results.

## Access
- Requires permission: `inspection_access`
MD
            ],
            [
                'category' => 'Rescue Drills',
                'question' => 'How do Drill Schedules work?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Overview
Drill Schedules automatically create Drill tasks on a repeat cycle.

## Where
- Admin Panel -> **Rescue Drills** -> **Schedules**

## Background job
- The server runs `php artisan tasks:create-drill-scheduled` via the Laravel scheduler.

## Access
- Requires permission: `drill_schedule_access`
MD
            ],
            [
                'category' => 'Rescue Drills',
                'question' => 'What are Drill Types?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
Drill Types are templates like Fire Fighting, Evacuation, CPR, etc.
They are used when creating Drill Schedules and Drill tasks.

## Where
- Admin Panel -> **Rescue Drills** -> **Type Drills**

## Access
- Requires permission: `type_drill_access`
MD
            ],
            [
                'category' => 'Incidents',
                'question' => 'How do incidents and escalation work?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Incidents
Incidents are recorded per site and can be reviewed in the **Incident** menu.

## Escalation
Escalation sends alerts/messages to higher roles for the site.

## Access
- Requires permission: `incident_access`
MD
            ],
            [
                'category' => 'Incidents',
                'question' => 'What is Post Incident?',
                'is_internal' => true,
                'answer_md' => <<<MD
## What it is
Post Incident is a follow-up workflow after an incident (debrief questions, learnings, and actions).

## Where
- Admin Panel -> **Incident** -> **Post Incident**
- Admin Panel -> **Incident** -> **Post Incident Schedule**

## Access
- Requires permission: `post_incident_access` (currently restricted to super)
MD
            ],
            [
                'category' => 'Complaints',
                'question' => 'Where do I view complaints?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
- Header menu: **Complaint**

## Access
- Typically requires permission: `complaint_access`
MD
            ],
            [
                'category' => 'Documents',
                'question' => 'Where do I find site documents?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
- Admin Panel -> **Site** -> **Documents**
- Header menu: **Document**

## Access
- Requires permission: `file_access`
MD
            ],
            [
                'category' => 'Documents',
                'question' => 'How do I upload a document?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Where
- Admin Panel -> **Site** -> **Documents**

## Steps
1. Click **Add** (or the upload action).
2. Select the file and (if required) the document type.
3. Save.

## Access
- Requires permission: `file_access`
MD
            ],
            [
                'category' => 'Staff',
                'question' => 'Who can manage staff (users)?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Access
- Requires permission: `user_access`

## Notes
If you cannot see **Staff**, ask an admin to grant access or assign the correct role.
MD
            ],
            [
                'category' => 'Staff',
                'question' => 'How do roles and permissions work?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Overview
Access is controlled by roles and permissions.

## Common roles
- super: full access
- admin: admin access for assigned sites
- director: admin-style access (site operations + schedules)
- manager: operational access (inspections, incidents, documents)
- operation: operational access (inspections, drills, incidents)

## Tip
If you do not see a menu item, ask an admin to check your permissions.
MD
            ],
            [
                'category' => 'Admin',
                'question' => 'Which artisan commands seed baseline data?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Common setup commands
```bash
php artisan app:import-users
php artisan app:item-types
php artisan import:items
php artisan seed:help-questions --with-samples
```

## Notes
- `seed:help-questions` is idempotent.
- Some existing import commands may not be idempotent (run with care on production).
MD
            ],
            [
                'category' => 'Admin',
                'question' => 'How do we import initial Element Items (CLI)?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Command
```bash
php artisan import:items
```

## What it does
Creates Element Items and their details used by inspections.

## Important notes
- The current import command uses hardcoded IDs (site/type) and uses `create()` (not idempotent).
- Run only on a fresh DB, or adjust the command before re-running on production.
MD
            ],
            [
                'category' => 'Admin',
                'question' => 'How does scheduling run in production?',
                'is_internal' => true,
                'answer_md' => <<<MD
## Laravel scheduler
On Forge (or any server), you typically run:
```bash
php artisan schedule:run
```
every minute via cron.

## This app
This app schedules task generators (inspections/drills/post-incident) and they run from the scheduler.

## Manual run (debug)
```bash
php artisan tasks:create-inspection-scheduled
php artisan tasks:create-drill-scheduled
php artisan tasks:create-post-incident-scheduled
```
MD
            ],
        ];

        foreach ($samples as $sample) {
            $category = $categoryModels[$sample['category']] ?? null;
            if (!$category) {
                    $this->warn("Skipping question; category not found: {$sample['category']}");
                    continue;
                }

            $question = HelpQuestion::query()->updateOrCreate(
                [
                    'help_category_id' => $category->id,
                    'team_id' => $targetTeamId,
                    'question' => $sample['question'],
                ],
                [
                    'is_internal' => (bool) $sample['is_internal'],
                ]
            );

            HelpAnswer::query()->updateOrCreate(
                ['help_question_id' => $question->id],
                ['answer_md' => $sample['answer_md']]
            );
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
