<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FIBPaymentService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $authUrl="https://fib.stage.fib.iq/auth/realms/fib-online-shop/protocol/openid-connect/token";
    protected string $payoutUrl="https://fib.stage.fib.iq/protected/v1/payouts";
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
     public function getAccessToken(): string
    {
        $response = Http::asForm()->post($this->authUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => "stage-mpit-payout",
            'client_secret' => "38e2d012-2403-435a-a9ce-882e33da9bdb",
        ]);

        if ($response->failed()) {
            throw new \Exception("Failed to get FIB access token: " . $response->body());
        }

        return $response->json()['access_token'];
    }
      public function getPayment(string $paymentId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/protected/v1/payments/{$paymentId}");

        if ($response->failed()) {
            throw new \Exception("Failed to fetch payment: " . $response->body());
        }

        return $response->json();
    }
    /**
     * Create a payout
     */
     public function createPayout(float $amount, string $currency, string $iban, string $description): array
    {
        $token = $this->getAccessToken();

        $payload = [
            "amount" => [
                "amount" => $amount,
                "currency" => $currency
            ],
            "targetAccountIban" => $iban,
            "description" => $description
        ];

        $response = Http::withToken($token)
            ->post($this->payoutUrl, $payload);

        if ($response->failed()) {
            throw new \Exception("FIB payout failed: " . $response->body());
        }

        return $response->json();
    }

    /** ✅ Authorize payout (handles 204 or 200) */
    public function authorizePayout(string $payoutId): array
    {
        $token = $this->getAccessToken();

        $response = Http::withToken($token)
            ->withOptions(['http_errors' => false]) // prevent exceptions
            ->post("{$this->payoutUrl}/{$payoutId}/authorize");

        $status = $response->status();
        $body = $response->body();

        return [
            'status_code' => $status,
            'body'        => strlen($body) ? $response->json() : null,
            'success'     => $status >= 200 && $status < 300,
        ];
    }
}
