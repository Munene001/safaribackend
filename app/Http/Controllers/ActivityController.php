<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Http\Request;
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
    public function index(Request $request)
    {
        $count = Activity::count();
        $activities = Activity::with(['images', 'country'])->get();
        return response()->json(['activities' => $activities, 'count' => $count]);

    }
    public function show($activity_id)
    {
        $activity = Activity::with('images', 'country')->findOrFail($activity_id);
        return response()->json($activity);
    }
    public function destroy($activity_id)
    {
        $activity = Activity::findOrFail($activity_id);
        $activity->delete();
        return response()->json(['message' => 'Activity deleted successfully'], 200);
    }
}
