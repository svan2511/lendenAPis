<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        
        try {

            $user = $this->authService->register(["phone" => $request->phone ,"name" => $request->name]);

            if(!$user) {
                return ApiResponse::error('User Already Exist !.', 200);
            }

            return ApiResponse::success('User created successfully!' , "user" ,$user);
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);
            return ApiResponse::error('Failed to create user. Please try again.', 500);
        }
    }

   public function verifyOtp(VerifyOtpRequest $request)
    {
        try {
            $result = $this->authService->verifyAndAuthenticate(
                $request->phone,
                $request->otp,
                $request->name ?? null
            );

        return ApiResponse::success('OTP verified successfully!', "data" ,$result);

        } catch (ValidationException $e) {
            return ApiResponse::validationError($e->getMessage() ?: 'Invalid or expired OTP');
            // or: return ApiResponse::validationError($e->errors()->first());
        } catch (\Exception $e) {
            Log::error('OTP verification failed', [
                'phone' => $request->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(), // optional - more debug info
            ]);

            return ApiResponse::error('Failed to verify OTP. Please try again.', 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            
            $user = $this->authService->login($request->phone);
            if(!$user) {
                return ApiResponse::error('User not found !.', 200);
            }

        return ApiResponse::success('Otp send successfully!' , "data" ,$user);
        } catch (\Exception $e) {
            Log::error('OTP send failed', [
                'phone' => $request->phone,
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to send OTP. Please try again.', 500);
        }
    }

    // Test protected route
    public function me(Request $request)
    {
        try {
            $user = Auth::user();
            if(!$user) {
                return ApiResponse::error('User not found !.', 200);
            }

        return ApiResponse::success('Getting User profile !' , "user" ,$user);
        } catch (\Exception $e) {
            Log::error('Getting User profile failed', [
                'error' => $e->getMessage()
            ]);
        return ApiResponse::error('Failed to Getting User profile. Please try again.', 500);
        }
        
    }

   public function logout(Request $request)
    {
        $user = $request->user();

        try {
            $request->user()->currentAccessToken()->delete();
            return ApiResponse::success('Logged out successfully !' , "user" ,$user);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return ApiResponse::error('Logout failed. Please try again !.', 500);
        }
    }
}