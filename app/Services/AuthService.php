<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepository;
    protected $otpService;

    public function __construct(
        UserRepository $userRepository,
        OtpService $otpService
    ) {
        $this->userRepository = $userRepository;
        $this->otpService     = $otpService;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->findByMobile($data['phone']);
        
        if($user) {
        return false;
        }
        $data = [
            'phone' => $data['phone'],
            'name' => $data['name']
        ];
        return $this->userRepository->create($data);
       
    }

     public function login(string $phone)
    {
        $user = $this->userRepository->findByMobile($phone);
        if($user) {
        $this->otpService->generateAndStore($user->phone);
        return $user;
        }
        return false;
       
    }

    public function verifyAndAuthenticate(string $mobile, string $otp, ?string $name = null): array
    {
        if (! $this->otpService->verify($mobile, $otp)) {
            throw ValidationException::withMessages(['otp' => 'Invalid or expired OTP']);
        }

        $user = $this->userRepository->findByMobile($mobile);

        if (! $user) {
           return [];
        }

        //$token = $user->createToken('mobile-app-token')->accessToken;
        $token = null;

        return [
            'user'    => $user->load('onboarding'),
            'token'   => $token,
        ];
    }
}