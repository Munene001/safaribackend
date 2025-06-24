<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccommodationFeature extends Model
{
    use HasFactory;
    protected $primaryKey = 'feature_id';
    protected $fillable = [
        'accommodation_id',
        'feature_name',
        'feature_value',
    ];
    public function accommodation()
    {

        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }
}
