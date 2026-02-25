<?php

namespace App\Services;

use App\Models\Onboarding;
use App\Repositories\BillItemRepository;
use App\Repositories\BillRepository;
use App\Repositories\OnboardingRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;

class BillItemService
{
     
     protected $repo;

    public function __construct(BillItemRepository $repo)
    {

        $this->repo = $repo;
    }
   
    public function createBillItems(array $data) {
        return $this->repo->create($data);
    }

    // public function getProducts() {
    //     return $this->repo->index();
    // }

    //  public function getProductById($id) {
    //     return $this->repo->find($id);
    // }
}