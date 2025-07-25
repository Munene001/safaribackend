<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryFAQ extends Model
{
    use HasFactory;
    protected $table = 'CountryFAQs';
    protected $primaryKey = 'faq_id';

    protected $fillable = [
        'country_id',
        'question',
        'answer',
    ];

    protected $casts = [
        'faq_id' => 'integer',
        'country_id' => 'integer',
    ];

    public $timestamps = false;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
