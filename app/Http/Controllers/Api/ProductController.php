<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public $service ;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

   public function store(Request $request)
    {
        $user = Auth::user();
        // Validation
        $validator = Validator::make($request->all(), [
            'name'      => ['required', 'string', 'min:2', 'max:120'],
            'price'     => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'quantity'  => ['nullable', 'integer', 'min:0'],
            'type'      => ['required', Rule::in(['product', 'service'])],
            'unit_type' => ['required', Rule::in(['weight', 'fixed'])],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            // Create the product (you can move this to a ProductService if you prefer)
            $product = $this->service->createProduct([
                'user_id'   => $user->id,
                'name'      => $request->name,
                'price'     => $request->price,
                'quantity'  => $request->quantity,
                'type'      => $request->type,
                'unit_type' => $request->unit_type,
            ]);

            return ApiResponse::success(
                message: 'Product added successfully',
                key: 'product',
                data: $product,           // or new ProductResource($product) if you use resources
                status: 201
            );

        } catch (\Exception $e) {
            Log::error('Failed to create product', [
                'user_id' => $user->id,
                'name'    => $request->name ?? 'N/A',
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                message: 'Failed to add item. Please try again.',
                status: 500
            );
        }
    }

     public function index(Request $request)
    {
        try {
            
            $products = $this->service->getProducts();
            
        return ApiResponse::success('Getting products successfully!' , "products" ,$products);
        } catch (\Exception $e) {
            Log::error('getting products failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to reterive products. Please try again.', 500);
        }
    }


    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        // Authorization: ensure the authenticated user owns this product
        if ($product->user_id !== $user->id) {
            return ApiResponse::error(
                message: 'You do not have permission to update this product.',
                status: 403
            );
        }

        // Validation - use 'sometimes' to allow partial updates
        $validator = Validator::make($request->all(), [
            'name'      => ['sometimes', 'required', 'string', 'min:2', 'max:120'],
            'price'     => ['sometimes', 'required', 'numeric', 'min:0.01', 'max:9999999'],
            'quantity'  => ['sometimes', 'required', 'integer', 'min:0'],
            'type'      => ['sometimes', 'required', Rule::in(['product', 'service'])],
            'unit_type' => ['sometimes', 'required', Rule::in(['weight', 'fixed'])],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            // Collect only the fields that were actually sent
            $updateData = $request->only([
                'name',
                'price',
                'quantity',
                'type',
                'unit_type',
            ]);

            // Remove null/empty values if you don't want to overwrite with empty
            $updateData = array_filter($updateData, function ($value) {
                return $value !== null && $value !== '';
            });

            // Perform the update
            $product->update($updateData);

            return ApiResponse::success(
                message: 'Product updated successfully',
                key: 'product',
                data: $product->fresh(),   // return fresh data with updated timestamps etc.
                status: 200
            );

        } catch (\Exception $e) {
            Log::error('Failed to update product', [
                'user_id'     => $user->id,
                'product_id'  => $product->id,
                'name'        => $request->name ?? 'N/A',
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                message: 'Failed to update item. Please try again.',
                status: 500
            );
        }
    }

      public function destroy(Request $request, Product $product)
    {
        $user = Auth::user();

        // Authorization: ensure the authenticated user owns this product
        if ($product->user_id !== $user->id) {
            return ApiResponse::error(
                message: 'You do not have permission to delete this product.',
                status: 403
            );
        }


        try {

            $deletedId = $product->id;
        
            $product->delete();

            return ApiResponse::success(
                message: 'Product deleted successfully',
                key: 'product',
                data: $deletedId,   // return fresh data with updated timestamps etc.
                status: 200
            );

        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'user_id'     => $user->id,
                'product_id'  => $product->id,
                'name'        => $request->name ?? 'N/A',
                'error'       => $e->getMessage(),
                'trace'       => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                message: 'Failed to delete item. Please try again.',
                status: 500
            );
        }
    }

     public function show($id)
    {
        try {
            
            $product = $this->service->getProductById($id);
            
            if(!$product) {
            return ApiResponse::error('Product not found!' , 200);
            }
           return ApiResponse::success('Getting product details successfully!' , "product" ,$product);

        } catch (\Exception $e) {
            Log::error('getting product details failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to reterive product details. Please try again.', 500);
        }
    }
}