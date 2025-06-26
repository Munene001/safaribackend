<?php

namespace App\Services;

use App\Models\Itinerary;
use App\Models\ItineraryDayPlan;
use App\Models\SubItinerary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItineraryService
{
    public function createItinerary(array $data): Itinerary
    {
        return DB::transaction(function () use ($data) {
            // Handle image upload
            if (!isset($data['image']) || !$data['image']->isValid()) {
                throw new \Exception('Image is required or invalid');
            }

            $path = $data['image']->store('itineraries', 'public');
            $imageUrl = Storage::url($path);

            // Create the itinerary
            $itinerary = Itinerary::create([
                'country_id' => $data['country_id'],
                'title' => $data['title'],
                'overview' => $data['overview'],
                'best_season' => $data['best_season'],
                'main_destination' => $data['main_destination'],
                'destination_description' => $data['destination_description'],
                'destination_location' => $data['destination_location'],
                'image_url' => $imageUrl,
            ]);

            if (!$itinerary) {
                // Cleanup image if itinerary creation fails
                Storage::disk('public')->delete($path);
                throw new \Exception('Failed to create itinerary record');
            }

            // Create sub-itineraries and day plans
            foreach ($data['sub_itineraries'] as $subData) {
                $subItinerary = SubItinerary::create([
                    'itinerary_id' => $itinerary->itinerary_id,
                    'duration_days' => $subData['duration_days'],
                    'duration_nights' => $subData['duration_nights'],
                    'price' => $subData['price'],
                    'special_notes' => $subData['special_notes'] ?? null,
                ]);

                foreach ($subData['day_plans'] as $dayPlanData) {
                    ItineraryDayPlan::create([
                        'sub_itinerary_id' => $subItinerary->sub_itinerary_id,
                        'day_number' => $dayPlanData['day_number'],
                        'location' => $dayPlanData['location'],
                        'description' => $dayPlanData['description'],
                        'activities_summary' => $dayPlanData['activities_summary'] ?? null,
                    ]);
                }
            }

            return $itinerary->load([
                'country',
                'subItineraries.dayPlans',
                'subItineraries.accommodations.images',
                'subItineraries.accommodations.features',
            ]);
        }, 5);
    }

    public function attachAccommodation($itineraryId, $subItineraryId, array $data)
    {
        $subItinerary = SubItinerary::where('itinerary_id', $itineraryId)->findOrFail($subItineraryId);
        $subItinerary->accommodations()->attach($data['accommodation_id'], [
            'night_number' => $data['night_number'],
        ]);
    }

}
