<?php

if (!function_exists('setAppLocale')) {
    function setAppLocale($request)
    {
        $locale = $request->header('lang', 'en');
        app()->setLocale($locale);
    }
}
 
