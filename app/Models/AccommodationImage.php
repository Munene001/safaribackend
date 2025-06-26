<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccommodationImage extends Model
{
    use HasFactory;
    protected $table = 'Accommodation_Images';
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'accommodation_id',
        'image_url',
        'caption',
        'is_primary',
    ];
    public $timestamps = false;
    public function accommodation()
    {

        return $this->belongsTo(Accommodation::class, 'accommodation_id');
    }
}
