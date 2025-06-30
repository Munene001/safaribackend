<?php

namespace App\Http\Controllers;

use App\Enums\CountryEnum;
use App\Models\Country;

class CountryController extends Controller
{
    public function getCountries()
    {
        $countries = Country::select('country_id', 'name')->get()->map(function ($country) {
            try {
                $enum = CountryEnum::tryFrom($country->country_id);
                
                return [
                    'id' => $country->country_id,
                    'name' => $enum ? $enum->label() : $country->name,
                ];
            } catch (\Exception $e) {
                return [
                    'id' => $country->country_id,
                    'name' => $country->name,
                ];
            }
        });
        
        return response()->json($countries);
    }

    public function getCountryById(int $countryId)
    {
        $country = Country::where('country_id', $countryId)->firstOrFail();
        
        try {
            $enum = CountryEnum::tryFrom($country->country_id);
            
            return response()->json([
                'id' => $country->country_id,
                'name' => $enum ? $enum->label() : $country->name,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'id' => $country->country_id,
                'name' => $country->name,
            ]);
        }
    }
}