<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        session(['active_menu' => 'customer-list']);
        return view('pages.customer.customer-list');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'address' => 'required|string|max:500',
        ]);

        // Here you would typically save to database
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully.'
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'address' => 'required|string|max:500',
        ]);

        // Here you would typically update in database
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully.'
        ]);
    }

    public function destroy($id)
    {
        // Here you would typically delete from database
        // For now, just return success response
        
        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    public function printCount($id)
    {
        // Here you would typically get print count from database
        // For now, return mock data
        
        return response()->json([
            'success' => true,
            'print_count' => rand(0, 10)
        ]);
    }
}