# Flutterwave Mobile Money Payment API

This integration provides a reusable API for processing mobile money and credit card payments using Flutterwave in your Laravel projects.

![Mobile Money Payment API](https://flutterwave.com/images/banners/flutterwave-standard.png)

## Features

- Credit/Debit Card payments
- Mobile Money payments (MTN, Vodafone, Airtel, etc.)
- Bank Transfer payments
- Support for multiple countries (Uganda, Ghana, Kenya, Rwanda, Tanzania, Nigeria)
- Webhook handling for payment notifications
- Comprehensive error handling
- Beautiful, responsive UI with Bootstrap
- Easy to integrate into any Laravel project

## Setup Instructions

### 1. Environment Configuration

Add the following variables to your `.env` file:

```
FLUTTERWAVE_PUBLIC_KEY=your_public_key_here
FLUTTERWAVE_SECRET_KEY=your_secret_key_here
FLUTTERWAVE_ENCRYPTION_KEY=your_encryption_key_here
FLUTTERWAVE_WEBHOOK_SECRET=your_webhook_hash_here
FLUTTERWAVE_ENVIRONMENT=sandbox  # Change to 'live' for production
FLUTTERWAVE_LOGO_URL=https://your-website.com/logo.png  # Optional
```

### 2. Register Webhook URL

In your Flutterwave dashboard, set up a webhook URL that points to:
```
https://your-domain.com/api/flutterwave/webhook
```

## Usage Examples

### Basic Payment Initialization

```php
use App\Services\FlutterwaveService;

class YourController extends Controller
{
    protected $flutterwaveService;

    public function __construct(FlutterwaveService $flutterwaveService)
    {
        $this->flutterwaveService = $flutterwaveService;
    }

    public function makePayment(Request $request)
    {
        $paymentData = [
            'amount' => 100.00,
            'email' => 'customer@example.com',
            'name' => 'John Doe',
            'phone' => '0123456789',
            'payment_method' => 'card', // 'card', 'mobilemoney', or 'bank_transfer'
            'currency' => 'USD',
            'redirect_url' => route('payment.callback'),
            'meta' => [
                'order_id' => 'ORDER-123',
                'user_id' => auth()->id(),
            ],
        ];

        $response = $this->flutterwaveService->initializePayment($paymentData);
        
        if ($response['status'] === 'success') {
            return redirect($response['data']['link']);
        }
        
        return back()->with('error', 'Payment initialization failed');
    }
}
```

### Mobile Money Payment

For mobile money payments, include additional parameters:

```php
$paymentData = [
    // ... basic payment data
    'payment_method' => 'mobilemoney',
    'currency' => 'UGX', // Currency code for the country
    'network' => 'AIRTEL', // Mobile network provider (MTN or AIRTEL for Uganda)
    'country' => 'UG', // Country code
];
```

> **Important Note for Uganda Mobile Money:**
> - Phone number must be in international format with country code 256
> - Network must be either 'MTN' or 'AIRTEL'
> - Currency should be 'UGX'

### Verifying Payments

```php
public function verifyPayment($reference)
{
    $verification = $this->flutterwaveService->verifyPayment($reference);
    
    if ($verification['status'] === 'success') {
        // Payment successful, update your database
        return view('payment.success', ['transaction' => $verification['data']]);
    }
    
    return view('payment.failed');
}
```

## Screenshots

### Payment Form
![Payment Form](screenshots/payment_form.png)

### Payment Options
![Payment Options](screenshots/payment_options.png)

### OTP Verification
![OTP Verification](screenshots/otp_verification.png)

## API Documentation

### FlutterwaveService Methods

#### `initializePayment(array $data)`

Initializes a payment transaction.

Parameters:
- `amount`: (required) Amount to charge
- `email`: (required) Customer's email
- `name`: (required) Customer's name
- `phone`: (required) Customer's phone number
- `payment_method`: (required) Payment method ('card', 'mobilemoney', 'bank_transfer')
- `currency`: (required) 3-letter currency code
- `redirect_url`: (required) URL to redirect after payment
- `meta`: (optional) Additional metadata
- `network`: (optional) Mobile network for mobile money
- `country`: (optional) Country code for mobile money

Returns:
- Array containing payment link and transaction reference

#### `verifyPayment(string $reference)`

Verifies a payment transaction.

Parameters:
- `reference`: Transaction reference

Returns:
- Array containing transaction status and details

#### `verifyWebhookSignature(string $signature)`

Verifies the webhook signature from Flutterwave.

Parameters:
- `signature`: Signature from request header

Returns:
- Boolean indicating if signature is valid

#### `processWebhook(string $event, array $data)`

Processes webhook notifications.

Parameters:
- `event`: Event type
- `data`: Event data

## Webhook Events

The integration handles the following webhook events:
- `charge.completed`: When a payment is completed
- `transfer.completed`: When a transfer is completed
- `payment.failed`: When a payment fails

## Example Implementation

This package includes an example implementation in:
- `PaymentExampleController.php`
- `resources/views/payments/form.blade.php`
- `resources/views/welcome.blade.php`
- `resources/views/payments/success.blade.php`
- `resources/views/payments/failed.blade.php`

## Security Considerations

- Never expose your secret key or encryption key
- Always verify payments server-side
- Validate webhook signatures
- Use HTTPS for all API calls

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Developed By

Mycosoft Technologies

### Contact Information

- **Phone:** +256 750501151 or +256 781779477
- **Email:** mycosoftofficial@gmail.com
- **Website:** [mycosofttechnologies.com](https://mycosofttechnologies.com)
