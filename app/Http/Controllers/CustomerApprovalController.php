<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerApprovalController extends Controller
{
    public function index()
    {
        session(['active_menu' => 'approve-customer']);
        return view('pages.customer.approve-customer');
    }

    public function approve(Request $request)
    {
        $customerId = $request->input('customer_id');
        
        // Here you would typically update the customer status in the database
        // For now, we'll just redirect with the customer ID
        
        return redirect()->route('service.connection', ['customer_id' => $customerId])
                        ->with('success', 'Customer approved successfully. Please proceed with service connection.');
    }

    public function decline(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'reason' => 'required|string|max:500'
        ]);

        $customerId = $request->input('customer_id');
        $reason = $request->input('reason');
        
        // Here you would typically update the customer status in the database
        // and store the decline reason
        
        return response()->json([
            'success' => true,
            'message' => 'Customer application declined successfully.'
        ]);
    }

    public function restore(Request $request)
    {
        $customerId = $request->input('customer_id');
        
        // Here you would typically restore the customer to pending status
        // in the database
        
        return response()->json([
            'success' => true,
            'message' => 'Customer application restored to approval queue.'
        ]);
    }
}
