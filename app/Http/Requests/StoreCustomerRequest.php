<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Helpers\ApiResponse; // ← adjust namespace if ApiResponse is elsewhere

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or add logic e.g. auth()->check()
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:2', 'max:100'],
            'phone' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                Rule::unique('customers', 'phone'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'name.string'   => 'Name must be a valid string.',
            'name.min'      => 'Name must be at least 2 characters.',
            'name.max'      => 'Name may not be greater than 100 characters.',

            'phone.required' => 'Mobile number is required.',
            'phone.string'   => 'Mobile number must be a valid string.',
            'phone.size'     => 'Mobile number must be exactly 10 digits.',
            'phone.regex'    => 'Mobile number must contain exactly 10 digits (0-9 only).',

            // Custom message for unique rule
            'phone.unique'   => 'This phone number is already registered.',
        ];
    }

    /**
     * Return only the FIRST error message in your custom ApiResponse format
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }
}