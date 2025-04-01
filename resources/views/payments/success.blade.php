<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .success-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .transaction-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }
        .transaction-details table {
            width: 100%;
        }
        .transaction-details td {
            padding: 8px 0;
        }
        .transaction-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
                <!-- Fallback for Bootstrap Icons -->
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            
            <h2>Payment Successful!</h2>
            <p class="lead">Your transaction has been completed successfully.</p>
            
            <div class="transaction-details">
                <h5>Transaction Details</h5>
                <table>
                    <tr>
                        <td>Transaction ID:</td>
                        <td>{{ $transaction['id'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Amount:</td>
                        <td>{{ $transaction['currency'] ?? '' }} {{ number_format($transaction['amount'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Payment Method:</td>
                        <td>{{ $transaction['payment_type'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td>{{ isset($transaction['created_at']) ? date('F j, Y g:i a', strtotime($transaction['created_at'])) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Reference:</td>
                        <td>{{ $transaction['tx_ref'] ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="mt-4">
                <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
