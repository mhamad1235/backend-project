<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use FirstIraqiBank\FIBPaymentSDK\Services\FIBPaymentIntegrationService;
use Exception;


class FIBPaymentController extends Controller
{
        protected $paymentService;

    // Inject the FIBPaymentIntegrationService
    public function __construct(FIBPaymentIntegrationService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Create a payment using FIBPaymentIntegrationService
     */
    public function createPayment(Request $request)
    {
try {
    // Call the createPayment method of the FIBPaymentIntegrationService
    $response = $this->paymentService->createPayment(1000, 'http://localhost/callback', 'Your payment description', 'http://localhost/redirectUri');
   
    $paymentData = json_decode($response->getBody(), true);

    // Return a response with the payment details and structure it as per your need.
    if($response->successful()) {
        return response()->json([
            'message' => 'Payment created successfully!',
            'payment' => $paymentData,
        ]);
    }
} catch (Exception $e) {
    // Handle any errors that might occur.
    return response()->json([
        'message' => 'Error creating payment: ' . $e->getMessage()
    ], 500);
}
    }

    public function handleCallback(Request $request)
{
    $payload = $request->all();

    $paymentId = $payload['id'] ?? null;
    $status = $payload['status'] ?? null;

    if (!$paymentId || !$status) {
        return response()->json(['error' => 'Invalid callback payload'], 400);
    }

    try {
        // Implement your callback handling logic
        return response()->json(['message' => 'Callback processed successfully']);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to process callback: ' . $e->getMessage()], 500);
    }
}
}