<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        session(['active_menu' => 'user-list']);

        return view('pages.user.user-list');
    }

    public function store(Request $request)
    {
        // Handle user creation
        return response()->json(['success' => true, 'message' => 'User created successfully']);
    }

    public function update(Request $request, $id)
    {
        // Handle user update
        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }

    public function destroy($id)
    {
        // Handle user deletion
        return response()->json(['success' => true, 'message' => 'User deleted successfully']);
    }
}
