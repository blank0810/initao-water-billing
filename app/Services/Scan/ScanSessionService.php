<?php

namespace App\Services\Scan;

use App\Events\ScanCompleted;
use App\Models\ScanSession;

class ScanSessionService
{
    public function createSession(int $userId): ScanSession
    {
        return ScanSession::create([
            'token' => ScanSession::generateToken(),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
            'created_by' => $userId,
        ]);
    }

    public function getValidSessionByToken(string $token): ?ScanSession
    {
        $session = ScanSession::where('token', $token)->first();

        if (! $session || $session->status === 'completed') {
            return null;
        }

        if ($session->isExpired()) {
            $session->update(['status' => 'expired']);

            return null;
        }

        return $session;
    }

    public function getPendingSession(string $token): ?ScanSession
    {
        return ScanSession::where('token', $token)
            ->where('status', 'pending')
            ->first();
    }

    public function completeSession(ScanSession $session, array $scannedData): void
    {
        $session->update([
            'status' => 'completed',
            'scanned_data' => $scannedData,
            'completed_at' => now(),
        ]);

        broadcast(new ScanCompleted($session));
    }
}
