<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillRepository
{
    protected $model;

    public function __construct(Bill $model)
    {
        $this->model = $model;
    }

    public function create(array $data): Bill
    {
        return $this->model->create($data);
    }

    public function monthlyProfitLoss($request)
    {
        $month = $request->query('month'); // 1–12
        $year  = $request->query('year');

        // Default to current month/year
        $month = $month ? (int) $month : Carbon::now()->month;
        $year  = $year  ? (int) $year  : Carbon::now()->year;

        // Basic validation
        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
           throw new Exception('Invalid month or year');
        }

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end   = $start->copy()->endOfMonth();

        // 1. Total Sales = sum of all bills.total_amount in the month
        $totalSales = DB::table('bills')
            ->where('user_id' ,Auth::user()->id)
            ->whereBetween('billed_at', [$start, $end])
            ->sum('total_amount');

        // 2. Total Expenses
        // → Change 'expense_date' to your actual column name if different
        //   (from your frontend code it seems to be expense_date)
        $totalExpenses = DB::table('expenses')
            ->where('user_id' ,Auth::user()->id)
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // Alternative: if you already have a working getMonthlySummary logic/service
        // $summary = app(YourExpenseService::class)->getMonthlySummary($month, $year);
        // $totalExpenses = $summary['total_expenses'] ?? 0;

        $netProfit = $totalSales - $totalExpenses;

        return [
                'month'          => (int) $month,
                'year'           => (int) $year,
                'total_sales'    => round($totalSales, 2),
                'total_expenses' => round($totalExpenses, 2),
                'net_profit'     => round($netProfit, 2),
            ];
    }

}