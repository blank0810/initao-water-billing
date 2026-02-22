<?php

namespace App\Http\Controllers\Scan;

use App\Http\Controllers\Controller;
use App\Services\Scan\ScanSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanSessionController extends Controller
{
    public function __construct(
        private ScanSessionService $scanSessionService
    ) {}

    /**
     * Create a new scan session (called from PC, authenticated).
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $session = $this->scanSessionService->createSession($request->user()->id);

            return response()->json([
                'success' => true,
                'token' => $session->token,
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
        $session = $this->scanSessionService->getValidSessionByToken($token);

        if (! $session) {
            return view('scan.expired', ['reason' => 'expired']);
        }

        return view('scan.show', ['token' => $token]);
    }

    /**
     * Receive scanned data from phone (public, token-validated).
     */
    public function submit(Request $request, string $token): JsonResponse
    {
        $session = $this->scanSessionService->getPendingSession($token);

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

        $this->scanSessionService->completeSession($session, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Scan received successfully.',
        ]);
    }
}
