<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        session(['active_menu' => 'payment-management']);

        $customerId = $request->query('customerId');
        $customerName = $request->query('customerName');

        return view('pages.customer.payment-management', compact('customerId', 'customerName'));
    }
}
