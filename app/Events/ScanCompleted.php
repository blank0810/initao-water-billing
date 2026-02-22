<?php

namespace App\Events;

use App\Models\ScanSession;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanCompleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ScanSession $scanSession
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('scan-session.' . $this->scanSession->token),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'raw_data' => $this->scanSession->scanned_data['raw_data'] ?? '',
            'format' => $this->scanSession->scanned_data['format'] ?? 'unknown',
        ];
    }

    public function broadcastAs(): string
    {
        return 'scan.completed';
    }
}
