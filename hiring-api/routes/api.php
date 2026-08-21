<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\RecipeController;
use Illuminate\Support\Facades\Route;

$publicRoutes = function () {
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
    Route::get('/recipes/category/{category}', [RecipeController::class, 'byCategory']);
    Route::get('/recipes/difficulty/{difficulty}', [RecipeController::class, 'byDifficulty']);

    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'service' => 'HiringMachine API',
            'version' => '1.0.0',
        ]);
    });
};

$protectedRoutes = function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
    });

    Route::prefix('recipes')->group(function () {
        Route::post('/', [RecipeController::class, 'store']);
        Route::get('/my-recipes', [RecipeController::class, 'myRecipes']);
        Route::post('/{recipe}/ratings', [RecipeController::class, 'rate']);
        Route::put('/{recipe}', [RecipeController::class, 'update']);
        Route::delete('/{recipe}', [RecipeController::class, 'destroy']);
    });

    Route::prefix('recipes/{recipe}/comments')->group(function () {
        Route::get('/', [CommentController::class, 'index']);
        Route::post('/', [CommentController::class, 'store']);
    });

    Route::prefix('comments')->group(function () {
        Route::put('/{comment}', [CommentController::class, 'update']);
        Route::delete('/{comment}', [CommentController::class, 'destroy']);
    });
};

Route::prefix('v1')->middleware('auth:api')->group($protectedRoutes);
Route::prefix('v1')->group($publicRoutes);
