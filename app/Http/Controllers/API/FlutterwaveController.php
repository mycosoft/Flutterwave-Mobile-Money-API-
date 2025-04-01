<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FlutterwaveService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FlutterwaveController extends Controller
{
    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    /**
     * Initialize a payment transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initialize(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'required|string',
            'payment_method' => 'required|string|in:card,mobilemoney,bank_transfer',
            'currency' => 'required|string|size:3',
            'redirect_url' => 'required|url',
            'meta' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $response = $this->flutterwaveService->initializePayment($request->all());
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Flutterwave initialization error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Payment initialization failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify a payment transaction
     *
     * @param string $reference
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify($reference)
    {
        try {
            $response = $this->flutterwaveService->verifyPayment($reference);
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Flutterwave verification error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Payment verification failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle webhook notifications from Flutterwave
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        // Verify webhook signature if available
        $signature = $request->header('verif-hash');
        if (!$this->flutterwaveService->verifyWebhookSignature($signature)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook signature'], 401);
        }

        try {
            $payload = $request->all();
            Log::info('Flutterwave webhook received', ['payload' => $payload]);
            
            // Process the webhook based on event type
            $event = $payload['event'] ?? null;
            $data = $payload['data'] ?? null;
            
            if ($event && $data) {
                $this->flutterwaveService->processWebhook($event, $data);
                return response()->json(['status' => 'success', 'message' => 'Webhook processed successfully']);
            }
            
            return response()->json(['status' => 'error', 'message' => 'Invalid webhook payload'], 400);
        } catch (\Exception $e) {
            Log::error('Flutterwave webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Webhook processing failed'], 500);
        }
    }
}
