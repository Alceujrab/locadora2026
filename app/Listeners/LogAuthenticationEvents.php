<?php

namespace App\Listeners;

use App\Models\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;

class LogAuthenticationEvents
{
    public function handleLogin(Login $event): void
    {
        LoginLog::create([
            'user_id' => $event->user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'action' => 'login',
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            LoginLog::create([
                'user_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'logout',
            ]);
        }
    }

    public function handleFailed(Failed $event): void
    {
        // Tenta encontrar user pelo email
        $user = \App\Models\User::where('email', $event->credentials['email'] ?? '')->first();
        if ($user) {
            LoginLog::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'action' => 'failed',
            ]);
        }
    }
}
