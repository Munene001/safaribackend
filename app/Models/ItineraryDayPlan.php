<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryDayPlan extends Model
{
    use HasFactory;
    protected $primaryKey = 'day_plan_id';
    protected $fillable = [
        'sub_itinerary_id',
        'day_number',
        'location',
        'description',
        'activities_summary',
    ];
    public function subItinerary()
    {
        return $this->belongsTo(SubItinerary::class, 'sub_itinerary_id');
    }
}
