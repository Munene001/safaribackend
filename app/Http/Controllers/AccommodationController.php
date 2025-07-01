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
    public function index(Request $request)
    {
        try {
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
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch accommodations',
                'error' => $e->getMessage(),

            ], 500);
        }

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

    //
}
