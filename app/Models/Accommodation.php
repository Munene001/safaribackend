<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    use HasFactory;
    protected $table = 'Accommodations';
    protected $primaryKey = 'accommodation_id';
    protected $fillable = [
        'country_id',
        'name',
        'description',
        'location',
        'type',
        'rating',
        'website_url',

    ];
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function features()
    {
        return $this->hasMany(AccommodationFeature::class, 'accommodation_id');
    }
    public function images()
    {
        return $this->hasMany(AccommodationImage::class, 'accommodation_id');
    }
    public function subItineraries()
    {
        return $this->belongsToMany(SubItinerary::class, 'sub_itinerary_accommodations', 'accommodation_id', 'sub_itinerary_id')->withPivot('night_number');
    }
}
