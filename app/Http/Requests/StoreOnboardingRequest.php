<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Helpers\ApiResponse; // ← adjust namespace if ApiResponse is in a different location

class StoreOnboardingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // This endpoint requires an authenticated user
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'business_type'     => ['required', Rule::in(['product', 'service', 'both', 'freelance'])],
            'has_stock'         => ['nullable', 'boolean'],
            'has_appointments'  => ['nullable', 'boolean'],
            'has_staff'         => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages(): array
    {
        return [
            'business_type.required' => 'Business type is required.',
            'business_type.in'       => 'Business type must be one of: product, service, both, freelance.',

            'has_stock.boolean'      => 'Stock management flag must be true or false.',
            'has_appointments.boolean' => 'Appointment management flag must be true or false.',
            'has_staff.boolean'      => 'Staff management flag must be true or false.',
        ];
    }

    /**
     * Return only the first validation error message
     * using your custom ApiResponse helper — matches original behavior
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }

    /**
     * Optional: Customize the failed authorization response
     * (when user is not authenticated)
     */
    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            ApiResponse::unauthorized('Please login to continue')
        );
    }
}