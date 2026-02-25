<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Onboarding;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{
    protected $model;


    public function create(array $data): Customer
    {
        return Customer::create($data);
         
    }

    public function index(): Collection
    {
        return Customer::query()
            ->select([
                'customers.id',
                'customers.name',
               'customers.phone',
                DB::raw('COALESCE((
                    SELECT SUM(bills.remaining_amount)
                    FROM bills
                    WHERE bills.customer_id = customers.id
                ), 0) as total_remaining')
            ])
            ->where('user_id',Auth::user()->id)
            ->orderBy('customers.name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id'            => $customer->id,
                    'name'            => $customer->name,
                     'phone'            => $customer->phone,
                    'total_remaining' => round($customer->total_remaining, 2),
                ];
            });
         
    }

    public function singleCustomer(Customer $customer)
    {
       
        $bills = $customer->bills()
                        ->select('id', 'billed_at', 'total_amount', 'paid_amount')
                        ->orderByDesc('billed_at')
                        ->get(); // ← changed from paginate to get


        // Calculate summary (unchanged)
        $totalOrders = $bills->count();
        $totalAmount = round($bills->sum('total_amount'), 2);
        $totalPaid   = round($bills->sum('paid_amount'), 2);
        $totalPending = $totalAmount - $totalPaid;

        // Map bills to frontend format (unchanged)
        $orders = $bills->map(function ($bill) {
            return [
                'id'           => $bill->id,
                'show_id'      => 'ORD-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT),
                'date'    => $bill->billed_at->format('d M Y'),
                'total'   => round($bill->total_amount, 2),
                'paid'    => round($bill->paid_amount, 2),
                'balance' => round($bill->total_amount - $bill->paid_amount, 2),
            ];
        });


        return [
            'customer' => [
                'name'  => $customer->name,
                'phone' => $customer->phone,
            ],
            'summary' => [
                'totalOrders'  => $totalOrders,
                'totalAmount'  => $totalAmount,
                'totalPaid'    => $totalPaid,
                'totalPending' => $totalPending,
            ],
            'orders'  => $orders,
        ];
    }

}


