<?php

namespace App\Services;

use App\Models\Onboarding;
use App\Repositories\CustomerRepository;
use App\Repositories\OnboardingRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CustomerService
{
     
     protected $repo;

    public function __construct(CustomerRepository $repo)
    {

        $this->repo = $repo;
    }
   
    public function createCustomer(array $data) {
        return $this->repo->create($data);
    }

     public function getCustomers() {
        return $this->repo->index();
    }

    public function getSingle($customer) {
        return $this->repo->singleCustomer($customer);
    }
}