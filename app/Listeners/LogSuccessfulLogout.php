<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;

class LogSuccessfulLogout
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Logout $event): void
    {
        if ($event->user) {
            activity('authentication')
                ->causedBy($event->user)
                ->withProperties([
                    'ip_address' => $this->request->ip(),
                    'user_agent' => $this->request->userAgent(),
                    'logout_at' => now()->toDateTimeString(),
                ])
                ->log('User logged out');
        }
    }
}
