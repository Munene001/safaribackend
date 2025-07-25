<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    use HasFactory;
    protected $table = 'Parks';
    protected $primaryKey = 'park_id';

    protected $fillable = [
        'name',
        'description',
        'location',
        'best_time_to_visit',
        'image_url',
        'highlights',
        'country_id',
    ];

    protected $casts = [
        'park_id' => 'integer',
        'country_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

}
