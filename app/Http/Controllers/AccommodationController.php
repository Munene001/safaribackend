<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccommodationRequest;
use App\Models\Accommodation;
use App\Services\AccommodationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    //
}
