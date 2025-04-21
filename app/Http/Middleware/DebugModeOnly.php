<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugModeOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.debug')) {
            return response()->json([
                'message' => 'This endpoint is only available in debug mode',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
