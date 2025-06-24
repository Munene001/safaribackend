<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $primaryKey = 'country_id';
    protected $fillable = ['name'];

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'country_id');
    }
}
