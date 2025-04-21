<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureSystemKey
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
        if (
            !$request->header('System-Key') ||
            $request->header('System-Key') !== config('app.system_key')
        ) {
            return response()->json([
                'result' => false,
                'message' => 'Request not found!'
            ], Response::HTTP_NOT_FOUND);
        }

        return $next($request);
    }
}
