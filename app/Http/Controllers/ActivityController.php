<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Log;

class ActivityController extends Controller
{
    protected $activityService;
    public function __construct(ActivityService $activityService)
    {

        $this->activityService = $activityService;
    }
    public function store(StoreActivityRequest $request)
    {
        try {
            // Pass validated data and uploaded files
            $data = $request->validated();
            $data['primary_image'] = $request->file('primary_image');
            $data['gallery_images'] = $request->file('gallery_images');

            $activity = $this->activityService->createActivity($data);

            return response()->json([
                'message' => 'Activity created successfully',
                'activity' => $activity,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Activity creation failed: ' . $e->getMessage());

            return response()->json([
                'error' => 'Activity creation failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
