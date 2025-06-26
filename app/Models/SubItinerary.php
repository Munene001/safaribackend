<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubItinerary extends Model
{
    use HasFactory;

    protected $table = 'Sub_Itineraries';
    protected $primaryKey = 'sub_itinerary_id';
    protected $fillable = [
        'itinerary_id',
        'duration_days',
        'duration_nights',
        'price',
        'special_notes',
    ];

    public $timestamps = false;
    public function itinerary()
    {

        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }
    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'Sub_Itinerary_Accommodations', 'sub_itinerary_id', 'accommodation_id')->withPivot('night_number');

    }

    public function dayPlans()
    {
        return $this->hasMany(ItineraryDayPlan::class, 'sub_itinerary_id');
    }

}
