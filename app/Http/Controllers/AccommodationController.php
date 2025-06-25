<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccommodationRequest;
use App\Services\AccommodationService;
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

    //
}
