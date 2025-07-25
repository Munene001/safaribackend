<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\CountryController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/accommodations', [AccommodationController::class, 'store']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::post('/itineraries', [ItineraryController::class, 'store']);
Route::post('/itineraries/{itinerary}/sub-itineraries/{subItinerary}/accommodations',
    [ItineraryController::class, 'attachAccommodation']);
Route::prefix('countries')->group(function () {
    // Get all countries (basic info)
    Route::get('/', [CountryController::class, 'getCountries']);

    // Get single country with full details
    Route::get('/{countryId}', [CountryController::class, 'getCountryById'])
        ->where('countryId', '[0-9]+');

    // Create country data (with FAQs, Parks, Image)
    Route::post('/', [CountryController::class, 'createCountry']);
    Route::delete('/{id}', [CountryController::class, 'deleteCountry']);
});
Route::get('/accommodations', [AccommodationController::class, 'index']);
Route::get('/accommodation/{accommodation_id}', [AccommodationController::class, 'show']);
Route::delete('/accommodation/{accommodation_id}', [AccommodationController::class, 'destroy']);
Route::put('/accommodations/{accommodation_id}', [AccommodationController::class, 'update']);
Route::get('/activities', [ActivityController::class, 'index']);
Route::get('/activity/{activity_id}', [ActivityController::class, 'show']);
Route::delete('/activity/{activity_id}', [ActivityController::class, 'destroy']);
Route::get('/itineraries', [ItineraryController::class, 'index']);
Route::get('/itinerary/{itinerary_id}', [ItineraryController::class, 'show']);
Route::delete('/itinerary/{itinerary_id}', [ItineraryController::class, 'destroy']);
