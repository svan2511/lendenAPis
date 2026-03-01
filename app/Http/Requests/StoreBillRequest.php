<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\ApiResponse; // ← adjust namespace if ApiResponse is located elsewhere

class StoreBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // or check permissions, e.g. auth()->check()
    }

    public function rules(): array
    {
        return [
            'customerId'          => ['required', 'exists:customers,id'],
            'totalAmount'         => ['required', 'numeric', 'min:0'],
            'status'              => ['required', 'in:FULL,PARTIAL'],

            // Conditional: paidAmount required only when status = PARTIAL
            'paidAmount'          => ['required_if:status,PARTIAL', 'numeric', 'min:0'],

            'items'               => ['required', 'array', 'min:1'],
            'items.*.productId'   => ['required', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.price'       => ['required', 'numeric', 'min:0'],
            'items.*.total'       => ['required', 'numeric', 'min:0'],

            // Uncomment if you decide to add it later
            // 'items.*.unit_type'   => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'customerId.required'       => 'Customer is required.',
            'customerId.exists'         => 'Selected customer does not exist.',

            'totalAmount.required'      => 'Total amount is required.',
            'totalAmount.numeric'       => 'Total amount must be a number.',
            'totalAmount.min'           => 'Total amount cannot be negative.',

            'status.required'           => 'Bill status is required.',
            'status.in'                 => 'Status must be FULL or PARTIAL.',

            'paidAmount.required_if'    => 'Paid amount is required when status is PARTIAL.',
            'paidAmount.numeric'        => 'Paid amount must be a number.',
            'paidAmount.min'            => 'Paid amount cannot be negative.',

            'items.required'            => 'At least one item is required.',
            'items.array'               => 'Items must be an array.',
            'items.min'                 => 'At least one item is required.',

            'items.*.productId.required' => 'Product is required for each item.',
            'items.*.productId.exists'   => 'One or more products do not exist.',

            'items.*.quantity.required'  => 'Quantity is required for each item.',
            'items.*.quantity.numeric'   => 'Quantity must be a number.',
            'items.*.quantity.min'       => 'Quantity must be at least 0.01.',

            'items.*.price.required'     => 'Price is required for each item.',
            'items.*.price.numeric'      => 'Price must be a number.',
            'items.*.price.min'          => 'Price cannot be negative.',

            'items.*.total.required'     => 'Total is required for each item.',
            'items.*.total.numeric'      => 'Total must be a number.',
            'items.*.total.min'          => 'Total cannot be negative.',
        ];
    }

    /**
     * Return only the FIRST validation error message
     * in your custom ApiResponse format — same as original controller
     */
    protected function failedValidation(Validator $validator)
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            ApiResponse::validationError($firstError)
        );
    }
}