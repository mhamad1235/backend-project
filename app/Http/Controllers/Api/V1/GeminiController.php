<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeminiTable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Jobs\GenerateGeminiTravelPlan;
use App\Jobs\GenerateLocation;
use Illuminate\Support\Str;


class GeminiController extends Controller
{
     function generateUniqueCode($length = 12)
    {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#';

    // Shuffle and generate
    $code = '';
    $maxIndex = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $index = random_int(0, $maxIndex);
        $code .= $characters[$index];
    }

    return $code;
}
    public function requestPlan($city,$day){
   
    $code = $this->generateUniqueCode(16);
    GenerateGeminiTravelPlan::dispatch($code,$city,$day);

    return response()->json([
        'status' => 'queued',
        'message' => 'Your travel plan is being generated. Please check back shortly.',
        'code_chat'=>$code
    ]);
    }
    public function requestLocation($code){
    $code = $code;
    GenerateLocation::dispatch($code);

    return response()->json([
        'status' => 'wait',
        'message' => 'Your travel plan is being generated. Please check back shortly.',
        'code_chat'=>$code
    ]);
    }

    public function geminiData($code)
{
    $data = GeminiTable::where('code_chat', $code)->first();

    if (!$data) {
        return response()->json(['error' => 'Data not found'], 404);
    }

    return response()->json($data);
}
public function fetchGeminiData($code)
{
    $res = GeminiTable::where('code_chat', $code)->first();

    if (!$res) {
        return response()->json(['error' => 'Data not found'], 404);
    }

    // Decode JSON if needed
    $data = is_string($res->data) ? json_decode($res->data, true) : $res->data;

    if (!is_array($data) || !isset($data['days'])) {
        return response()->json(['error' => 'Invalid data format'], 400);
    }

    return response()->json($data);
}

public function getLocation($code)
{
    // Validate input
    $res = GeminiTable::where('code_chat', $code)->first();

    if (! $res) {
        return response()->json(['error' => 'Data not found'], 404);
    }

    $data = is_string($res->data) ? json_decode($res->data, true) : $res->data;
    if (! is_array($data) || ! isset($data['days'])) {
        return response()->json(['error' => 'Invalid data format'], 400);
    }

    // collect unique, trimmed locations
    $uniqueLocations = [];
    foreach ($data['days'] as $day) {
        foreach (($day['rows'] ?? []) as $row) {
            $loc = trim($row['location'] ?? '');
            if ($loc !== '' && ! in_array($loc, $uniqueLocations, true)) {
                $uniqueLocations[] = $loc;
            }
        }
    }

    $results = [];
    foreach ($uniqueLocations as $location) {
      
        $normalized = $location;
     

        $cacheKey = 'geo_' . md5($normalized);
        $cached = Cache::get($cacheKey, null);

        // If previously cached with coordinates (or cached null marker), use it
        if ($cached !== null && is_array($cached) && array_key_exists('lat', $cached) && array_key_exists('lon', $cached)) {
            $results[] = [
                'location'  => $location,
                'latitude'  => $cached['lat'],
                'longitude' => $cached['lon'],
                'found'     => ($cached['lat'] !== null && $cached['lon'] !== null)
            ];
            // still wait a bit to be polite between locations (optional)
            usleep(200000); // 0.2s
            continue;
        }

        // Sleep 1 second between uncached queries to respect Nominatim policy
        // (do this before the request to pace outbound queries)
        usleep(1000000); // 1,000,000 microseconds = 1 second

        try {
            // Retry on transient failures: 3 attempts, 500ms backoff
            $response = Http::withHeaders([
                'User-Agent' => 'MyApp/1.0 (mhamadsalliim@gmail.com)',
                'Accept'     => 'application/json',
            ])->timeout(12)
              ->retry(3, 500) // retries: attempt, wait 500ms, attempt, ...
              ->get('https://nominatim.openstreetmap.org/search', [
                  'q'      => $normalized,
                  'format' => 'json',
                  'limit'  => 1,
                  'email'  => 'mhamadsalliim@gmail.com',
              ]);

            if (! $response->ok()) {
                // Log and treat as not found for now (cache short negative)
                \Log::warning('Nominatim returned non-200', [
                    'q' => $normalized,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 1000),
                ]);

                // Cache a negative result briefly to avoid repeat hits
                Cache::put($cacheKey, ['lat' => null, 'lon' => null], now()->addHours(6));
                $results[] = ['location' => $location, 'latitude' => null, 'longitude' => null, 'found' => false];
                continue;
            }

            $json = $response->json();

            if (empty($json) || ! isset($json[0]['lat'], $json[0]['lon'])) {
                \Log::info('Nominatim returned empty for query', ['q' => $normalized]);
                // cache negative result for a short time
                Cache::put($cacheKey, ['lat' => null, 'lon' => null], now()->addHours(6));
                $results[] = ['location' => $location, 'latitude' => null, 'longitude' => null, 'found' => false];
                continue;
            }

            // success
            $lat = (float) $json[0]['lat'];
            $lon = (float) $json[0]['lon'];

            Cache::put($cacheKey, ['lat' => $lat, 'lon' => $lon], now()->addDays(30));
            $results[] = [
                'location'  => $location,
                'latitude'  => $lat,
                'longitude' => $lon,
                'found'     => true
            ];
        } catch (\Throwable $e) {
            // Log and continue to next location (do not abort entire endpoint)
            \Log::error('Nominatim exception', ['q' => $normalized, 'err' => $e->getMessage()]);
            // do not cache exceptions; mark not found
            $results[] = ['location' => $location, 'latitude' => null, 'longitude' => null, 'found' => false];
            // small pause before next attempt to be polite
            usleep(500000);
            continue;
        }
    }

    return response()->json(['locations' => $results]);
}



}
