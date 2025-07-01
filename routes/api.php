<?php

use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ItineraryController;
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
Route::get('/countries', [CountryController::class, 'getCountries']);
Route::get('/countries/{countryId}', [CountryController::class, 'getCountryById']);
Route::get('/accommodations', [AccommodationController::class, 'index']);
