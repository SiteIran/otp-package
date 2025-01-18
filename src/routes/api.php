<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ScoreController;

// -----------------------
// Public Routes
// -----------------------
Route::prefix('auth')->group(function () {
    Route::post('/mobile', [AuthController::class, 'authMobile']); // Authenticate via mobile
    Route::post('/mobile/verify', [AuthController::class, 'verifyMobile']); // Verify mobile authentication
    Route::post('/email', [AuthController::class, 'authEmail']); // Authenticate via email
    Route::post('/email/verify', [AuthController::class, 'verifyEmail']); // Verify email authentication
});

// Fetch user information
Route::get('/user-info', [AuthController::class, 'getUserInfo']);

// -----------------------
// Protected Routes (auth:sanctum)
// -----------------------
Route::middleware('auth:sanctum')->group(function () {
    // Settings routes
    Route::get('/settings', [SettingController::class, 'index']); // Get all settings

    // Logout user and revoke all tokens
    Route::post('/logout', [AuthController::class, 'logout']);

    // Fetch authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

// -----------------------
// Score Management Routes
// -----------------------
Route::prefix('scores')->group(function () {
    Route::get('/', [ScoreController::class, 'index']); // List all scores
    Route::post('/', [ScoreController::class, 'store']); // Create a new score
    Route::get('/{id}', [ScoreController::class, 'show']); // Get score details by ID
    Route::put('/{id}', [ScoreController::class, 'update']); // Update score by ID
    Route::delete('/{id}', [ScoreController::class, 'destroy']); // Delete score by ID
});



