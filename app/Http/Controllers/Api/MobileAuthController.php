<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingSchedule;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    /**
     * Authenticate a mobile app user and return user details.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
        ]);

        // Rate limiting
        $throttleKey = Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        // Find user by username
        $user = User::where('username', $request->input('username'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey);

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        // Generate Sanctum Token
        $token = $user->createToken($request->input('device_name'))->plainTextToken;

        // Load roles for the user
        $user->load('roles');

        // Get active reading schedules assigned to this user
        $activeSchedules = ReadingSchedule::where('reader_id', $user->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['period', 'area'])
            ->get()
            ->map(function ($schedule) {
                return [
                    'schedule_id' => $schedule->schedule_id,
                    'period_name' => $schedule->period?->per_name,
                    'area_desc' => $schedule->area?->a_desc,
                    'status' => $schedule->status,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token' => $token, // Add token to the response
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('role_name')->toArray(),
                'active_schedules' => $activeSchedules->toArray(),
            ],
        ]);
    }
}
