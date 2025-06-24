<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;
    protected $primaryKey = 'itinerary_id';
    protected $fillable = [
        'country_id',
        'title',
        'overview',
        'best_season',
        'main_destination',
        'destination_description',
        'destination_location',
        'image_url',

    ];
    public function country()
    {

        return $this->belongsTo(Country::class, 'country_id');
    }
    public function subItineraries()
    {
        return $this->hasMany(SubItinerary::class, 'itinerary_id');
    }

}
