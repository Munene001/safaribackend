<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryImage extends Model
{
    use HasFactory;
    protected $table = "Itinerary_Images";
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'itinerary_id',
        'image_url',
        'caption',
        'is_primary',
    ];

    public $timestamps = false;
    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class, 'itinerary_id');
    }

}
