<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse; // ← adjust namespace if your ApiResponse lives elsewhere

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← add real auth logic if needed (e.g. user is logged in)
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|digits:10',
            'otp'   => 'required|string|digits:6',
        ];
    }

    /**
     * Custom validation messages (English, professional)
     */
    public function messages(): array
    {
        return [
            'phone.required'    => 'Mobile number is required.',
            'phone.digits'      => 'Mobile number must be exactly 10 digits.',
            'phone.string'      => 'Mobile number must be a valid string.',

            'otp.required'      => 'OTP is required.',
            'otp.digits'        => 'OTP must be exactly 6 digits.',
            'otp.string'        => 'OTP must be a valid string.',
        ];
    }

    /**
     * Override to return ONLY the first error message
     * in your custom ApiResponse format — exactly like your original code
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }
}