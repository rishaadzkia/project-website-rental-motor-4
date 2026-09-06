<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MotorcycleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $motorcycleId = $this->route('motorcycle')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'brand' => [
                'required',
                'string',
                'max:100',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
            ],

            'plate_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('motorcycles', 'plate_number')
                    ->ignore($motorcycleId),
            ],

            'year' => [
                'required',
                'integer',
                'digits:4',
                'min:2000',
                'max:' . date('Y'),
            ],

            'price_per_day' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'rented',
                    'maintenance',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }
}