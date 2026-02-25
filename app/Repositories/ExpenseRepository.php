<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ExpenseRepository
{
    protected $model;

    public function __construct(Expense $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Expense
    {
        return $this->model->create($data);
    }

    public function index($request): Collection
    {
         $query = Expense::where('user_id', Auth::id());

        // Optional filters
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('expense_date', $request->month)
                  ->whereYear('expense_date', $request->year);
        }

        return $query->orderBy('expense_date', 'desc')->get();
         
    }

    public function find($id): Expense | null
    {
        return Expense::where('user_id', Auth::id())->findOrFail($id);
         
    }

    public function summary($request){
        $month = $request->input('month', now()->month);
        $year  = $request->input('year', now()->year);

        $total = Expense::where('user_id', Auth::id())
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        return [
            'month'          => (int) $month,
            'year'           => (int) $year,
            'total_expenses' => (float) $total,
        ];
    }

  


    // Agar future mein aur methods chahiye (update, delete, etc.) to yahan add kar dena
}