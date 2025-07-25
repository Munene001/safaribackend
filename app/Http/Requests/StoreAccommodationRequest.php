<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccommodationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'country_id' => 'required|integer|exists:Countries,country_id',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'rating' => 'required|numeric|between:0,5|regex:/^\d+(\.\d{1})?$/',
            'website_url' => 'nullable|url|max:500',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:250',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:250',
            'features' => 'nullable|array',
            'features.*.feature_name' => 'required|string',

            //
        ];
    }
}
