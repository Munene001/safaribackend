<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $table = 'Countries';
    protected $primaryKey = 'country_id';
    protected $fillable = ['name'];

    protected $casts = [
        'country_id' => 'integer',
    ];

    public $timestamps = false;

    public function accommodations()
    {
        return $this->hasMany(Accommodation::class, 'country_id');
    }
    public function activities()
    {
        return $this->hasMany(Activity::class, 'country_id');
    }
    public function itineraries()
    {
        return $this->hasMany(Itinerary::class, 'country_id');
    }
}
