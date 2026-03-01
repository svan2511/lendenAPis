<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse; // Adjust namespace if ApiResponse is in a different location

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Usually true for login/initiate-otp endpoints
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|digits:10',
        ];
    }

    /**
     * Custom error messages (English, clear & user-friendly)
     */
    public function messages(): array
    {
        return [
            'phone.required'    => 'Mobile number is required.',
            'phone.digits'      => 'Mobile number must be exactly 10 digits.',
            'phone.string'      => 'Mobile number must be a valid string.',
        ];
    }

    /**
     * Customize failed validation to return ONLY the first message
     * in your ApiResponse format — matches your original code exactly
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }
}