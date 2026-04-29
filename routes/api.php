<?php

use App\Http\Controllers\Api\V1\Admin\AuthController;
use App\Http\Controllers\Api\V1\Admin\ComplaintApiController;
use App\Http\Controllers\Api\V1\Admin\DrillDetailApiController;
use App\Http\Controllers\Api\V1\Admin\DrillMembersApiController;
use App\Http\Controllers\Api\V1\Admin\DrillsApiController;
use App\Http\Controllers\Api\V1\Admin\EquipmentInspectionDetailApiController;
use App\Http\Controllers\Api\V1\Admin\FilesApiController;
use App\Http\Controllers\Api\V1\Admin\HomeApiController;
use App\Http\Controllers\Api\V1\Admin\IncidentApiController;
use App\Http\Controllers\Api\V1\Admin\InspectionsApiController;
use App\Http\Controllers\Api\V1\Admin\PostIncidentApiController;
use App\Http\Controllers\Api\V1\Admin\PostIncidentDetailApiController;
use App\Http\Controllers\Api\V1\Admin\SafetyInspectionDetailApiController;
use App\Http\Controllers\Api\V1\Admin\TeamApiController;
use App\Http\Controllers\Api\V1\Admin\TypeBoolApiController;
use App\Http\Controllers\Api\V1\Admin\TypeDrillsApiController;
use App\Http\Controllers\Api\V1\Admin\TypeFilesApiController;
use App\Http\Controllers\Api\V1\Admin\TypeIncidentApiController;
use App\Http\Controllers\Api\V1\Admin\TypeInspectionApiController;
use App\Http\Controllers\Api\V1\Admin\TypeQuestionApiController;
use App\Http\Controllers\Api\V1\Admin\UserAlertApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/v1/test', [AuthController::class, 'test'])->name('test');
// Route::get('/v1/test', function () {
//     return response()->json(['message' => 'Test route working!']);
// });



Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    // Authentication Routes
    Route::post('login', [AuthController::class, 'login'])->name('login');
    //Route::get('team-index', [TeamApiController::class, 'index'])->name('team.index');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});

// Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {

//     //Route::apiResource('teams', 'TeamApiController'); //->except(['create', 'edit'])
//     //Route::apiResource('teams', TeamApiController::class)->except(['create', 'edit']);
//     //Route::get('team-index', [TeamApiController::class, 'index'])->name('team.index');
// });

// , 'namespace' => 'Api\V1\Admin'
Route::group(['prefix' => 'v1', 'as' => 'api.', 'middleware' => ['auth:sanctum']], function () {
    // Team
    // Route::post('teams/media', 'TeamApiController@storeMedia')->name('teams.storeMedia');
    Route::apiResource('teams', TeamApiController::class)->except(['create', 'edit']);

    // Type Bool
    Route::apiResource('type-bools', TypeBoolApiController::class)->except(['create', 'edit']);

    // Type Inspection
    Route::apiResource('type-inspections', TypeInspectionApiController::class)->except(['create', 'edit']);

    // Home getData
    Route::post('getTaskData', [HomeApiController::class, 'getTaskData'])->name('getTaskData');
    Route::post('getAppData', [HomeApiController::class, 'getAppData'])->name('getAppData');

    // Inspections
    Route::apiResource('inspections', InspectionsApiController::class)->except(['create', 'edit']);
    Route::post('inspections/{inspection}/verify-upload', [InspectionsApiController::class, 'verifyUpload'])->name('inspections.verifyUpload');

    // Safety Inspection Detail
    Route::apiResource('safety-inspection-details', SafetyInspectionDetailApiController::class)->except(['create', 'edit']);

    // Equipment Inspection Detail
    Route::apiResource('equipment-inspection-details', EquipmentInspectionDetailApiController::class)->except(['create', 'edit']);

    // Type Drills
    Route::apiResource('type-drills', TypeDrillsApiController::class)->except(['create', 'edit']);

    // Drills
    Route::apiResource('drills', DrillsApiController::class)->except(['create', 'edit']);
    // Drill Detail
    Route::apiResource('drill-details', DrillDetailApiController::class)->except(['create', 'edit']);

    // Drill Members
    Route::apiResource('drill-members', DrillMembersApiController::class)->except(['create', 'edit']);

    // Type Incident
    Route::apiResource('type-incidents', TypeIncidentApiController::class)->except(['create', 'edit']);

    // Incident
    Route::apiResource('incidents', IncidentApiController::class)->except(['create', 'edit']);

    // Complaint
    Route::apiResource('complaints', ComplaintApiController::class)->except(['create', 'edit']);
    // UserAlert
    Route::apiResource('user-alerts', UserAlertApiController::class)->except(['create', 'edit']);
    // AppError
    Route::apiResource('app-errors', UserAlertApiController::class)->except(['create', 'edit']);

    // Incident Instructor // pivot table for Incident
    //Route::apiResource('incident-instructors', IncidentInstructorApiController::class)->except(['create', 'edit']); 

    // Type Question
    Route::apiResource('type-questions', TypeQuestionApiController::class)->except(['create', 'edit']);

    // Post Incident
    Route::apiResource('post-incidents', PostIncidentApiController::class)->except(['create', 'edit']);

    Route::apiResource('post-incident-details', PostIncidentDetailApiController::class)->except(['create', 'edit']);

    // Type Files
    Route::apiResource('type-files', TypeFilesApiController::class)->except(['create', 'edit']);

    // Files
    Route::apiResource('files', FilesApiController::class)->except(['create', 'edit']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
