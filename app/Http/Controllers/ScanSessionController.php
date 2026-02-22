<?php

namespace App\Http\Controllers;

use App\Events\ScanCompleted;
use App\Models\ScanSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanSessionController extends Controller
{
    /**
     * Create a new scan session (called from PC, authenticated).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $session = ScanSession::create([
                'token' => ScanSession::generateToken(),
                'status' => 'pending',
                'expires_at' => now()->addMinutes(5),
                'created_by' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'token' => $session->token,
                'scan_url' => url('/scan/'.$session->token),
                'expires_at' => $session->expires_at->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create scan session.',
            ], 500);
        }
    }

    /**
     * Phone scanning page (public, token-validated).
     */
    public function show(string $token)
    {
        $session = ScanSession::where('token', $token)->first();

        if (! $session || $session->status === 'completed') {
            return view('scan.expired', ['reason' => 'used']);
        }

        if ($session->isExpired()) {
            $session->update(['status' => 'expired']);

            return view('scan.expired', ['reason' => 'expired']);
        }

        return view('scan.show', ['token' => $token]);
    }

    /**
     * Receive scanned data from phone (public, token-validated).
     */
    public function submit(Request $request, string $token): JsonResponse
    {
        $session = ScanSession::where('token', $token)
            ->where('status', 'pending')
            ->first();

        if (! $session) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired scan session.',
            ], 404);
        }

        if ($session->isExpired()) {
            $session->update(['status' => 'expired']);

            return response()->json([
                'success' => false,
                'message' => 'Scan session has expired.',
            ], 410);
        }

        $validated = $request->validate([
            'raw_data' => 'required|string',
            'format' => 'nullable|string|max:50',
        ]);

        $session->update([
            'status' => 'completed',
            'scanned_data' => $validated,
            'completed_at' => now(),
        ]);

        broadcast(new ScanCompleted($session));

        return response()->json([
            'success' => true,
            'message' => 'Scan received successfully.',
        ]);
    }
}
