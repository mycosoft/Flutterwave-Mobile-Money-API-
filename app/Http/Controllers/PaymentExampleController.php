<?php

namespace App\Http\Controllers;

use App\Services\FlutterwaveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentExampleController extends Controller
{
    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    /**
     * Show payment form
     */
    public function showPaymentForm()
    {
        return view('payments.form');
    }

    /**
     * Process a payment request
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function processPayment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string',
        ]);

        // Generate a unique reference
        $reference = 'FLW_' . Str::random(16);

        // Log payment attempt
        Log::info('Payment attempt', [
            'method' => $request->payment_method,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'email' => $request->email,
        ]);

        try {
            // Prepare payment data
            $data = [
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'country' => $request->country ?? 'NG',
                'email' => $request->email,
                'phone' => $request->phone,
                'name' => $request->name,
                'redirect_url' => route('payment.callback'),
                'meta' => [
                    'order_id' => 'ORDER-' . time(),
                    'user_id' => 'guest-' . time(),
                ],
            ];

            // Add mobile money specific data if applicable
            if ($request->payment_method === 'mobilemoney') {
                $data['network'] = $request->network ?? '';
                $data['voucher'] = $request->voucher ?? '';
            }

            // Initialize payment
            $response = $this->flutterwaveService->initializePayment($data);

            if ($response['status'] === 'success') {
                // Check if it's a direct charge that requires redirect to our instructions page
                if (isset($response['data']['redirect_url'])) {
                    return redirect()->away($response['data']['redirect_url']);
                }
                
                // Otherwise, redirect to Flutterwave checkout page
                return redirect()->away($response['data']['authorization_url']);
            }

            return redirect()->route('payment.form')->with('error', 'Payment initialization failed: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Payment initialization error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('payment.form')->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Initialize a payment transaction
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function initializePayment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|string',
        ]);

        // Log payment attempt
        Log::info('Payment initialization attempt', [
            'method' => $request->payment_method,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'email' => $request->email,
        ]);

        try {
            // Format phone number for Uganda
            $phone = $request->phone;
            if ($request->country === 'UG' && substr($phone, 0, 1) === '0') {
                // Convert 07XXXXXXXX to 2567XXXXXXXX
                $phone = '256' . substr($phone, 1);
            }

            // Prepare payment data
            $data = [
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'country' => $request->country ?? 'NG',
                'email' => $request->email,
                'phone' => $phone,
                'name' => $request->name,
                'redirect_url' => route('payment.callback'),
                'meta' => [
                    'order_id' => 'ORDER-' . time(),
                    'user_id' => 'guest-' . time(),
                ],
            ];

            // Add mobile money specific data if applicable
            if ($request->payment_method === 'mobilemoney') {
                $data['network'] = $request->network ?? '';
                $data['voucher'] = $request->voucher ?? '';
            }

            // Initialize payment
            $response = $this->flutterwaveService->initializePayment($data);

            if ($response['status'] === 'success') {
                // Check if it's a direct charge that requires Flutterwave verification
                if (isset($response['is_direct_charge']) && $response['is_direct_charge']) {
                    // If it requires Flutterwave verification (captcha, OTP, etc.)
                    if (isset($response['requires_verification']) && $response['requires_verification'] && isset($response['data']['link'])) {
                        // Redirect to Flutterwave's verification page
                        return redirect()->away($response['data']['link']);
                    }
                    
                    // If it's a direct PIN entry on phone (no verification)
                    return redirect()->route('payment.mobile-money-instructions', [
                        'reference' => $response['data']['reference'],
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'phone' => $phone,
                        'network' => $request->network ?? 'Mobile Money',
                        'instructions' => $response['data']['instructions'] ?? 'Please check your phone to complete the payment',
                    ]);
                }
                
                // Otherwise, redirect to Flutterwave checkout page
                return redirect()->away($response['data']['authorization_url']);
            }

            return redirect()->route('payment.form')->with('error', 'Payment initialization failed: ' . ($response['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('Payment initialization error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('payment.form')->with('error', 'Payment initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment callback
     */
    public function handleCallback(Request $request)
    {
        try {
            // Check if we have a transaction reference from direct charge
            if (session()->has('payment_reference')) {
                $reference = session('payment_reference');
                session()->forget('payment_reference');
            } else {
                // For standard flow, get reference from Flutterwave response
                $reference = $request->tx_ref;
            }

            if (!$reference) {
                return redirect()->route('payment.form')->with('error', 'No transaction reference found');
            }

            // Verify the payment
            $verification = $this->flutterwaveService->verifyPayment($reference);

            if ($verification['status'] === 'success') {
                // Payment successful
                return view('payments.success', [
                    'transaction' => $verification['data'],
                ]);
            } else {
                // Payment failed
                return redirect()->route('payment.form')->with('error', 'Payment verification failed: ' . ($verification['message'] ?? 'Unknown error'));
            }

        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('payment.form')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle the mobile money payment instructions page
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function mobileMoneyInstructions(Request $request)
    {
        // Validate the request
        $request->validate([
            'reference' => 'required|string',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'phone' => 'required|string',
            'network' => 'required|string',
        ]);
        
        // Get the instructions from the request or use a default
        $instructions = $request->input('instructions', 'Please check your phone for a payment prompt and enter your PIN to complete the transaction.');
        
        // Pass the data to the view
        return view('payments.mobile_money_instructions', [
            'reference' => $request->input('reference'),
            'amount' => $request->input('amount'),
            'currency' => $request->input('currency'),
            'phone' => $request->input('phone'),
            'network' => $request->input('network'),
            'instructions' => $instructions,
        ]);
    }
    
    /**
     * Check the payment status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkPaymentStatus(Request $request)
    {
        // Validate the request
        $request->validate([
            'reference' => 'required|string',
        ]);
        
        $reference = $request->input('reference');
        
        try {
            // Get the payment service
            $flutterwaveService = app(FlutterwaveService::class);
            
            // Verify the transaction
            $result = $flutterwaveService->verifyTransaction($reference);
            
            // Check if the transaction was successful
            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment was successful',
                    'data' => $result['data']
                ]);
            }
            
            // If the transaction is still pending
            if ($result['status'] === 'pending') {
                return response()->json([
                    'status' => 'pending',
                    'message' => 'Payment is still pending',
                ]);
            }
            
            // If the transaction failed
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment failed: ' . ($result['message'] ?? 'Unknown error'),
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error checking payment status', [
                'reference' => $reference,
                'error' => $e->getMessage(),
            ]);
            
            // Return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking payment status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
