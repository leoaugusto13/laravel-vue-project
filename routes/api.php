<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CourseController;
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


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin Routes
    Route::prefix('admin')->group(function () {
        // User Management
        Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index']);
        Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update']);
        Route::post('/users/{user}/toggle-approval', [App\Http\Controllers\Admin\UserController::class, 'toggleApproval']);

        // Logs
        Route::get('/logs', [App\Http\Controllers\Admin\SystemLogController::class, 'index']);

        // Reports
        Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index']);

        // Courses
        Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index']);
        Route::post('/courses', [App\Http\Controllers\Admin\CourseController::class, 'store']);
        Route::put('/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'update']);
        Route::delete('/courses/{course}', [App\Http\Controllers\Admin\CourseController::class, 'destroy']);

        // Trainings
        Route::apiResource('trainings', App\Http\Controllers\Admin\TrainingController::class);

        // Directorates
        Route::apiResource('directorates', App\Http\Controllers\Admin\DirectorateController::class);

        // Target Audiences
    Route::apiResource('target-audiences', App\Http\Controllers\Admin\TargetAudienceController::class);

    // Training Types
    Route::apiResource('training-types', App\Http\Controllers\Admin\TrainingTypeController::class);
    Route::apiResource('years', App\Http\Controllers\Admin\YearController::class);

    // Strategies
    Route::apiResource('strategies', App\Http\Controllers\Admin\StrategyController::class);

        // Modalities
        Route::apiResource('modalities', App\Http\Controllers\Admin\ModalityController::class);

        // Cities
        Route::apiResource('cities', App\Http\Controllers\API\CityController::class);

        // States
        Route::apiResource('states', App\Http\Controllers\API\StateController::class);

        // Regionals
        Route::apiResource('regionals', App\Http\Controllers\API\RegionalController::class);
    });

    // Public/User Routes
    // Enrollments
    Route::post('/enrollments', [App\Http\Controllers\EnrollmentController::class, 'store']);

    // Courses for Enrollment (using Admin controller for now, but exposed publicly/authenticated users)
    // Alternatively, we can alias /api/courses to the admin one if needed by frontend
    Route::get('/courses', [App\Http\Controllers\Admin\CourseController::class, 'index']); 
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
