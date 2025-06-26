<?php

namespace App\Http\Requests;

use App\Enums\CountryEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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
            'activity_name' => 'required|string|max:255',
            'country_id' => [
                'required',
                'integer',
                Rule::in(CountryEnum::getValues()), // Use getValues() instead of values()
            ],
            'description' => 'required|string',
            'difficulty_level' => 'required|string',
            'duration_hours' => 'required|numeric',
            'images.*.caption' => 'nullable|string',
            'primary_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:250',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:250',

            //
        ];
    }
}
