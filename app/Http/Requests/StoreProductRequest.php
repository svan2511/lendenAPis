<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Helpers\ApiResponse; // ← adjust namespace if ApiResponse is elsewhere
use Illuminate\Support\Facades\Auth;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check(); // or true if you handle auth elsewhere
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:120'],
            'price'     => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'quantity'  => ['nullable', 'integer', 'min:0'],
            'type'      => ['required', Rule::in(['product', 'service'])],
            'unit_type' => ['required', Rule::in(['weight', 'fixed'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Product/service name is required.',
            'name.string'        => 'Name must be a valid string.',
            'name.min'           => 'Name must be at least 2 characters long.',
            'name.max'           => 'Name may not be longer than 120 characters.',

            'price.required'     => 'Price is required.',
            'price.numeric'      => 'Price must be a valid number.',
            'price.min'          => 'Price must be at least 0.01.',
            'price.max'          => 'Price cannot exceed 9,999,999.',

            'quantity.integer'   => 'Quantity must be a whole number.',
            'quantity.min'       => 'Quantity cannot be negative.',

            'type.required'      => 'Type is required.',
            'type.in'            => 'Type must be either "product" or "service".',

            'unit_type.required' => 'Unit type is required.',
            'unit_type.in'       => 'Unit type must be either "weight" or "fixed".',
        ];
    }

    /**
     * Return only the FIRST validation error message
     * in your custom ApiResponse format — same as your original code
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }

    /**
     * Optional: Customize unauthorized response
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            ApiResponse::unauthorized('Please login to continue')
        );
    }
}