<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse; // ← Adjust namespace if ApiResponse is in different folder/namespace

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change to your auth logic if needed (e.g. auth()->check())
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'  => 'required|string',
            'phone' => 'required|string|digits:10',
            
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'phone.required'    => 'Please enter mobile number.',
            'phone.string'      => 'Mobile number must be a valid.',
            'phone.digits'      => 'Mobile number must be exactly 10 digits.',

            'name.required'     => 'Please enter your name.',
            'name.string'       => 'Name must be a valid.',
        ];
    }

    /**
     * Handle a failed validation attempt — match your original format
     * (only first message via ApiResponse::validationError)
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $firstErrorMessage = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstErrorMessage)
        );
    }
}