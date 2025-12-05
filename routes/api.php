<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Tinder App API Routes
Route::prefix('v1')->group(function () {
    
    // Get all people (for testing)
    Route::get('/people', [PersonController::class, 'index']);
    
    // Get single person
    Route::get('/people/{id}', [PersonController::class, 'show']);
    
    // Get recommended people for a person (with pagination)
    Route::get('/people/{personId}/recommended', [PersonController::class, 'getRecommended']);
    
    // Like a person
    Route::post('/interactions/like', [PersonController::class, 'likePerson']);
    
    // Dislike a person
    Route::post('/interactions/dislike', [PersonController::class, 'dislikePerson']);
    
    // Get list of people who liked the current person
    Route::get('/people/{personId}/liked-by', [PersonController::class, 'getLikedByList']);
    
    // Get list of people who disliked the current person
    Route::get('/people/{personId}/disliked-by', [PersonController::class, 'getDislikedByList']);
    
    // Get list of people that the current person has disliked
    Route::get('/people/{personId}/disliked', [PersonController::class, 'getDislikedList']);
});
