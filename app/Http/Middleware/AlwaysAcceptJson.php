<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AlwaysAcceptJson
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config("logging.log_request_enabled")) {
            if (request()->wantsJson() || request()->is('api/*')) {
                Log::channel("request")->info("Request Info " . request()->url(), [
                    "url" => request()->url(),
                    "fullUrl" => request()->fullUrl(),
                    "path" => request()->path(),
                    "isSecure" => request()->isSecure(),
                    "method" => request()->method(),
                    "ip" => request()->ip(),
                    "ips" => request()->ips(),
                    "userAgent" => request()->userAgent(),
                    "header" => request()->header(),
                    "headers" => request()->headers,
                    "body" => request()->all(),
                ]);
            }
        }

        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
