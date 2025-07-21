<?php

namespace App\Services;

use App\Models\Itinerary;
use App\Models\ItineraryDayPlan;
use App\Models\ItineraryImage;
use App\Models\SubItinerary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ItineraryService
{
    public function createItinerary(array $data): Itinerary
    {
        return DB::transaction(function () use ($data) {

            $itinerary = Itinerary::create([
                'country_id' => $data['country_id'],
                'title' => $data['title'],
                'overview' => $data['overview'],
                'best_season' => $data['best_season'],
                'main_destination' => $data['main_destination'],
                'destination_description' => $data['destination_description'],
                'destination_location' => $data['destination_location'],

            ]);

            if (!$itinerary) {
                throw new \Exception('Failed to create itinerary record');
            }
            if (!isset($data['primary_image']) || !$data['primary_image']->isValid()) {
                throw new \Exception('Primary image is required or invalid');

            }
            $this->storeImage($data['primary_image'], $itinerary->itinerary_id, true);
            if (isset($data['gallery_images'])) {
                foreach ($data['gallery_images'] as $image) {
                    if ($image->isValid()) {
                        $this->storeImage($image, $itinerary->itinerary_id, false);
                    } else {
                        Log::error('Invalid gallery image skipped');
                    }
                }
            }

            // Create sub-itineraries and day plans
            {
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
            }

            return $itinerary->load([
                'country',
                'subItineraries' => function ($query) {
                    $query->with('dayPlans', 'accommodations.images', 'accommodations.features');
                },

                'images',
            ]);
        }, 5);
    }
    private function storeImage($imageFile, $itineraryId, $isPrimary)
    {
        try {
            if (!$imageFile->isValid()) {
                throw new \Exception('Invalid file uploaded');
            }
            $path = $imageFile->store('itineraries', 'public');
            $image = ItineraryImage::create([
                'itinerary_id' => $itineraryId,
                'image_url' => Storage::url($path),
                'caption' => null,
                'is_primary' => $isPrimary,
            ]);
            if (!$image) {
                Storage::disk('public')->delete($path);
                throw new \Exception('Failed to create image record');
            }
            return $path;

        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function attachAccommodation($itineraryId, $subItineraryId, array $data)
    {
        $subItinerary = SubItinerary::where('itinerary_id', $itineraryId)->findOrFail($subItineraryId);
        $subItinerary->accommodations()->attach($data['accommodation_id'], [
            'night_number' => $data['night_number'],
        ]);
    }

}
