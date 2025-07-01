<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItineraryAccommodationRequest;
use App\Http\Requests\StoreItineraryRequest;
use App\Services\ItineraryService;
use Illuminate\Support\Facades\Log;

class ItineraryController extends Controller
{
    protected $itineraryService;

    public function __construct(ItineraryService $itineraryService)
    {
        $this->itineraryService = $itineraryService;
    }

    public function store(StoreItineraryRequest $request)
    {
        try {
            // Pass validated data and uploaded file
            $data = $request->validated();
            $data['primary_image'] = $request->file('primary_image');
            $data['gallery_images'] = $request->file('gallery_images');

            $itinerary = $this->itineraryService->createItinerary($data);

            return response()->json([
                'message' => 'Itinerary created successfully',
                'itinerary' => $itinerary,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Itinerary creation failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Itinerary creation failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function attachAccommodation(StoreItineraryAccommodationRequest $request, $itineraryId, $subItineraryId)
    {
        try {
            $this->itineraryService->attachAccommodation($itineraryId, $subItineraryId, $request->validated());
            return response()->json(['message' => 'Accommodation attached'], 201);
        } catch (\Exception $e) {
            Log::error('Accommodation attachment failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Accommodation attachment failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
