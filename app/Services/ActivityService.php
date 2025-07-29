<?php
namespace App\Services;

use App\Models\Activity;
use App\Models\ActivityImage;
use App\Models\Country;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ActivityService
{
    public function createActivity(array $data)
    {
        $activity = Activity::create([
            'activity_name' => $data['activity_name'],
            'description' => $data['description'],
            'country_id' => $data['country_id'] === '' ? null : $data['country_id'],
            'difficulty_level' => $data['difficulty_level'],
            'duration_hours' => $data['duration_hours'],

        ]);

        if (!Country::where('country_id', $data['country_id'])->exists()) {
            throw new \Exception('Invalid country_id provided');
        }

        if (!$activity) {
            throw new \Exception('Failed to create activity record');

        }
        if (!isset($data['primary_image']) || !$data['primary_image']->isValid()) {
            throw new \Exception('Primary image is required or invalid');
        }
        $this->storeImage($data['primary_image'], $activity->activity_id, true);
        if (isset($data['gallery_images'])) {
            foreach ($data['gallery_images'] as $image) {
                if ($image->isValid()) {
                    $this->storeImage($image, $activity->activity_id, false);
                } else {
                    Log::error('Invalid gallery image skipped');
                }
            }
        }
        return $activity->load('country', 'images');

    }
    private function storeImage($imageFile, $activityId, $isPrimary)
    {
        try {
            if (!$imageFile->isValid()) {
                throw new \Exception('Invalid file uploaded');
            }
            $path = $imageFile->store('activities', 'public');
            $image = ActivityImage::create([
                'activity_id' => $activityId,
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

}
