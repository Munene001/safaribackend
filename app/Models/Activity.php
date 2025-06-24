<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $fillable = [
        'activity_name',
        'description',
        'difficulty_level',
        'duration_hours',
    ];

    public function images()
    {
        return $this->hasmany(ActivityImage::class, 'activity_id');
    }
    public function subItineraries()
    {
        return $this->belongsToMany(SubItinerary::class, 'sub_itinerary_activities', 'activity_id', 'sub_itinerary_id')->withPivot(['day_number', 'time_of_day']);

    }
}
