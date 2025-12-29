<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        session(['active_menu' => 'payment-management']);
        return view('pages.payment.payment-management');
    }

    public function create($customerCode = null)
    {
        session(['active_menu' => 'payment-management']);
        return view('pages.payment.create-payment', [
            'customerCode' => $customerCode
        ]);
    }

    public static function calculateStats()
    {
        // Dummy calculated data - replace with actual DB queries
        $payments = [
            ['amount' => 350.00, 'status' => 'Applicant'],
            ['amount' => 450.50, 'status' => 'Active / Connected'],
            ['amount' => 1250.75, 'status' => 'Delinquent'],
            ['amount' => 2500.00, 'status' => 'Disconnected'],
            ['amount' => 3500.00, 'status' => 'Reconnection Pending'],
            ['amount' => 900.00, 'status' => 'Suspended'],
        ];

        $totalBilled = array_sum(array_column($payments, 'amount'));
        $totalPaid = array_sum(array_filter(array_column($payments, 'amount'), function($amount, $key) use ($payments) {
            return $payments[$key]['status'] === 'Active / Connected';
        }, ARRAY_FILTER_USE_BOTH));
        $totalPending = $totalBilled - $totalPaid;

        return [
            'totalBilled' => '₱ ' . number_format($totalBilled, 2),
            'totalPaid' => '₱ ' . number_format($totalPaid, 2),
            'totalPending' => '₱ ' . number_format($totalPending, 2),
            'totalTransactions' => count($payments),
        ];
    }
}
