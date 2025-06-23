<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
class GlobalHelper
{
    public static function sendJson($data, $message = '', $status = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function generateRandomCode($length = 6)
    {
        return strtoupper(str_random($length));
    }
 public static function saveImage($file)
    {
     $path = Storage::disk('s3')->put('uploads', $file);
        $image = Image::create([
            'path' => $path
        ]);
        $url = Storage::disk('s3')->url($path);
        return $url;
    }
   
}
