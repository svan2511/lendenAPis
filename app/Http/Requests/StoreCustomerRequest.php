<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // ← add auth/gates later if needed
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:2', 'max:100'],
            'phone' => [
                'required',
                'string',
                'size:10',           // exactly 10 characters
                'regex:/^[0-9]{10}$/', // only digits
                Rule::unique('customers', 'phone'),
            ],
            'email' => ['nullable', 'email:rfc,dns', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.size'       => 'Phone number must be exactly 10 digits.',
            'phone.regex'      => 'Phone number must contain only digits.',
            'phone.unique'     => 'This phone number is already registered.',
            'name.required'    => 'Customer name is required.',
            'email.email'      => 'Please enter a valid email address.',
        ];
    }
}