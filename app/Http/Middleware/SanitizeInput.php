<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!in_array(strtolower($request->method()), ['put', 'post'])) {
            return $next($request);
        }

        // Loop through each input and sanitize it
        $input = $request->all();

        array_walk_recursive($input, function (&$inputValue) {
            //             Use strip_tags() if:
            //                  You want to completely remove all HTML and don’t need users to submit any HTML in their input (e.g., form submissions like contact forms).
            //                  Your app needs strict enforcement, preventing any kind of HTML-based input.


            // Use htmlspecialchars() if:

            // You still want to allow users to submit special characters (e.g., text that contains < or > as plain text).
            // You are concerned about encoding for display purposes, ensuring input is safe to output while still allowing some HTML-like input as plain text.

            // Remove script tags and encode special characters
            // $inputValue = htmlspecialchars($inputValue, ENT_QUOTES, 'UTF-8');

            // Remove script tags
            $inputValue = strip_tags($inputValue);
        });

        // Replace the request input with the sanitized data
        $request->merge($input);

        return $next($request);
    }
}
