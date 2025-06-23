<?php

if (!function_exists('setAppLocale')) {
    function setAppLocale($request)
    {
        $locale = $request->header('lang', 'en');
        app()->setLocale($locale);
    }
}

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

if (!function_exists('save_file_to_s3')) {
    /**
     * Save uploaded file to S3 with optional prefix.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $prefix
     * @return string Saved file path in S3
     */
    function save_file_to_s3(UploadedFile $file, string $prefix = 'uploads/'): string
    {
        $prefix = rtrim($prefix, '/') . '/';
        
        return Storage::put($prefix, $file);
    }
}
if (!function_exists('s3_url')) {
    function s3_url(string $path): string
    {
        return Storage::disk('s3')->url($path);
    }
    
}

   function returnPAth()
    {
        return "test again" ;
        
    }