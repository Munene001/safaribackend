<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\CountryFAQ;
use App\Models\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    public function getCountries()
    {
        $countries = Country::select('country_id', 'name', 'image_url')->get();
        return response()->json($countries);
    }

    public function getCountryById(int $countryId)
    {
        $country = Country::with(['faqs', 'parks'])->findOrFail($countryId);
        return response()->json($country);
    }

    public function createCountry(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|unique:Countries,name',
            'overview' => 'required|string|min:20',
            'about' => 'required|string|min:20',
            'best_time_to_visit' => 'required|string|min:10',
            'image' => 'required|image|mimes:webp,jpeg,png,jpg,gif|max:250',
            'faqs' => 'sometimes|array',
            'faqs.*.question' => 'required_with:faqs|string|min:10',
            'faqs.*.answer' => 'required_with:faqs|string|min:10',
            'parks' => 'sometimes|array',
            'parks.*.name' => 'required_with:parks|string|min:3',
            'parks.*.description' => 'required_with:parks|string|min:20',
            'parks.*.location' => 'required_with:parks|string|min:5',
            'parks.*.best_time_to_visit' => 'required_with:parks|string|min:5',
            'parks.*.highlights' => 'required_with:parks|string|min:10',
            'parks.*.image' => 'required_with:parks|image|mimes:webp,jpeg,png,jpg,gif|max:250',
        ]);

        try {
            // Store country image
            $imagePath = $validated['image']->store('countries', 'public');
            $imageUrl = Storage::url($imagePath);

            // Create country
            $country = Country::create([
                'name' => $validated['name'],
                'overview' => $validated['overview'],
                'about' => $validated['about'],
                'best_time_to_visit' => $validated['best_time_to_visit'],
                'image_url' => $imageUrl,
            ]);

            // Create FAQs if provided
            if (isset($validated['faqs'])) {
                foreach ($validated['faqs'] as $faq) {
                    CountryFAQ::create([
                        'country_id' => $country->country_id,
                        'question' => $faq['question'],
                        'answer' => $faq['answer'],
                    ]);
                }
            }

            // Create Parks if provided
            if (isset($validated['parks'])) {
                foreach ($validated['parks'] as $parkData) {
                    $parkImagePath = $parkData['image']->store('parks', 'public');
                    $parkImageUrl = Storage::url($parkImagePath);

                    Park::create([
                        'country_id' => $country->country_id,
                        'name' => $parkData['name'],
                        'description' => $parkData['description'],
                        'location' => $parkData['location'],
                        'best_time_to_visit' => $parkData['best_time_to_visit'],
                        'highlights' => $parkData['highlights'],
                        'image_url' => $parkImageUrl,
                    ]);
                }
            }

            return response()->json([
                'message' => 'Country created successfully',
                'data' => $country->load(['faqs', 'parks']),
            ], 201);

        } catch (\Exception $e) {
            // Cleanup uploaded files if error occurs
            isset($imagePath) && Storage::disk('public')->delete($imagePath);
            isset($parkImagePath) && Storage::disk('public')->delete($parkImagePath);

            Log::error('Country creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create country',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCountry($id)
    {
        try {
            $country = Country::findOrFail($id);
            $country->delete();

            return response()->json([
                'message' => 'Country deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete country',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
