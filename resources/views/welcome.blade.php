<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money Payment API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #dc3545 100%);
            padding-top: 50px;
            padding-bottom: 50px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .payment-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #dc3545;
            font-weight: 700;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-container svg {
            height: 60px;
            color: #dc3545;
        }
        .payment-method-selector {
            margin-bottom: 20px;
        }
        .payment-method-selector .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .payment-method-selector .btn.active {
            background-color: #dc3545;
            color: white;
        }
        .mobile-money-options {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .quick-select {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8d7da;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        .quick-select-btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .info-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid #dc3545;
        }
        .step {
            display: flex;
            margin-bottom: 15px;
        }
        .step-number {
            background-color: #dc3545;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 10px;
            flex-shrink: 0;
        }
        .step-content {
            flex-grow: 1;
        }
        .step-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-label {
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        .form-label i {
            margin-right: 8px;
            color: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 15px;
            color: white;
            font-size: 14px;
        }
        .footer a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover, .btn-primary:focus, .btn-primary:active {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-outline-primary {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-primary:hover, .btn-check:checked+.btn-outline-primary {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        .text-primary {
            color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-form">
            <div class="logo-container">
                <svg viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253Z" fill="currentColor"/>
                </svg>
            </div>
            
            <h2 class="form-title">Mobile Money Payment API</h2>
            <p class="text-center mb-4">
                <span class="badge bg-danger me-1">Uganda</span>
                <span class="badge bg-danger me-1">Ghana</span>
                <span class="badge bg-danger me-1">Kenya</span>
                <span class="badge bg-danger me-1">Rwanda</span>
                <span class="badge bg-danger me-1">Tanzania</span>
                <span class="badge bg-danger">Nigeria</span>
            </p>
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('payment.initialize') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label"><i class="bi bi-person-fill"></i> Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your full name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label"><i class="bi bi-envelope-fill"></i> Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="your.email@example.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label"><i class="bi bi-phone-fill"></i> Phone Number</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. 0750123456" required>
                    <div class="form-text">For mobile money, enter your registered number (format depends on country)</div>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label"><i class="bi bi-cash-stack"></i> Amount</label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 5000) }}" min="1" step="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label"><i class="bi bi-currency-exchange"></i> Currency</label>
                        <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="UGX" {{ old('currency') == 'UGX' ? 'selected' : '' }}>UGX (Uganda)</option>
                            <option value="GHS" {{ old('currency') == 'GHS' ? 'selected' : '' }}>GHS (Ghana)</option>
                            <option value="NGN" {{ old('currency') == 'NGN' ? 'selected' : '' }}>NGN (Nigeria)</option>
                            <option value="KES" {{ old('currency') == 'KES' ? 'selected' : '' }}>KES (Kenya)</option>
                            <option value="RWF" {{ old('currency') == 'RWF' ? 'selected' : '' }}>RWF (Rwanda)</option>
                            <option value="ZAR" {{ old('currency') == 'ZAR' ? 'selected' : '' }}>ZAR (South Africa)</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-credit-card-fill"></i> Payment Method</label>
                    <div class="payment-method-selector">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="card" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }} autocomplete="off" required>
                            <label class="btn btn-outline-primary" for="card"><i class="bi bi-credit-card"></i> Credit/Debit Card</label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="mobilemoney" value="mobilemoney" {{ old('payment_method') == 'mobilemoney' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-primary" for="mobilemoney"><i class="bi bi-phone"></i> Mobile Money</label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-primary" for="bank_transfer"><i class="bi bi-bank"></i> Bank Transfer</label>
                        </div>
                    </div>
                    @error('payment_method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="mobilemoneyOptions" class="mobile-money-options mb-3">
                    <div class="quick-select mb-3">
                        <label class="form-label"><i class="bi bi-lightning-charge-fill"></i> Quick Select:</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="UG" data-network="AIRTEL" data-currency="UGX">Airtel Uganda</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="UG" data-network="MTN" data-currency="UGX">MTN Uganda</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="GH" data-network="MTN" data-currency="GHS">MTN Ghana</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="KE" data-network="" data-currency="KES">M-Pesa Kenya</button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="network" class="form-label"><i class="bi bi-broadcast-pin"></i> Mobile Network</label>
                            <select class="form-select" id="network" name="network">
                                <option value="">Select Network</option>
                                <option value="MTN" {{ old('network') == 'MTN' ? 'selected' : '' }}>MTN</option>
                                <option value="AIRTEL" {{ old('network') == 'AIRTEL' ? 'selected' : '' }}>Airtel</option>
                                <option value="VODAFONE" {{ old('network') == 'VODAFONE' ? 'selected' : '' }}>Vodafone</option>
                                <option value="TIGO" {{ old('network') == 'TIGO' ? 'selected' : '' }}>Tigo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label"><i class="bi bi-globe"></i> Country</label>
                            <select class="form-select" id="country" name="country">
                                <option value="UG" {{ old('country') == 'UG' ? 'selected' : '' }}>Uganda</option>
                                <option value="GH" {{ old('country') == 'GH' ? 'selected' : '' }}>Ghana</option>
                                <option value="KE" {{ old('country') == 'KE' ? 'selected' : '' }}>Kenya</option>
                                <option value="RW" {{ old('country') == 'RW' ? 'selected' : '' }}>Rwanda</option>
                                <option value="TZ" {{ old('country') == 'TZ' ? 'selected' : '' }}>Tanzania</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="vodafoneOptions" class="mb-3" style="display: none;">
                        <label for="voucher" class="form-label"><i class="bi bi-ticket-perforated-fill"></i> Vodafone Voucher Code</label>
                        <input type="text" class="form-control" id="voucher" name="voucher" value="{{ old('voucher') }}">
                        <small class="text-muted">Required for Vodafone Ghana payments</small>
                    </div>
                </div>
                
                <div class="info-section mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-info-circle-fill text-primary me-2"></i>
                        <span class="fw-bold">Important Notes:</span>
                    </div>
                    <ul class="mb-0 ps-4 small">
                        <li>For Uganda mobile money, phone number must be in international format with country code 256</li>
                        <li>Ensure you have sufficient balance in your account</li>
                        <li>Standard mobile money charges may apply</li>
                        <li>For Vodafone Ghana, a voucher code is required</li>
                    </ul>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-lock-fill me-2"></i>
                        Proceed to Payment
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted d-flex align-items-center justify-content-center">
                    <i class="bi bi-shield-lock me-1"></i>
                    Secure payments powered by Flutterwave
                </small>
            </div>
        </div>
        
        <div class="footer">
            <p>
                <i class="bi bi-code-slash"></i> Developed by 
                <a href="#" target="_blank">Mycosoft Technologies</a>
                <i class="bi bi-c-circle"></i> {{ date('Y') }}
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMoneyRadio = document.getElementById('mobilemoney');
            const mobileMoneyOptions = document.getElementById('mobilemoneyOptions');
            const networkSelect = document.getElementById('network');
            const countrySelect = document.getElementById('country');
            const currencySelect = document.getElementById('currency');
            const vodafoneOptions = document.getElementById('vodafoneOptions');
            
            // Show/hide mobile money options based on initial selection
            if (mobileMoneyRadio.checked) {
                mobileMoneyOptions.style.display = 'block';
            }
            
            // Add event listeners to payment method radios
            document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    if (this.value === 'mobilemoney') {
                        mobileMoneyOptions.style.display = 'block';
                    } else {
                        mobileMoneyOptions.style.display = 'none';
                    }
                });
            });
            
            // Show/hide Vodafone voucher field based on network selection
            function updateVodafoneOptions() {
                if (networkSelect.value === 'VODAFONE' && countrySelect.value === 'GH') {
                    vodafoneOptions.style.display = 'block';
                } else {
                    vodafoneOptions.style.display = 'none';
                }
            }
            
            networkSelect.addEventListener('change', updateVodafoneOptions);
            countrySelect.addEventListener('change', updateVodafoneOptions);
            
            // Initial check
            updateVodafoneOptions();
            
            // Quick select buttons
            document.querySelectorAll('.quick-select-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const country = this.getAttribute('data-country');
                    const network = this.getAttribute('data-network');
                    const currency = this.getAttribute('data-currency');
                    
                    countrySelect.value = country;
                    networkSelect.value = network;
                    currencySelect.value = currency;
                    
                    // Make sure mobile money is selected
                    mobileMoneyRadio.checked = true;
                    mobileMoneyOptions.style.display = 'block';
                    
                    // Update Vodafone options
                    updateVodafoneOptions();
                });
            });
        });
    </script>
</body>
</html>
