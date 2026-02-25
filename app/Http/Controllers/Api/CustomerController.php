<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }


    public function store(Request $request)
    {
        // Validation
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name'  => ['required', 'string', 'min:2', 'max:100'],
            'phone' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                Rule::unique('customers', 'phone'),
            ],
            'email' => ['nullable', 'email:rfc,dns', 'max:100'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
           
            $customer = $this->service->createCustomer([
                'name'   => $request->name,
                'phone'  => $request->phone,
                'email'  => $request->email ?? null,
                'user_id' => $user->id,   
            ]);

            return ApiResponse::success(
                message: 'Customer added successfully',
                key: 'customer',
                data: $customer,
                status: 201
            );

        } catch (\Exception $e) {
            Log::error('Failed to create customer', [
                'user_id' => $user->id,
                'phone'   => $request->phone,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            // Handle unique constraint violation more gracefully (if needed)
            if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] === 23505) {
                return ApiResponse::error(
                    message: 'This phone number is already registered.',
                    status: 409
                );
            }

            return ApiResponse::error(
                message: 'Failed to add customer. Please try again.',
                status: 500
            );
        }
    }

    public function index(Request $request)
    {
        try {
            
            $customers = $this->service->getCustomers();
            
        return ApiResponse::success('Getting customers successfully!' , "customers" ,$customers);
        } catch (\Exception $e) {
            Log::error('getting customers failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to reterive customers. Please try again.', 500);
        }
    }

    // app/Http/Controllers/Api/CustomerController.php

    public function show(Customer $customer)
    {
        try {
          $records = $this->service->getSingle($customer);
            
        return ApiResponse::success('Customer details fetched successfully!' , "customer" ,$records);
        } catch (\Exception $e) {
            Log::error('getting Customer details fetched failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to reterive Customer details. Please try again.', 500);
        }
    }
}