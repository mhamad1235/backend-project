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
public function samiParkFromOSM($code): JsonResponse
{
     $res = GeminiTable::where('code_chat', $code)->first();
    $data = $res->data;

$results = [];

foreach ($data['days'] as $day) {
    $dayResult = [
        'day' => $day['day'],
        'rows' => []
    ];

    foreach ($day['rows'] as $row) {
        $query = $row['location'];

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'MyApp/1.0 (mhamadsalliim@gmail.com)',
                'Accept'     => 'application/json',
            ])->timeout(10)->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $query,
                'format' => 'json',
                'limit'  => 1,
                'email'  => 'mhamadsalliim@gmail.com',
            ]);

            $lat = null;
            $lon = null;

            if ($response->ok()) {
                $json = $response->json();

                if (!empty($json) && isset($json[0]['lat'], $json[0]['lon'])) {
                    $lat = (float) $json[0]['lat'];
                    $lon = (float) $json[0]['lon'];
                }
            } else {
                Log::warning("Nominatim error", [
                    'location' => $query,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            $dayResult['rows'][] = [
                ...$row,
                'latitude' => $lat,
                'longitude' => $lon
            ];

        } catch (\Throwable $e) {
            Log::error("Nominatim failed for $query: " . $e->getMessage());

            $dayResult['rows'][] = [
                ...$row,
                'latitude' => null,
                'longitude' => null
            ];
        }
    }

    $results[] = $dayResult;
}

return response()->json([
    'days' => $results
]);

}

}
