# AGENTS.md

## Source of truth
- Laravel app (this repo): `C:\Users\DELL\Herd\chimp_safety`
- mobile app : `d:\projects\WHITEHART\mobile\adventure-safety-app`

## Docs location (keep repo root clean)
- Structured TODOs: `temp/TODO.md` (mark done by changing `[ ]` to `[x]`)
- Extra review TODOs: `temp/todos-2.md`
- Human notes: `temp/notes.txt`
- System business logic / flows: `temp/LOGIC.md`
- Skills/instructions: `temp/SKILLS.md`
- Rolling context snapshot (optional): `temp/SESSION.md` (create if needed)
- Daily session logs:
  - Template: `temp/session-template.md`
  - One file per day: `temp/session-YYYY-MM-DD.md`
- Note: `temp/` is gitignored (local-only notes).

## Session definition
- A "session" means this single Codex chat thread. If a new chat starts (or context feels incomplete), treat it as a new session.

## Session checklist (do this first)
1) Open `AGENTS.md`.
2) Open the latest `temp/session-YYYY-MM-DD.md` for recent context.
3) If yesterday's session file is missing key topics you remember working on, add a short **Backfill** section (do not move/rename old files).
4) If today's session file does not exist, create it from `temp/session-template.md`.
5) Check `temp/TODO.md` (and `temp/todos-2.md` if present) for the current action plan and priority items.
6) Confirm which repo/path you're about to edit (Laravel backend vs this mobile app) before making changes.
7) At the end of the session: update today's `temp/session-YYYY-MM-DD.md` and (if used) `temp/SESSION.md`.

## No-surprises rules
- Do not auto-commit. Only commit when explicitly asked.
- Do not change production/live behavior unless it is gated/flagged and approved.
- Do not delete/rename large folders or run destructive git commands unless explicitly asked.
- Credentials note: `temp/notes.txt` currently contains usernames/passwords for Expo/Apple/etc. Treat it as sensitive; do not copy credentials into other repo files.

## Read-only tooling (approved)
- It is always OK to run read-only inspection commands inside this repo (e.g. `rg`, `rg --files`, `Get-ChildItem`, `Get-Content`, `git status`, `git diff`, `git log`, `git blame`).
- Avoid commands that write files/change state unless explicitly requested.
- Do not add or copy secrets (passwords/API keys) into repo files; keep them in env/Forge.
- PowerShell tip: for Expo Router route-group paths like `app/(main)` use `-LiteralPath` (parentheses break `-Path` parsing).

## Core principles
- Always think like a senior developer when making technical decisions.
- All BA/design choices should reflect senior-level UI/UX and senior-marketer thinking (clarity, conversion, and user trust).
- Default to the best possible UI/UX unless there is a clear constraint.


### Bash / Laravel Tinker Rules

Always output the simplest valid, directly executable command.

Strict rules:
- No unnecessary escaping (never use backslashes unless required)
- Never escape semicolons in PHP code
- Use double quotes for bash, single quotes inside PHP
- Output must be a single-line command

Correct:
php artisan tinker --execute="echo 'ok';"

Incorrect:
php artisan tinker --execute='echo \ok\;'
php artisan tinker --execute="echo ok\;"

## Project summary
- Laravel 10 + Inertia (React) app for managing safety operations: inspections, equipment checks, drills, incidents, post-incident follow-ups, files, and reporting.
- Web UI lives in `resources/js` (Inertia pages/components); server-side logic is in `app/Http/Controllers/Admin`.
- Mobile API uses Sanctum tokens: routes in `routes/api.php`, controllers in `app/Http/Controllers/Api/V1/Admin`.
- Roles/permissions are team-scoped via Spatie Permission (`config/permission.php` has `teams => true`).


## Local setup (preferred)
- Requirements: PHP 8.1+, Composer, Node.js (Vite), and a local DB (SQLite by default in `.env.example`).
- First-time:
  - `composer install`
  - `npm install`
  - `Copy-Item .env.example .env`
  - `php artisan key:generate`
  - `php artisan migrate --seed` (seeders create sample data; don’t run this on production)
  - `php artisan storage:link`
- Run:
  - App: Herd or `php artisan serve`
  - Assets: `npm run dev`
  - Scheduler (needed for scheduled-task generation): `php artisan schedule:work`


## Deployment
- Environment variables live in Forge (don’t commit secrets).
- Follow the existing Forge deploy recipe; typical steps include running migrations (`php artisan migrate --force`) and building assets (`npm ci && npm run build`) when applicable.


## Useful commands
- `php artisan test` (or `vendor/bin/pest`)
- `php artisan route:list`
- `npm run dev` / `npm run build`
- Data assessment/cleanup (items):
  - `php artisan data:assess-items --site=ID`
  - `php artisan data:dedupe-items --site=ID` (dry-run)
- Scheduled-task generators:
  - `php artisan tasks:create-inspection-scheduled`
  - `php artisan tasks:create-drill-scheduled`
  - `php artisan tasks:create-post-incident-scheduled`
- Imports/seeding helpers:
  - `php artisan app:import-users`
  - `php artisan app:item-types`
  - `php artisan import:items`
