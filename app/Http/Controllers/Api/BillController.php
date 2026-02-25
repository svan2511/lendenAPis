<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Product;
use App\Services\BillItemService;
use App\Services\BillService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BillController extends Controller
{
    public $billService;
    public $billItemService;

    public function __construct(BillService $service , BillItemService $billItemService)
    {
        $this->billService = $service;
         $this->billItemService = $billItemService;
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'customerId'    => 'required|exists:customers,id',
            'totalAmount'   => 'required|numeric|min:0',
            'status'        => 'required|in:FULL,PARTIAL',
            'paidAmount'    => 'required_if:status,PARTIAL|numeric|min:0',
            'items'         => 'required|array|min:1',
            'items.*.productId'   => 'required|exists:products,id',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.price'       => 'required|numeric|min:0',
            'items.*.total'       => 'required|numeric|min:0',
            // optional: 'items.*.unit_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            $billData = [
                'customer_id'      => $request->customerId,
                'user_id'          => $user->id,
                'total_amount'     => $request->totalAmount,
                'paid_amount'      => $request->status === 'FULL' ? $request->totalAmount : ($request->paidAmount ?? 0),
                'remaining_amount' => $request->remainingAmount ?? ($request->totalAmount - ($request->paidAmount ?? 0)),
                'status'           => $request->status,
                'billed_at'        => now(),
            ];

            $bill = $this->billService->createBill($billData, $request->items);

            return ApiResponse::success('Bill created successfully!', 'bill', $bill->load('items'));

        } catch (\Exception $e) {
            Log::error('Bill creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (str_contains($e->getMessage(), 'Insufficient stock')) {
                Log::error($e->getMessage());
                return ApiResponse::error("Failed to create bill", 500);
            }

            return ApiResponse::error('Failed to create bill. Please try again.', 500);
        }
    }

        /**
         * Marks a bill as fully paid by setting status to 'FULL',
         * moving remaining amount to paid_amount, and zeroing remaining_amount.
         *
         * @param Bill $bill
         * @return \Illuminate\Http\JsonResponse
         */
        public function markFullPaid(Bill $bill)
        {

       
            // Authorization check - ensure the bill belongs to the authenticated user
            if ($bill->user_id !== Auth::id()) {
                return ApiResponse::error(
                    message: 'You do not have permission to update this bill.',
                    status: 403
                );
            }

            // No need to mark as paid if already fully paid
            if ($bill->status === 'FULL' || $bill->remaining_amount <= 0) {
                return ApiResponse::success(
                    message: 'Bill is already fully paid.',
                     key:"bill",
                    data: $bill->fresh() // or just return $bill
                );
            }

            try {
                
                $bill->update([
                    'status'          => 'FULL',
                    'paid_amount'     => $bill->paid_amount + $bill->remaining_amount,
                    'remaining_amount' => 0,
                 
                ]);

                // Optional: refresh the model if you want the latest state
                $bill->refresh();

                return ApiResponse::success(
                    message: 'Bill marked as fully paid successfully.',
                    key:"bill",
                    data: $bill,
                    status: 200
                );

            } catch (Exception $e) {
                Log::error('Failed to mark bill as fully paid', [
                    'bill_id'     => $bill->id,
                    'user_id'     => Auth::id(),
                    'error'       => $e->getMessage(),
                    'trace'       => $e->getTraceAsString(),
                ]);

                return ApiResponse::error(
                    message: 'Failed to update bill status.',
                    status: 500
                );
            }
        }


        public function getProfitOrLoss(Request $request)
        {

            try {
                
                $data = $this->billService->getMontlyProfitOrLoss($request);

                return ApiResponse::success(
                    message: 'Profit fetched successfully.',
                    key:"data",
                    data: $data,
                    status: 200
                );

            } catch (Exception $e) {
                Log::error('Failed to getting Profit', [
                    'error'       => $e->getMessage(),
                    'trace'       => $e->getTraceAsString(),
                ]);

                return ApiResponse::error(
                    message: 'Failed to getting Profit.',
                    status: 500
                );
            }
        }
}

