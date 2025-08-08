<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FIBPaymentService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.fib.base_url');
        $this->clientId = config('services.fib.client_id');
        $this->clientSecret = config('services.fib.client_secret');
    }

    public function getToken(): string|null
    {
        $response = Http::asForm()->post("{$this->baseUrl}/auth/realms/fib-online-shop/protocol/openid-connect/token", [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);

        return $response->json()['access_token'] ?? null;
    }

    public function createPayment(array $data): array
    {
        $token = $this->getToken();

        $response = Http::withToken($token)->post("{$this->baseUrl}/protected/v1/payments", $data);

        return $response->json();
    }

    public function refundPayment(string $paymentId): array
    {
        $token = $this->getToken();

        $response = Http::withToken($token)->post("{$this->baseUrl}/protected/v1/payments/{$paymentId}/refund");

        return $response->json();
    }
}
