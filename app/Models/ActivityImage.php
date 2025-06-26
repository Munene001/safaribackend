<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityImage extends Model
{
    use HasFactory;

    protected $table = "Activity_Images";
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'activity_id',
        'image_url',
        'caption',
        'is_primary',
    ];

    public $timestamps = false;
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
