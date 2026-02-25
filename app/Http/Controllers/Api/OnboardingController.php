<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\OnboardingResponse;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    /**
     * Save onboarding answers for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    protected $userservice;

    public function __construct(UserService $userservice)
    {
        $this->userservice = $userservice;
    }



    public function store(Request $request )
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::unauthorized('Please login to continue');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'business_type'     => ['required', Rule::in(['product', 'service', 'both', 'freelance'])],
            'has_stock'         => ['required', 'boolean'],
            'has_appointments'  => ['required', 'boolean'],
            'has_staff'         => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            // Update or create onboarding record (prevents duplicates)
        
             $response = $this->userservice->createOnboarding([
                    'business_type'     => $request->business_type,
                    'has_stock'         => $request->has_stock,
                    'has_appointments'  => $request->has_appointments,
                    'has_staff'         => $request->has_staff ?? null,
                    'user_id'           => $user->id
            ]);


            return ApiResponse::success(
                message: 'Onboarding completed successfully!',
                key:"onboarding",
                data: $response, // or you can return user onboarding data if needed
                status: 200
            );

        } catch (\Exception $e) {
            Log::error('Failed to save onboarding data', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return ApiResponse::error(
                message: 'Failed to save onboarding data. Please try again.',
                status: 500
            );
        }
    }

    /**
     * Get the user's saved onboarding data
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::unauthorized('Please login to continue');
        }

        try {
            $onboarding = $user->onboarding;

            if (!$onboarding) {
                return ApiResponse::error(
                    message: 'No onboarding data found',
                    status: 404
                );
            }

            return ApiResponse::success(
                message: 'Onboarding data retrieved successfully',
                key:"onboarding",
                data: $onboarding,
                status: 200
            );

        } catch (\Exception $e) {
            Log::error('Failed to fetch onboarding data', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return ApiResponse::error(
                message: 'Failed to retrieve onboarding data',
                status: 500
            );
        }
    }
}