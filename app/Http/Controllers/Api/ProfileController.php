<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function show()
    {
        $user = Auth::user();
        $shop = $user->profile;

       if (!$shop) {
        return ApiResponse::error(
                message: 'Profile not found.',
                status: 403
            );
        }

         return ApiResponse::success(
                message: 'Profile fetch successfully',
                key: 'profile',
                data: [
            'shopName' => $shop->name,
            'shopAddress' => $shop->address,
            "phone"   => $user->phone
                ], 
        status: 200
            );
    }

    // Add a new profile (POST /api/profile)
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->profile) {
              return ApiResponse::error(
                message: 'Profile already exists. Use update instead.',
                status: 400
            );
        }

        try {
          
             $validator = Validator::make($request->all(),[
                'shopName' => 'required|string|max:255',
                'shopAddress' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

        
             $profile = $this->service->createProfile([
               'user_id'   => $user->id,
                'name' => $request->shopName,
                'address' => $request->shopAddress ?? null,
            ]);
            return ApiResponse::success(
                message: 'Profile added successfully',
                key: 'profile',
                data: $profile, 
                status: 201
            );

           
        } catch (Exception $e) {
              Log::error('Failed to create profile ---'. $e->getMessage());
        return ApiResponse::error('Failed to create profile.', 500);
            
        }
    }

    // Update the profile (PUT /api/profile)
    public function update(Request $request)
    {

        try {

                $user = Auth::user();
                $shop = $user->profile;

                if (!$shop) {
                ApiResponse::error('Profile not found. Create one first.', 404);
                }

              $validator = Validator::make($request->all(),[
                'shopName' => 'required|string|max:255',
                'shopAddress' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

            

            $shop->update([
                'user_id'   => $user->id,
                'name' => $request->shopName,
                'address' => $request->shopAddress ?? null,
            ]);

              return ApiResponse::success(
                message: 'Profile updated successfully',
                key: 'profile',
                data: $shop, 
                status: 201
            );
        }catch (Exception $e) {
         Log::error('Failed to update profile ---'. $e->getMessage());
        return ApiResponse::error('Failed to update profile.', 500);
        }
    }
}
