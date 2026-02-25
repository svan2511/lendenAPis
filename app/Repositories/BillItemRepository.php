<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class BillItemRepository
{
    protected $model;

    public function __construct(BillItem $model)
    {
        $this->model = $model;
    }

    public function create(array $data): BillItem
    {
        return $this->model->create($data);
    }

    // public function index(): Collection
    // {
    //     return Product::where('user_id',Auth::user()->id)->get();
         
    // }

    // public function find($id): Product | null
    // {
    //     return Product::where('user_id',Auth::user()->id)->where('id',$id)->first();
         
    // }


    // Agar future mein aur methods chahiye (update, delete, etc.) to yahan add kar dena
}