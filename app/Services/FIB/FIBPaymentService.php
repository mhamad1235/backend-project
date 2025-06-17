<?php

namespace App\Services\FIB;

use FirstIraqiBank\FIBPaymentSDK\Services\FIBPaymentIntegrationService;
use Exception;

class FIBPaymentService
{
    protected $paymentService;

    public function __construct(FIBPaymentIntegrationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createPayment($amount, $callbackUrl, $description, $redirectUri)
    {
        try {
            $response = $this->paymentService->createPayment($amount, $callbackUrl, $description, $redirectUri);
            $data = json_decode($response->getBody(), true);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $data
                ];
            }

            return [
                'success' => false,
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkStatus($paymentId)
    {
        try {
            $response = $this->paymentService->checkPaymentStatus($paymentId);
            $data = json_decode($response->getBody(), true);

            return [
                'success' => $response->successful(),
                'data' => $data
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function refund($paymentId)
    {
        try {
            $response = $this->paymentService->refund($paymentId);
            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function cancel($paymentId)
    {
        try {
            $response = $this->paymentService->cancelPayment($paymentId);

            return [
                'success' => in_array($response->getStatusCode(), [200, 201, 202, 204]),
                'data' => json_decode($response->getBody(), true)
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}