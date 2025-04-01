<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FlutterwaveService
{
    protected $baseUrl;
    protected $publicKey;
    protected $secretKey;
    protected $encryptionKey;
    protected $webhookSecret;
    protected $environment;

    public function __construct()
    {
        $this->baseUrl = 'https://api.flutterwave.com/v3';
        $this->publicKey = config('flutterwave.public_key');
        $this->secretKey = config('flutterwave.secret_key');
        $this->encryptionKey = config('flutterwave.encryption_key');
        $this->webhookSecret = config('flutterwave.webhook_secret');
        $this->environment = config('flutterwave.environment', 'sandbox');
    }

    /**
     * Initialize a payment transaction
     * 
     * @param array $data
     * @return array
     */
    public function initializePayment(array $data)
    {
        // Generate a unique reference
        $reference = $this->generateReference();
        
        // Check if this is a mobile money payment in live mode
        if ($this->environment === 'live' && $data['payment_method'] === 'mobilemoney') {
            // For mobile money in live mode, use direct charge
            $result = $this->initializeMobileMoneyDirectCharge($data, $reference);
            
            // If this is a direct charge
            if (isset($result['is_direct_charge']) && $result['is_direct_charge']) {
                // Return the response as is - the controller will handle the appropriate redirect
                return $result;
            }
            
            return $result;
        }
        
        // For other payment methods or in sandbox mode, use standard payment page
        $payload = [
            'tx_ref' => $reference,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'payment_options' => $data['payment_method'],
            'redirect_url' => $data['redirect_url'],
            'customer' => [
                'email' => $data['email'],
                'name' => $data['name'],
                'phone_number' => $data['phone'] ?? '',
            ],
            'meta' => $data['meta'] ?? [],
            'customizations' => [
                'title' => $data['title'] ?? config('app.name', 'Laravel'),
                'description' => $data['description'] ?? 'Payment for products/services',
                'logo' => $data['logo'] ?? config('flutterwave.logo_url', ''),
            ],
        ];
        
        // Log the payload for debugging
        Log::info('Flutterwave Payment Initialization Payload', ['payload' => $payload]);
        
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/payments', $payload);
        
        if ($response->successful()) {
            $result = $response->json();
            
            // Log the response for debugging
            Log::info('Flutterwave Payment Initialization Response', ['response' => $result]);
            
            return [
                'status' => 'success',
                'message' => 'Payment initialization successful',
                'data' => [
                    'reference' => $reference,
                    'authorization_url' => $result['data']['link'],
                ],
            ];
        }
        
        // Log the error for debugging
        Log::error('Flutterwave Payment Initialization Error', [
            'status' => $response->status(),
            'response' => $response->json(),
            'payload' => $payload
        ]);
        
        throw new \Exception('Payment initialization failed: ' . ($response->json()['message'] ?? 'Unknown error'));
    }
    
    /**
     * Initialize a direct mobile money charge (no redirect to Flutterwave page)
     * 
     * @param array $data
     * @param string $reference
     * @return array
     */
    protected function initializeMobileMoneyDirectCharge(array $data, string $reference)
    {
        $network = $data['network'] ?? '';
        $country = $data['country'] ?? 'GH'; // Default to Ghana
        
        // Get country code and payment details
        $countryInfo = $this->getMobileMoneyInfo($country, $network);
        
        // For Uganda, use a completely different payload structure
        if ($countryInfo['code'] === 'UG') {
            // Build Uganda-specific payload exactly as in Flutterwave docs
            $payload = [
                'tx_ref' => $reference,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'email' => $data['email'],
                'phone_number' => $data['phone'],
                'fullname' => $data['name'],
                'redirect_url' => $data['redirect_url'],
                'network' => $countryInfo['network'],
            ];
            
            // Add meta data if provided
            if (!empty($data['meta'])) {
                $payload['meta'] = $data['meta'];
            }
            
            // Log the payload for debugging
            Log::info('Uganda Mobile Money Charge Payload', [
                'payload' => $payload,
                'country' => $countryInfo['code'],
                'network' => $countryInfo['network']
            ]);
            
            // Use the specific Uganda mobile money endpoint
            $endpoint = $this->baseUrl . '/charges?type=mobile_money_uganda';
            
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, $payload);
            
            // Log the response for debugging
            Log::info('Uganda Mobile Money Charge Response', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->json(),
                'payload' => $payload
            ]);
            
            if ($response->successful()) {
                $result = $response->json();
                
                // For direct charges, we need to check if further action is required
                if (isset($result['meta']['authorization']['mode'])) {
                    // If Flutterwave requires a redirect for verification
                    if ($result['meta']['authorization']['mode'] === 'redirect' && 
                        isset($result['meta']['authorization']['redirect'])) {
                        
                        // Return the response with the Flutterwave redirect URL
                        return [
                            'status' => 'success',
                            'message' => 'Mobile money charge initiated',
                            'is_direct_charge' => true,
                            'requires_verification' => true,
                            'data' => [
                                'link' => $result['meta']['authorization']['redirect'],
                                'reference' => $reference,
                                'instructions' => 'Please complete the verification process to proceed with your payment.',
                            ],
                        ];
                    }
                    
                    // If it requires PIN entry directly on the phone
                    if ($result['meta']['authorization']['mode'] === 'pin') {
                        // Return the response with a special flag indicating direct charge
                        return [
                            'status' => 'success',
                            'message' => 'Mobile money charge initiated',
                            'is_direct_charge' => true,
                            'requires_verification' => false,
                            'data' => [
                                'reference' => $reference,
                                'instructions' => $result['meta']['authorization']['note'] ?? 'Please check your phone to complete the payment',
                            ],
                        ];
                    }
                }
                
                return $result;
            }
            
            Log::error('Flutterwave Uganda Mobile Money API Error', [
                'status' => $response->status(),
                'response' => $response->json(),
                'payload' => $payload
            ]);
            
            throw new \Exception('Mobile money charge failed: ' . ($response->json()['message'] ?? 'Unknown error'));
        }
        
        // For other countries, use the standard approach
        // Build the base payload
        $payload = [
            'tx_ref' => $reference,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'email' => $data['email'],
            'phone_number' => $data['phone'],
            'fullname' => $data['name'],
            'redirect_url' => $data['redirect_url'],
            'meta' => $data['meta'] ?? [],
        ];
        
        // Add country-specific parameters
        if ($countryInfo['code'] === 'GH') {
            // For Ghana
            $payload['payment_type'] = $countryInfo['payment_type'];
            $payload['network'] = $countryInfo['network'];
            
            // Add voucher for Vodafone Ghana
            if ($countryInfo['network'] === 'VODAFONE' && isset($data['voucher'])) {
                $payload['voucher'] = $data['voucher'];
            }
        } else {
            // For other countries
            $payload['payment_type'] = $countryInfo['payment_type'];
            if (!empty($countryInfo['network'])) {
                $payload['network'] = $countryInfo['network'];
            }
        }
        
        // Log the payload for debugging
        Log::info('Mobile Money Direct Charge Payload', [
            'payload' => $payload,
            'country_code' => $countryInfo['code'],
            'payment_type' => $countryInfo['payment_type']
        ]);
        
        // Use standard charges endpoint
        $endpoint = $this->baseUrl . '/charges';
        
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);
        
        // Log the response for debugging
        Log::info('Mobile Money Direct Charge Response', [
            'endpoint' => $endpoint,
            'status' => $response->status(),
            'body' => $response->json(),
            'payload' => $payload
        ]);
        
        if ($response->successful()) {
            $result = $response->json();
            
            // For direct charges, we need to check if further action is required
            if (isset($result['meta']['authorization']['mode'])) {
                if ($result['meta']['authorization']['mode'] === 'pin' || 
                    $result['meta']['authorization']['mode'] === 'redirect') {
                    
                    // Return the response with a special flag indicating direct charge
                    return [
                        'status' => 'success',
                        'message' => 'Mobile money charge initiated',
                        'is_direct_charge' => true,
                        'data' => [
                            'link' => $result['meta']['authorization']['redirect'] ?? null,
                            'reference' => $reference,
                            'instructions' => $result['meta']['authorization']['note'] ?? 'Please check your phone to complete the payment',
                        ],
                    ];
                }
            }
            
            return $result;
        }
        
        Log::error('Flutterwave Direct Charge API Error', [
            'status' => $response->status(),
            'response' => $response->json(),
            'payload' => $payload
        ]);
        
        throw new \Exception('Mobile money charge failed: ' . ($response->json()['message'] ?? 'Unknown error'));
    }
    
    /**
     * Get mobile money information for a country and network
     * 
     * @param string $country
     * @param string $network
     * @return array
     */
    protected function getMobileMoneyInfo(string $country, string $network): array
    {
        $country = strtoupper($country);
        $network = strtoupper($network);
        
        // Default values
        $result = [
            'code' => 'GH',
            'payment_type' => 'mobilemoneygh',
            'network' => 'MTN'
        ];
        
        // Set country-specific values
        switch ($country) {
            case 'GH':
                $result['code'] = 'GH';
                $result['payment_type'] = 'mobilemoneygh';
                
                // Set network-specific values for Ghana
                if ($network === 'VODAFONE') {
                    $result['network'] = 'VODAFONE';
                } elseif ($network === 'AIRTEL' || $network === 'TIGO') {
                    $result['network'] = 'AIRTEL-TIGO';
                } else {
                    $result['network'] = 'MTN';
                }
                break;
                
            case 'KE':
                $result['code'] = 'KE';
                $result['payment_type'] = 'mpesa';
                $result['network'] = ''; // Not required for M-Pesa
                break;
                
            case 'UG':
                $result['code'] = 'UG';
                // According to Flutterwave docs, use "mobilemoneyuganda" (no underscores)
                $result['payment_type'] = 'mobilemoneyuganda';
                
                if ($network === 'AIRTEL') {
                    $result['network'] = 'AIRTEL';
                } else {
                    $result['network'] = 'MTN';
                }
                break;
                
            case 'RW':
                $result['code'] = 'RW';
                $result['payment_type'] = 'mobilemoneyrwanda';
                $result['network'] = 'MTN';
                break;
                
            case 'ZM':
                $result['code'] = 'ZM';
                $result['payment_type'] = 'mobilemoneyzambia';
                $result['network'] = 'MTN';
                break;
                
            case 'TZ':
                $result['code'] = 'TZ';
                $result['payment_type'] = 'mobilemoneytanzania';
                
                if ($network === 'VODACOM') {
                    $result['network'] = 'VODACOM';
                } else {
                    $result['network'] = 'MPESA';
                }
                break;
        }
        
        return $result;
    }

    /**
     * Generate a unique reference for the transaction
     * 
     * @return string
     */
    protected function generateReference()
    {
        return 'FLW_' . \Illuminate\Support\Str::random(16);
    }

    /**
     * Verify a payment transaction
     *
     * @param string $reference
     * @return array
     */
    public function verifyPayment(string $reference)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/transactions/verify_by_reference?tx_ref=' . $reference);

        if ($response->successful()) {
            $data = $response->json();
            
            // Check if the payment was successful
            if ($data['status'] === 'success' && isset($data['data']) && $data['data']['status'] === 'successful') {
                // You can add additional verification logic here
                return [
                    'status' => 'success',
                    'message' => 'Payment verified successfully',
                    'data' => $data['data'],
                ];
            }
            
            return [
                'status' => 'failed',
                'message' => 'Payment verification failed',
                'data' => $data['data'] ?? null,
            ];
        }

        Log::error('Flutterwave Verification API Error', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);

        throw new \Exception('Payment verification failed: ' . ($response->json()['message'] ?? 'Unknown error'));
    }

    /**
     * Verify webhook signature
     *
     * @param string|null $signature
     * @return bool
     */
    public function verifyWebhookSignature(?string $signature): bool
    {
        // For testing purposes, if webhook secret is empty, return true
        if (empty($this->webhookSecret)) {
            return true;
        }

        if (empty($signature)) {
            return false;
        }

        return $signature === $this->webhookSecret;
    }

    /**
     * Process webhook notification
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public function processWebhook(string $event, array $data): void
    {
        // Handle different event types
        switch ($event) {
            case 'charge.completed':
                $this->handleCompletedCharge($data);
                break;
            case 'transfer.completed':
                $this->handleCompletedTransfer($data);
                break;
            case 'payment.failed':
                $this->handleFailedPayment($data);
                break;
            default:
                Log::info('Unhandled Flutterwave webhook event', ['event' => $event, 'data' => $data]);
                break;
        }
    }

    /**
     * Handle completed charge event
     *
     * @param array $data
     * @return void
     */
    protected function handleCompletedCharge(array $data): void
    {
        // Implement your business logic for completed charges
        // For example, update order status, send confirmation email, etc.
        Log::info('Payment completed', ['data' => $data]);
        
        // You can dispatch events or jobs here to handle the payment asynchronously
        // event(new PaymentCompleted($data));
    }

    /**
     * Handle completed transfer event
     *
     * @param array $data
     * @return void
     */
    protected function handleCompletedTransfer(array $data): void
    {
        // Implement your business logic for completed transfers
        Log::info('Transfer completed', ['data' => $data]);
        
        // You can dispatch events or jobs here
        // event(new TransferCompleted($data));
    }

    /**
     * Handle failed payment event
     *
     * @param array $data
     * @return void
     */
    protected function handleFailedPayment(array $data): void
    {
        // Implement your business logic for failed payments
        Log::info('Payment failed', ['data' => $data]);
        
        // You can dispatch events or jobs here
        // event(new PaymentFailed($data));
    }
}
