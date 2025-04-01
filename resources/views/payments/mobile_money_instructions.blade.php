<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .instructions-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .instructions-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .payment-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .payment-details p {
            margin-bottom: 10px;
        }
        .payment-details strong {
            color: #0d6efd;
        }
        .instructions-list {
            margin-bottom: 30px;
        }
        .instructions-list ol {
            padding-left: 20px;
        }
        .instructions-list li {
            margin-bottom: 10px;
        }
        .status-container {
            text-align: center;
            margin-top: 20px;
        }
        .status-message {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .pin-entry-container {
            background-color: #e9f7fe;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #0d6efd;
        }
        .pin-instructions {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .timer {
            font-size: 18px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="instructions-container">
            <h2 class="instructions-title">Mobile Money Payment</h2>
            
            <div class="payment-details">
                <p><strong>Amount:</strong> {{ $currency }} {{ number_format($amount, 2) }}</p>
                <p><strong>Phone Number:</strong> {{ $phone }}</p>
                <p><strong>Network:</strong> {{ $network }}</p>
                <p><strong>Reference:</strong> {{ $reference }}</p>
            </div>
            
            <div class="pin-entry-container">
                <p class="pin-instructions">Please enter your mobile money PIN on your phone now:</p>
                <p>You should receive a prompt on your phone to authorize this payment. Enter your PIN to complete the transaction.</p>
                <div class="timer" id="countdown">2:00</div>
            </div>
            
            <div class="instructions-list">
                <h4>Instructions:</h4>
                <ol>
                    <li>Check your phone for a payment authorization request from {{ $network }}.</li>
                    <li>Enter your mobile money PIN to authorize the payment.</li>
                    <li>Once completed, the page will automatically update with your payment status.</li>
                    <li>Do not close this page until the payment is complete.</li>
                </ol>
                <p>{{ $instructions }}</p>
            </div>
            
            <div class="status-container">
                <div class="status-message" id="statusMessage">Waiting for payment confirmation...</div>
                <div class="spinner-border text-primary" role="status" id="loadingSpinner">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div id="paymentSuccess" style="display: none;">
                    <div class="alert alert-success">
                        <h4>Payment Successful!</h4>
                        <p>Your transaction has been completed successfully.</p>
                    </div>
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">Back to Payment Form</a>
                </div>
                <div id="paymentFailed" style="display: none;">
                    <div class="alert alert-danger">
                        <h4>Payment Failed</h4>
                        <p>There was an issue processing your payment. Please try again.</p>
                    </div>
                    <a href="{{ route('payment.form') }}" class="btn btn-primary">Try Again</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set up countdown timer
            let timeLeft = 120; // 2 minutes
            const countdownElement = document.getElementById('countdown');
            
            const countdownTimer = setInterval(function() {
                const minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                
                countdownElement.textContent = minutes + ':' + seconds;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    countdownElement.textContent = 'Time expired';
                }
                
                timeLeft--;
            }, 1000);
            
            // Check payment status periodically
            const reference = '{{ $reference }}';
            const statusMessage = document.getElementById('statusMessage');
            const loadingSpinner = document.getElementById('loadingSpinner');
            const paymentSuccess = document.getElementById('paymentSuccess');
            const paymentFailed = document.getElementById('paymentFailed');
            
            const checkPaymentStatus = function() {
                fetch('{{ route("payment.check-status") }}?reference=' + reference)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            clearInterval(statusChecker);
                            clearInterval(countdownTimer);
                            statusMessage.style.display = 'none';
                            loadingSpinner.style.display = 'none';
                            paymentSuccess.style.display = 'block';
                        } else if (data.status === 'failed') {
                            clearInterval(statusChecker);
                            clearInterval(countdownTimer);
                            statusMessage.style.display = 'none';
                            loadingSpinner.style.display = 'none';
                            paymentFailed.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error checking payment status:', error);
                    });
            };
            
            // Check status every 5 seconds
            const statusChecker = setInterval(checkPaymentStatus, 5000);
            
            // Initial check
            setTimeout(checkPaymentStatus, 2000);
        });
    </script>
</body>
</html>
