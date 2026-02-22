<?php

namespace App\Services\Scan;

use App\Events\ScanCompleted;
use App\Models\ScanSession;
use Illuminate\Support\Facades\DB;

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

    /**
     * Atomically find a pending session, validate it, and mark it as completed.
     *
     * Uses lockForUpdate() to prevent TOCTOU race conditions where concurrent
     * requests could both complete the same session.
     *
     * @return array{session: ScanSession, status: string}
     */
    public function completeSession(string $token, array $scannedData): array
    {
        return DB::transaction(function () use ($token, $scannedData) {
            $session = ScanSession::where('token', $token)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (! $session) {
                return ['session' => null, 'status' => 'not_found'];
            }

            if ($session->isExpired()) {
                $session->update(['status' => 'expired']);

                return ['session' => $session, 'status' => 'expired'];
            }

            $session->update([
                'status' => 'completed',
                'scanned_data' => $scannedData,
                'completed_at' => now(),
            ]);

            broadcast(new ScanCompleted($session));

            return ['session' => $session, 'status' => 'completed'];
        });
    }
}
