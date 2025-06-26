<?php
namespace App\Services;

use App\Models\Accommodation;
use App\Models\AccommodationFeature;
use App\Models\AccommodationImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccommodationService
{
    public function createAccommodation(array $data)
    {
        $accommodation = Accommodation::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'country_id' => $data['country_id'],
            'location' => $data['location'],
            'type' => $data['type'],
            'rating' => $data['rating'],
            'website_url' => $data['website_url'],

        ]);
        if (!$accommodation) {
            throw new \Exception('Failed to create accomodation record');
        }
        if (!isset($data['primary_image']) || !$data['primary_image']->isValid()) {
            throw new \Exception('Primary image is required or invalid');

        }
        $this->storeImage($data['primary_image'], $accommodation->accommodation_id, true);

        if (isset($data['gallery_images'])) {
            foreach ($data['gallery_images'] as $image) {
                if ($image->isValid()) {
                    $this->storeImage($image, $accommodation->accommodation_id, false);
                } else {
                    Log::error('Invalid gallery image skipped');
                }
            }
        }
        if (isset($data['features'])) {
            foreach ($data['features'] as $featureData) {
                AccommodationFeature::create([
                    'accommodation_id' => $accommodation->accommodation_id,
                    'feature_name' => $featureData['feature_name'],
                    'feature_value' => $featureData['feature_value'],
                ]);
            }
        }

        return $accommodation->load(['country', 'images', 'features']);
    }
    private function storeImage($imageFile, $accommodationId, $isPrimary)
    {
        try {
            if (!$imageFile->isValid()) {

                throw new \Exception('Invalid file uploaded');
            }
            $path = $imageFile->store('accommodations', 'public');
            $image = AccommodationImage::create([
                'accommodation_id' => $accommodationId,
                'image_url' => Storage::url($path),
                'caption' => null,
                'is_primary' => $isPrimary,

            ]);
            if (!$image) {
                // Change cleanup to use Storage
                Storage::disk('public')->delete($path);
                throw new \Exception('Failed to create image record');
            }

            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed:' . $e->getmessage());
            throw $e;
            //throw $th;
        }
    }

}
