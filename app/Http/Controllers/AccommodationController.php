<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccommodationRequest;
use App\Models\Accommodation;
use App\Models\AccommodationFeature;
use App\Models\AccommodationImage;
use App\Services\AccommodationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AccommodationController extends Controller
{
    protected $accommodationService;
    public function __construct(AccommodationService $accommodationService)
    {
        $this->accommodationService = $accommodationService;
    }

    public function store(StoreAccommodationRequest $request)
    {

        try {
            $data = $request->validated();
            $data['primary_image'] = $request->file('primary_image');
            $data['gallery_images'] = $request->file('gallery_images');

            $accommodation = $this->accommodationService->createAccommodation($data);
            return response()->json([
                'message' => 'Accommodation created successfully',
                'accommodation' => $accommodation,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors(),

            ], 422);

        } catch (\Exception $e) {
            Log::error('Accommodation creation failed:' . $e->getMessage());
            return response()->json([
                'error' => 'Accommodation creation failed',
                'message' => $e->getMessage(),
            ], 500);

        }

    }
    public function index(Request $request)
    {
        try {
            // Check if this is a minimal request (e.g., for dropdowns/day plans)
            $minimal = $request->has('minimal') && $request->input('minimal') == 'true';

            if ($minimal) {
                // Minimal data for dropdowns/day plans
                $accommodations = Accommodation::query()
                    ->select('accommodation_id', 'name', 'location')
                    ->orderBy('name')
                    ->get()
                    ->map(function ($acc) {
                        return [
                            'accommodation_id' => $acc->accommodation_id,
                            'name' => $acc->name,
                            'location' => $acc->location,
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $accommodations,
                ]);
            } else {
                // Full data with relationships (for admin dashboard)
                $count = Accommodation::count();
                $accommodations = Accommodation::with(['images', 'features', 'country'])->get();

                return response()->json([
                    'success' => true,
                    'accommodations' => $accommodations,
                    'count' => $count,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch accommodations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function show($accommodation_id)
    {
        $accommodation = Accommodation::with('images', 'features', 'country')->findOrFail($accommodation_id);
        return response()->json($accommodation);
    }
    public function destroy($accommodation_id)
    {
        $accommodation = Accommodation::findOrFail($accommodation_id);
        $accommodation->delete();
        return response()->json(['message' => 'Accommodation deleted successfully'], 200);
    }

    public function update(Request $request, $accommodation_id)
    {
        Log::debug('Incoming request data', ['data' => $request->all()]);
        Log::debug('Files', ['files' => $request->file() ?: []]);
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'website_url' => 'nullable|url|max:255',
                'rating' => 'nullable|numeric|min:0|max:5',
                'country_id' => 'required|exists:countries,id',
                'features_text' => 'nullable|string',
                'images_to_delete' => 'nullable|array',
                'images_to_delete.*' => 'integer',
                'new_primary_image_id' => 'nullable|integer',
                'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $accommodation = Accommodation::findOrFail($accommodation_id);

            $accommodation->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'type' => $validated['type'],
                'website_url' => $validated['website_url'] ?? null,
                'rating' => $validated['rating'] ?? null,
                'country_id' => $validated['country_id'],
            ]);

            $this->updateFeatures($accommodation, $validated['features_text'] ?? '');
            $this->updateImages(
                $accommodation,
                $request->file('primary_image'),
                $request->file('gallery_images'),
                $validated['images_to_delete'] ?? [],
                $validated['new_primary_image_id'] ?? null
            );

            return response()->json([
                'message' => 'Accommodation updated successfully',
                'accommodation' => $accommodation->load('images', 'features'),
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Accommodation update failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Update failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    protected function updateFeatures(Accommodation $accommodation, string $featuresText)
    {
        $accommodation->features()->delete();
        $features = array_filter(
            array_map('trim', preg_split('/[\n]+/', $featuresText)),
            fn($item) => !empty($item)
        );

        foreach ($features as $feature) {
            AccommodationFeature::create([
                'accommodation_id' => $accommodation->id,
                'feature_name' => $feature,

            ]);
        }
    }

    protected function updateImages(
        Accommodation $accommodation,
        $newPrimaryImage,
        $newGalleryImages,
        array $imageIdsToDelete,
        $newPrimaryImageId = null
    ) {
        if (!empty($imageIdsToDelete)) {
            $images = AccommodationImage::where('accommodation_id', $accommodation->id)
                ->whereIn('image_id', $imageIdsToDelete)
                ->get();
            foreach ($images as $image) {
                Storage::delete($image->image_url);
                $image->delete();
            }
        }
        if ($newPrimaryImage) {
            $oldPrimary = $accommodation->images()->where('is_primary', true)->first();
            if ($oldPrimary) {
                Storage::delete($oldPrimary->image_url);
                $oldPrimary->delete();
            }
            $path = $newPrimaryImage->store('public/accommodations');
            AccommodationImage::create([
                'accommodation_id' => $accommodation->id,
                'image_url' => str_replace('public/', '/storage/', $path),
                'is_primary' => true,
            ]);
        } elseif ($newPrimaryImageId) {
            $accommodation->images()->update(['is_primary' => false]);
            $accommodation->images()
                ->where('image_id', $newPrimaryImageId)
                ->update(['is_primary' => true]);
        }
        if ($newGalleryImages) {
            foreach ($newGalleryImages as $image) {
                $path = $image->store('public/accommodations');
                AccommodationImage::create([
                    'accommodation_id' => $accommodation->id,
                    'image_url' => str_replace('public/', '/storage/', $path),
                    'is_primary' => false,
                ]);
            }
        }
    }
    //
}
