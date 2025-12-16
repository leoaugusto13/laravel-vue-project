<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoggerService
{
    public static function log(string $action, ?string $description = null, ?int $userId = null): void
    {
        try {
            SystemLog::create([
                'user_id' => $userId ?? Auth::id(),
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to avoid breaking the application flow if logging fails
            // In a real production app, we might want to log this to a file
        }
    }
}
