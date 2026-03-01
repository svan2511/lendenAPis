<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Helpers\ApiResponse; // ← adjust namespace if needed

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or auth()->check() if needed
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:150'],
            'amount'        => ['required', 'numeric', 'min:0.01'],
            'expense_date'  => ['required', 'date'],
            'category'      => [
                'nullable',
                Rule::in(['rent', 'electricity', 'purchase_stock', 'salary', 'transport', 'marketing', 'maintenance', 'other'])
            ],
            'payment_mode'  => ['nullable', 'string', 'max:50'],
            'description'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'Expense title is required.',
            'title.string'           => 'Title must be a valid string.',
            'title.max'              => 'Title may not be greater than 150 characters.',

            'amount.required'        => 'Amount is required.',
            'amount.numeric'         => 'Amount must be a number.',
            'amount.min'             => 'Amount must be at least 0.01.',

            'expense_date.required'  => 'Expense date is required.',
            'expense_date.date'      => 'Expense date must be a valid date.',

            'category.in'            => 'Invalid expense category selected.',

            'payment_mode.string'    => 'Payment mode must be a valid string.',
            'payment_mode.max'       => 'Payment mode may not be greater than 50 characters.',

            'description.string'     => 'Description must be a valid string.',
        ];
    }

    /**
     * Return only the first validation error message
     * using your custom ApiResponse helper — same behavior as before
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }
}