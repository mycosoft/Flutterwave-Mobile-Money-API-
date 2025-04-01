<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
        }
        .payment-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .payment-method-selector {
            margin-bottom: 20px;
        }
        .payment-method-selector .btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .payment-method-selector .btn.active {
            background-color: #0d6efd;
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
            background-color: #e9f7fe;
            border-radius: 5px;
            border-left: 4px solid #0d6efd;
        }
        .quick-select-btn {
            margin-right: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-form">
            <h2 class="form-title">Payment Details</h2>
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('payment.initialize') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" min="1" step="0.01" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency</label>
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
                    <label class="form-label">Payment Method</label>
                    <div class="payment-method-selector">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="payment_method" id="card" value="card" {{ old('payment_method') == 'card' ? 'checked' : '' }} autocomplete="off" required>
                            <label class="btn btn-outline-primary" for="card">Credit/Debit Card</label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="mobilemoney" value="mobilemoney" {{ old('payment_method') == 'mobilemoney' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-primary" for="mobilemoney">Mobile Money</label>
                            
                            <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }} autocomplete="off">
                            <label class="btn btn-outline-primary" for="bank_transfer">Bank Transfer</label>
                        </div>
                    </div>
                    @error('payment_method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                
                <div id="mobilemoneyOptions" class="mobile-money-options mb-3">
                    <div class="quick-select mb-3">
                        <label class="form-label">Quick Select:</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="UG" data-network="AIRTEL" data-currency="UGX">Airtel Uganda</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="UG" data-network="MTN" data-currency="UGX">MTN Uganda</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="GH" data-network="MTN" data-currency="GHS">MTN Ghana</button>
                            <button type="button" class="btn btn-sm btn-outline-primary quick-select-btn" data-country="KE" data-network="" data-currency="KES">M-Pesa Kenya</button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="network" class="form-label">Mobile Network</label>
                            <select class="form-select" id="network" name="network">
                                <option value="">Select Network</option>
                                <option value="MTN" {{ old('network') == 'MTN' ? 'selected' : '' }}>MTN</option>
                                <option value="AIRTEL" {{ old('network') == 'AIRTEL' ? 'selected' : '' }}>Airtel</option>
                                <option value="VODAFONE" {{ old('network') == 'VODAFONE' ? 'selected' : '' }}>Vodafone</option>
                                <option value="TIGO" {{ old('network') == 'TIGO' ? 'selected' : '' }}>Tigo</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="country" class="form-label">Country</label>
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
                        <label for="voucher" class="form-label">Vodafone Voucher Code</label>
                        <input type="text" class="form-control" id="voucher" name="voucher" value="{{ old('voucher') }}">
                        <small class="text-muted">Required for Vodafone Ghana payments</small>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Proceed to Payment</button>
                </div>
            </form>
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
