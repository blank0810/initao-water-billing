<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;

class LogFailedLogin
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Failed $event): void
    {
        activity('authentication')
            ->withProperties([
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'attempted_email' => $event->credentials['email'] ?? 'unknown',
                'failed_at' => now()->toDateTimeString(),
            ])
            ->log('Failed login attempt');
    }
}
