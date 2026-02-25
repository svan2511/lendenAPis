<?php

namespace App\Services;

use App\Models\Onboarding;
use App\Repositories\OnboardingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Cache;

class UserService
{
     
     protected $onboardingrepo;
     protected $userRepo;

    public function __construct(OnboardingRepository $onboardingrepo , UserRepository $userRepo)
    {

        $this->onboardingrepo = $onboardingrepo;
         $this->userRepo = $userRepo;
    }
   
    public function createOnboarding(array $data) {
        return $this->onboardingrepo->create($data);
    }

    public function createProfile(array $data) {
       return  $this->userRepo->createProfile($data);
    }
}