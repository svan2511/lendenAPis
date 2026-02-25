<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ProductRepository
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    public function index(): Collection
    {
        return Product::where('user_id',Auth::user()->id)->get();
         
    }

    public function find($id): Product | null
    {
        return Product::where('user_id',Auth::user()->id)->where('id',$id)->first();
         
    }

    public function getLowstock() {
        return Product::where('user_id',Auth::user()->id)->where('type', 'product')
        ->where(function ($query) {
            $query->where('unit_type', 'fixed')
                  ->where('quantity', '<=', 5)
                  ->orWhere('unit_type', 'weight')
                  ->where('quantity', '<=', 10);
        })
        ->count();
    }


    // Agar future mein aur methods chahiye (update, delete, etc.) to yahan add kar dena
}