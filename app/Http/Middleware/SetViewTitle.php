<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetViewTitle
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = str_replace("-", " ", $request->segment(1));

        // if the route name ends with 's', and there is no other segment, then remove the 's'
        if (substr($routeName, -1) == 's' && !$request->segment(2) == null) {
            // the if if the last is 'ies'
            if (substr($routeName, -3) == 'ies') {
                $routeName = substr($routeName, 0, -3) . 'y';
            } else
                $routeName = substr($routeName, 0, -1);
        }

        $title = ucwords($routeName);
        view()->share('title', $title);
        return $next($request);
    }
}
