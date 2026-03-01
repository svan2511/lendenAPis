<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    public $expenseRepo;

    public function __construct(ExpenseRepository $expenseRepo)
    {
            $this->expenseRepo = $expenseRepo;
    }

    public function index(Request $request)
    {
        try{
           
        $expenses = $this->expenseRepo->index($request);
        return ApiResponse::success(
                message: 'Expenses list getting Successfully !',
                key:"expenses",
                data: $expenses, 
                status: 200
            );
        }catch(Exception $ex){
        Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to getting Expenses list. Please try again.',
                status: 500
            );
        }
       
    }

    public function store(StoreExpenseRequest $request)
    {
       
        try{
            $expense = $this->expenseRepo->create([
            'user_id' => Auth::id(),
            ...$request->validated(),
        ]);

        return ApiResponse::success(
                message: 'Expense created Successfully !',
                key:"expense",
                data: $expense, 
                status: 200
            );

        }catch(Exception $ex) {
         Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to create Expenses. Please try again.',
                status: 500
            );
        }
    }

    public function show($id)
    {
        try{
            $expense = $this->expenseRepo->find($id);

             if (!$expense) {
            return ApiResponse::error('No expense fond !');
        }

        return ApiResponse::success(
                message: 'Expense fetched Successfully !',
                key:"expense",
                data: $expense, 
                status: 200
            );

        }catch(Exception $ex) {
         Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to fetched Expense. Please try again.',
                status: 500
            );
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
            if (!$expense) {
            return ApiResponse::error('No expense fond !');
            }

            $validated = $request->validate([
                'title'         => 'sometimes|required|string|max:150',
                'amount'        => 'sometimes|required|numeric|min:0.01',
                'expense_date'  => 'sometimes|required|date',
                'category'      => [
                    'sometimes',
                    'nullable',
                    Rule::in(['rent', 'electricity', 'purchase_stock', 'salary', 'transport', 'marketing', 'maintenance', 'other'])
                ],
                'payment_mode'  => 'nullable|string|max:50',
                'description'   => 'nullable|string',
            ]);

              $expense->update($validated);

              return ApiResponse::success(
                message: 'Expense updated Successfully !',
                key:"expense",
                data: $expense, 
                status: 200
            );

    } catch(Exception $ex) {
     Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to update Expense. Please try again.',
                status: 500
            );
    }
    }

    public function destroy($id)
    {
       try{
         $expense = Expense::where('user_id', Auth::id())->findOrFail($id);
        if (!$expense) {
            return ApiResponse::error('No expense fond !');
        }
        $expense->delete();
        return ApiResponse::success(
                message: 'Expense deleted Successfully !',
                key:"expense",
                data: $expense, 
                status: 200
            );

       }catch(Exception $ex) {
         Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to delete Expense. Please try again.',
                status: 500
            );
       }
    }

    /**
     * Get monthly expense summary (useful for profit-loss report)
     */
    public function monthlySummary(Request $request)
    {
        try{
             
         $data = $this->expenseRepo->summary($request);
        return ApiResponse::success(
                message: 'Getting summary Successfully !',
                key:"summary",
                data: $data, 
                status: 200
            );

       }catch(Exception $ex) {
         Log::error($ex->getMessage());
         return ApiResponse::error(
                message: 'Failed to Getting summary. Please try again.',
                status: 500
            );
       }
        
    }
}