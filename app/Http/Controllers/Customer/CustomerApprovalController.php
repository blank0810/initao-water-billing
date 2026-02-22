<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customers\CustomerApprovalService;
use Illuminate\Http\Request;

class CustomerApprovalController extends Controller
{
    public function __construct(
        protected CustomerApprovalService $approvalService
    ) {}

    public function reactivate(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer|exists:customer,cust_id']);

        try {
            $customer = $this->approvalService->reactivateCustomer($request->input('customer_id'));

            return response()->json([
                'success' => true,
                'message' => 'Customer reactivated successfully.',
                'data' => $customer->load('status'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
