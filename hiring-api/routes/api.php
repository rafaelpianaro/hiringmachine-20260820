<?php

use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\RecipeController;
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

$publicRoutes = function () {
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Public job listings
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/{job}', [JobController::class, 'show']);

    // Public recipe listings
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
    Route::get('/recipes/category/{category}', [RecipeController::class, 'byCategory']);
    Route::get('/recipes/difficulty/{difficulty}', [RecipeController::class, 'byDifficulty']);

    // Health check
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => 'HiringMachine API',
            'version' => '1.0.0',
        ]);
    });
};

// Public routes - Authentication
Route::prefix('hm')->group($publicRoutes);
Route::prefix('v1')->group($publicRoutes);

$protectedRoutes = function () {
    // User profile routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });

    // Job management routes (for recruiters)
    Route::prefix('jobs')->group(function () {
        Route::post('/', [JobController::class, 'store']);
        Route::get('/my-jobs', [JobController::class, 'myJobs']);
        Route::put('/{job}', [JobController::class, 'update']);
        Route::delete('/{job}', [JobController::class, 'destroy']);
    });

    // Application routes
    Route::prefix('applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index']);
        Route::post('/', [ApplicationController::class, 'store']);
        Route::get('/{application}', [ApplicationController::class, 'show']);
        Route::put('/{application}/status', [ApplicationController::class, 'updateStatus']);
        Route::delete('/{application}', [ApplicationController::class, 'destroy']);
    });

    // Job applications (for recruiters to see applications for their jobs)
    Route::get('/jobs/{job}/applications', [ApplicationController::class, 'jobApplications']);

    // Recipe management routes
    Route::prefix('recipes')->group(function () {
        Route::post('/', [RecipeController::class, 'store']);
        Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);
        Route::put('/{recipe}', [RecipeController::class, 'update']);
        Route::delete('/{recipe}', [RecipeController::class, 'destroy']);
    });

    // Comment routes
    Route::prefix('recipes/{recipe}/comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
    });

    Route::prefix('comments')->group(function () {
        Route::put('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });
};

// Protected routes - Require JWT Authentication
Route::prefix('hm')->middleware('auth:api')->group($protectedRoutes);
Route::prefix('v1')->middleware('auth:api')->group($protectedRoutes);
