<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function chat($messages, $model = 'gpt-4')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
        ])->post($this->baseUrl . 'chat/completions', [
            'model'    => $model,
            'messages' => $messages,
        ]);

        return $response->json();
    }
}
