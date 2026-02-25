<?php

namespace App\Services;

use App\Models\Onboarding;
use App\Repositories\OnboardingRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;

class ProductService
{
     
     protected $repo;

    public function __construct(ProductRepository $repo)
    {

        $this->repo = $repo;
    }
   
    public function createProduct(array $data) {
        return $this->repo->create($data);
    }

    public function getProducts() {
        return $this->repo->index();
    }

     public function getProductById($id) {
        return $this->repo->find($id);
    }

    public function getLowstockItemsCount() {
        return $this->repo->getLowstock();
    }
}