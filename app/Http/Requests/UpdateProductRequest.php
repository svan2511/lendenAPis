<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Models\Product;

class UpdateProductRequest extends FormRequest
{
    /**
     * Authorize: must be logged in AND own this product
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        $product = $this->route('product'); // route model binding

        if (!$user || !$product) {
            return false;
        }

        return $product->user_id === $user->id;
    }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'required', 'string', 'min:2', 'max:120'],
            'price'     => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:9999999'],
            'quantity'  => ['sometimes', 'required', 'integer', 'min:0'],
            'type'      => ['sometimes', 'required', Rule::in(['product', 'service'])],
            'unit_type' => ['sometimes', 'required', Rule::in(['weight', 'fixed'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Product/service name is required when updating.',
            'name.string'        => 'Name must be a valid string.',
            'name.min'           => 'Name must be at least 2 characters long.',
            'name.max'           => 'Name may not be longer than 120 characters.',

            'price.required'     => 'Price is required when updating.',
            'price.numeric'      => 'Price must be a valid number.',
            'price.min'          => 'Price must be at least 0.01.',
            'price.max'          => 'Price cannot exceed 9,999,999.',

            'quantity.required'  => 'Quantity is required when updating.',
            'quantity.integer'   => 'Quantity must be a whole number.',
            'quantity.min'       => 'Quantity cannot be negative.',

            'type.required'      => 'Type is required when updating.',
            'type.in'            => 'Type must be either product or service.',

            'unit_type.required' => 'Unit type is required when updating.',
            'unit_type.in'       => 'Unit type must be either weight or fixed.',
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
        throw new HttpResponseException(
            ApiResponse::error(
                message: 'You do not have permission to update this product.',
                status: 403
            )
        );
    }
}