<?php

use App\Http\Controllers\ProfileController;
//use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\UserAlertController;
use App\Http\Controllers\Admin\TypeBoolController;
use App\Http\Controllers\Admin\TypeInspectionController;
use App\Http\Controllers\Admin\FileController;
use App\Http\Controllers\Admin\TypeFileController;
use App\Http\Controllers\Admin\CategoryItemController;
use App\Http\Controllers\Admin\CategoryDetailController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\SafetyInspectionDetailController;
use App\Http\Controllers\Admin\InspectionController;
use App\Http\Controllers\Admin\InspectionScheduleController;
use App\Http\Controllers\Admin\InspectionUserAssignedController;
use App\Http\Controllers\Admin\EquipmentInspectionDetailController;
use App\Http\Controllers\Admin\TypeDrillController;
use App\Http\Controllers\Admin\DrillScheduleController;
use App\Http\Controllers\Admin\RescueDrillController;
use App\Http\Controllers\Admin\DrillMemberController;
use App\Http\Controllers\Admin\TypeIncidentController;
use App\Http\Controllers\Admin\IncidentController;
//use App\Http\Controllers\Admin\IncidentInstructorController;
use App\Http\Controllers\Admin\TypeQuestionController;
use App\Http\Controllers\Admin\PostIncidentController;
use App\Http\Controllers\Admin\PostIncidentScheduleController;
use App\Http\Controllers\Admin\DrillController;
use App\Http\Controllers\Admin\DrillDetailController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\PostIncidentDetailController;
use App\Http\Controllers\Admin\SystemCalendarController;
use App\Http\Controllers\Admin\TypeDegradationController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {

//     Route::resource('users', UserController::class);

//     Route::resource('sites', SiteController::class);



// });
//Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
Route::middleware('auth')->group(function () {


    // Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::get('dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::resource('permissions', PermissionController::class);

    // Roles
    Route::resource('roles', RoleController::class);

    // Users
    Route::resource('users', UserController::class);

    // Team
    //Route::post('teams/parse-csv-import', 'SiteController@parseCsvImport')->name('teams.parseCsvImport');
    // Route::post('teams/process-csv-import', 'SiteController@processCsvImport')->name('teams.processCsvImport');
    //Route::get('sites/change/{site_id}', [SiteController::class, 'change'])->name('sites.change');


    Route::get('/sites/{site_id}/change', [SiteController::class, 'change'])->name('sites.change');
    Route::resource('sites', SiteController::class)->except(['create', 'edit']);

    // Audit Logs
    Route::resource('audit-logs', AuditLogController::class)->except(['create', 'store', 'edit', 'update', 'destroy']);

    // User Alerts
    Route::get('user-alerts/read', [UserAlertController::class, 'read'])->name('user-alerts.read');
    Route::resource('user-alerts', UserAlertController::class)->except(['create', 'edit']);

    // Type Bool
    Route::resource('type-bools', TypeBoolController::class)->except(['create', 'edit']);

    // Type Inspection
    Route::resource('type-inspections', TypeInspectionController::class)->except(['create', 'edit']);

    // Category Items
    Route::resource('category-items', CategoryItemController::class)->except(['create', 'edit']);

    // Category Detail
    Route::resource('category-details', CategoryDetailController::class)->except(['create', 'edit']);

    // Inspections
    Route::post('inspections/manage', [InspectionController::class, 'manage'])->name('inspections.manage');
    Route::post('inspections/resolve', [InspectionController::class, 'resolveIssue'])->name('inspections.resolve');
    Route::post('inspections/escalate', [InspectionController::class, 'esculateIssue'])->name('inspections.escalate');
    Route::resource('inspections', InspectionController::class)->except(['create', 'edit']);
    Route::resource('equipments', EquipmentController::class)->except(['create', 'edit']);

    // Inspection Schedule
    Route::resource('inspection-schedules', InspectionScheduleController::class)->except(['create', 'edit']);

    // Inspection User Assigned
    Route::resource('inspection-user-assigneds', InspectionUserAssignedController::class)->except(['create', 'edit']);

    // Equipment Inspection Detail
    Route::resource('equipment-inspection-details', EquipmentInspectionDetailController::class)->except(['create', 'edit']);

    // Safety Inspection Detail
    Route::resource('safety-inspection-details', SafetyInspectionDetailController::class)->except(['create', 'edit']);
    // TypeDegradation
    Route::resource('type-degradations', TypeDegradationController::class)->except(['create', 'edit']);

    // Complaints
    Route::resource('complaints', ComplaintController::class)->except(['create', 'edit']);
    // Type Drills
    Route::resource('type-drills', TypeDrillController::class)->except(['create', 'edit']);

    // Drill Schedule
    Route::resource('drill-schedules', DrillScheduleController::class)->except(['create', 'edit']);

    // Drills
    Route::post('drills/manage', [DrillController::class, 'manage'])->name('drills.manage');
    Route::resource('drills', DrillController::class)->except(['create', 'edit']);

    // Drills
    Route::resource('drill-details', DrillDetailController::class)->except(['create', 'edit']);

    // Drill Members
    Route::resource('drill-members', DrillMemberController::class)->except(['create', 'edit']);

    // Type Incident
    Route::resource('type-incidents', TypeIncidentController::class)->except(['create', 'edit']);

    // Incident
    Route::post('incidents/resolve', [IncidentController::class, 'resolveIssue'])->name('incidents.resolve');
    Route::post('incidents/escalate', [IncidentController::class, 'esculateIssue'])->name('incidents.escalate');
    Route::resource('incidents', IncidentController::class)->except(['create', 'edit']);

    // Incident Instructor
    //Route::resource('incident-instructors', IncidentInstructorController::class);

    // Type Question
    Route::resource('type-questions', TypeQuestionController::class)->except(['create', 'edit']);

    // Post Incident Schedule
    Route::resource('post-incident-schedules', PostIncidentScheduleController::class)->except(['create', 'edit']);

    // Post Incident
    Route::resource('post-incidents', PostIncidentController::class)->except(['create', 'edit']);

    // Post Incident Detail
    Route::resource('post-incident-details', PostIncidentDetailController::class)->except(['create', 'edit']);

    // Files
    Route::resource('files', FileController::class)->except(['create', 'edit']);

    // Type Files
    Route::resource('type-files', TypeFileController::class)->except(['create', 'edit']);

    Route::get('system-calendar', [SystemCalendarController::class, 'index'])->name('calendar.index');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Route::get('system-calendar', 'SystemCalendarController@index')->name('calendar.index');
    //Route::get('team-members', 'TeamMembersController@index')->name('team-members.index');
    //Route::post('team-members', 'TeamMembersController@invite')->name('team-members.invite');
});

require __DIR__ . '/auth.php';
