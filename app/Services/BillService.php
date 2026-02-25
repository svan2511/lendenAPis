<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Onboarding;
use App\Models\Product;
use App\Repositories\BillRepository;
use App\Repositories\OnboardingRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BillService
{
     
     protected $repo;

    public function __construct(BillRepository $repo)
    {

        $this->repo = $repo;
    }
   
   public function createBill(array $billData, array $items): Bill
    {
        return DB::transaction(function () use ($billData, $items) {

            $bill = Bill::create($billData);

            foreach ($items as $itemData) {
                $product = Product::findOrFail($itemData['productId']);

                // Critical: check & decrease stock inside transaction
                if($product->business_type === "product") {
                    $product->decreaseStock($itemData['quantity']);
                }
                
                $bill->items()->create([
                    'product_id'  => $product->id,
                    'quantity'    => $itemData['quantity'],
                    'unit_price'  => $itemData['price'],
                    'total_price' => $itemData['total'],
                    'unit_type'   => $itemData['unit_type'] ?? null,
                ]);
            }

            return $bill->fresh(['items']);
        });
    }

    public function getMontlyProfitOrLoss($request) {
        return $this->repo->monthlyProfitLoss($request);
    }

}