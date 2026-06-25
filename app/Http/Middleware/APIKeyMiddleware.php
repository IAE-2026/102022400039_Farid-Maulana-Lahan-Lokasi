<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class APIKeyMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $apiKey = $request->header('X-IAE-KEY');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: X-IAE-KEY header is missing',
                'errors' => null
            ], 401);
        }

        if ($apiKey !== config('services.iae.allowed_key')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: Invalid X-IAE-KEY',
                'errors' => null
            ], 403);
        }

        return $next($request);
    }
}