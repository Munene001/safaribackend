<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubItinerary extends Model
{
    use HasFactory;
    protected $primaryKey = 'sub_itinerary_id';
    protected $fillable = [
        'itinerary_id',
        'duration_days',
        'duration_nights',
        'price',
        'special_notes',
    ];
    public function itinerary()
    {

        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'sub_itinerary_accommodations', 'sub_itinerary_id', 'accommodation_id')->withPivot('night_number');

    }
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'sub_itinerary_activities', 'sub_itinerary_id', 'activity_id')->withPivot(['day_number', 'time_of_day']);
    }
    public function dayPlans()
    {
        return $this->hasMany(ItineraryDayPlan::class, 'sub_itinerary_id');
    }

}
