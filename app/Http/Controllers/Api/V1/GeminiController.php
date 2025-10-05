<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeminiTable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;

class GeminiController extends Controller
{
    
    public function geminiData($code)
{
    $data = GeminiTable::where('code_chat', $code)->first();

    if (!$data) {
        return response()->json(['error' => 'Data not found'], 404);
    }

    return response()->json($data);
}
public function samiParkFromOSM(): JsonResponse
{
    $response = Http::withHeaders([
        'User-Agent' => 'MyApp/1.0 (mhamadsalliim@gmail.com)',
        'Host' => 'nominatim.openstreetmap.org',  // explicit Host header
        'Accept' => 'application/json',
    ])->get('https://nominatim.openstreetmap.org/search', [
        'q' => 'Sami Abdulrahman Park, Erbil',
        'format' => 'json',
        'limit' => 1,
        'email' => 'mhamadsalliim@gmail.com',
    ]);

    if ($response->failed()) {
        return response()->json(['error' => 'Failed to get data from Nominatim'], 502);
    }

    return response()->json($response->json());
}

}
