<?php

use App\Models\ScanSession;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('scan-session.{token}', function ($user, $token) {
    $session = ScanSession::where('token', $token)->first();
    return $session && $session->created_by === $user->id;
});
