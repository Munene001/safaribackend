<?php

namespace App\Http\Requests;

use App\Enums\CountryEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItineraryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'country_id' => [
                'required',
                'integer',
                Rule::in(CountryEnum::getValues()), // Use getValues() instead of values()
            ],
            'overview' => 'required|string',
            'best_season' => 'required|string',
            'main_destination' => 'required|string',
            'destination_description' => 'required|string',
            'destination_location' => 'required|string',
            'images.*.caption' => 'nullable|string',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:250',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:250',
            'sub_itineraries' => 'required|array',
            'sub_itineraries.*.duration_days' => 'required|integer',
            'sub_itineraries.*.duration_nights' => 'required|integer',
            'sub_itineraries.*.price' => 'required|numeric',
            'sub_itineraries.*.special_notes' => 'nullable|string',
            'sub_itineraries.*.day_plans' => 'required|array',
            'sub_itineraries.*.day_plans.*.day_number' => 'required|integer',
            'sub_itineraries.*.day_plans.*.location' => 'required|string',
            'sub_itineraries.*.day_plans.*.description' => 'required|string',
            'sub_itineraries.*.day_plans.*.activities_summary' => 'nullable|string',
            //
        ];
    }
}
