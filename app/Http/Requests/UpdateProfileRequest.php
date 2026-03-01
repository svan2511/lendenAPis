<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();

        // Must be authenticated
        if (!$user) {
            return false;
        }

        // Profile must already exist for update
        if (!$user->profile) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'shopName'    => ['required', 'string', 'max:255'],
            'shopAddress' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'shopName.required' => 'Shop name is required.',
            'shopName.string'   => 'Shop name must be a valid string.',
            'shopName.max'      => 'Shop name may not be greater than 255 characters.',

            'shopAddress.string' => 'Shop address must be a valid string.',
            'shopAddress.max'    => 'Shop address may not be greater than 500 characters.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }

    protected function failedAuthorization()
    {
        $user = Auth::user();

        if (!$user) {
            throw new HttpResponseException(
                ApiResponse::unauthorized('Please login to continue')
            );
        }

        // Profile does not exist
        throw new HttpResponseException(
            ApiResponse::error(
                message: 'Profile not found. Create one first.',
                status: 404
            )
        );
    }
}