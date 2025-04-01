<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .failed-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
        }
        .failed-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-message {
            background-color: #f8d7da;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            color: #721c24;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="failed-container">
            <div class="failed-icon">
                <i class="bi bi-x-circle-fill"></i>
                <!-- Fallback for Bootstrap Icons -->
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                </svg>
            </div>
            
            <h2>Payment Failed</h2>
            <p class="lead">We couldn't process your payment at this time.</p>
            
            @if(isset($message) && !empty($message))
                <div class="error-message">
                    <strong>Error Details:</strong> {{ $message }}
                </div>
            @endif
            
            <div class="mt-4">
                <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">Try Again</a>
                <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
