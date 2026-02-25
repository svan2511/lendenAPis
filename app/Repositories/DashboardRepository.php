<?php

namespace App\Repositories;

use App\Models\Bill;
use App\Models\Onboarding;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardRepository
{
    protected $productSrvice;

    public function __construct(ProductService $productSrvice)
    {
        $this->productSrvice = $productSrvice;
    }


   public function index()
    {
        $todayStart = Carbon::today()->startOfDay();
        $todayEnd   = Carbon::today()->endOfDay();

        // Base query - DO NOT execute it yet
        $todayBillsQuery = Bill::where('user_id',Auth::user()->id)->whereBetween('billed_at', [$todayStart, $todayEnd]);

        // 1. Get all aggregates in ONE query (most efficient)
        $stats = $todayBillsQuery->selectRaw('
            SUM(total_amount) as total_sales,
            SUM(paid_amount) as cash_received,
            SUM(remaining_amount) as remaining_due,
            COUNT(*) as total_bills,
            SUM(CASE WHEN status = "FULL" THEN 1 ELSE 0 END) as full_paid_count,
            SUM(CASE WHEN status = "PARTIAL" THEN 1 ELSE 0 END) as partial_count
        ')->first();

        // If no bills today, stats will be null → handle with defaults
        $stats = $stats ?? (object) [
            'total_sales'     => 0,
            'cash_received'   => 0,
            'remaining_due'   => 0,
            'total_bills'     => 0,
            'full_paid_count' => 0,
            'partial_count'   => 0,
        ];

        // ── Total customers with any pending amount (all time) ──
        $customersWithPending = Bill::where('user_id', Auth::user()->id)
        ->where('remaining_amount', '>', 0)
        ->distinct('customer_id')
        ->count('customer_id');

        $lowStockCount = $this->productSrvice->getLowstockItemsCount();


        return [
            'total_sales'          => round($stats->total_sales, 2),
            'cash_in'              => round($stats->cash_received, 2),
            'cash_remaining'       => round($stats->remaining_due, 2),
            'bills_full_paid'      => (int) $stats->full_paid_count,
            'bills_partial'        => (int) $stats->partial_count,
            'total_bills_today'    => (int) $stats->total_bills,
            'customers_with_pending'  => (int) $customersWithPending,
            'today_date'           => $todayStart->format('d M Y'), // optional
            'low_stock_count'      =>  $lowStockCount
        ];
    }

}


