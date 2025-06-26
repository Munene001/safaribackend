<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccommodationFeature extends Model
{
    use HasFactory;

    protected $table = 'Accommodation_Features';
    protected $primaryKey = 'feature_id';
    protected $fillable = [
        'accommodation_id',
        'feature_name',
        'feature_value',
    ];
    public $timestamps = false;

    public function accommodation()
    {

        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }
}
