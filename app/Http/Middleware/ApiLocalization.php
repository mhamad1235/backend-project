<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiLocalization
{
    public function handle(Request $request, Closure $next)
    {
        //check header request and determine the language
        $local = "en";

        // request has lang header and lang is available in app config valid_languages
        if ($request->hasHeader('lang') && in_array($request->header('lang'), config('app.valid_locals'))) {
            // $local = $this->mapAcceptLanguageToLocale($request->header('Accept-Language'));
            $local = ($request->header('lang'));
        }

        //set laravel localization
        app()->setLocale($local);
        return $next($request);
    }

    private function mapAcceptLanguageToLocale(string $lang)
    {
        $map = [
            "en"  => "en",
            "ar"  => "ar",
            "ckb" => "ku",
        ];

        return $map[$lang];
    }
}
