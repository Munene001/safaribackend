<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $table = 'Activities';

    protected $primaryKey = 'activity_id';
    protected $fillable = [
        'country_id',
        'activity_name',
        'description',
        'difficulty_level',
        'duration_hours',

    ];
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function images()
    {
        return $this->hasmany(ActivityImage::class, 'activity_id');
    }

}
