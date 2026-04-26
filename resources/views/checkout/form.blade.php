<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ ucfirst($type) }} Product</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 10px; }
        .breadcrumb { color: #7f8c8d; margin-bottom: 30px; font-size: 14px; }
        .breadcrumb a { color: #3498db; text-decoration: none; }
        .factory-info { background: #e8f4f8; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #3498db; }
        .factory-info p { color: #2c3e50; margin: 0; font-size: 14px; }
        .factory-info strong { color: #2980b9; }
        .form-section { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; }
        select, input { width: 100%; padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 14px; }
        select:focus, input:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52,152,219,0.1); }
        .form-hint { color: #7f8c8d; font-size: 12px; margin-top: 5px; }
        .button-group { display: flex; gap: 10px; }
        button { flex: 1; padding: 12px; border: none; border-radius: 4px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-submit { background: #27ae60; color: white; }
        .btn-submit:hover { background: #229954; }
        .btn-back { background: #95a5a6; color: white; }
        .btn-back:hover { background: #7f8c8d; }
        .product-preview { background: #f9f9f9; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .product-preview h3 { color: #2c3e50; font-size: 16px; margin-bottom: 10px; }
        .price { color: #27ae60; font-size: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('checkout.select') }}">← Back to Product Selection</a>
        </div>

        <h1>{{ ucfirst($type) }} Product Checkout</h1>
        
        <div class="factory-info">
            <p><strong>🏭 Factory in Use:</strong> {{ $factoryName }}</p>
            <p style="margin-top: 8px; font-size: 13px;">This specialized factory creates a family of compatible objects optimized for {{ $type }} products.</p>
        </div>

        <form method="POST" action="{{ route('checkout.process') }}" class="form-section">
            @csrf

            <input type="hidden" name="product_type" value="{{ $type }}">

            <div class="product-preview">
                <h3>📦 Product Sample</h3>
                @if($type === 'digital')
                    <p><strong>Windows 11 Pro License</strong></p>
                    <p class="price">$199.99</p>
                @else
                    <p><strong>NVIDIA RTX 4090</strong></p>
                    <p class="price">$1,599.99</p>
                @endif
            </div>

            <div class="form-group">
                <label for="discount_type">Discount Type</label>
                <select name="discount_type" id="discount_type" required>
                    <option value="">-- Select Discount Type --</option>
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount ($)</option>
                </select>
                <div class="form-hint">Choose how to apply the discount</div>
            </div>

            <div class="form-group">
                <label for="discount_value">Discount Value</label>
                <input type="number" name="discount_value" id="discount_value" min="0" step="0.01" placeholder="e.g., 15 or 25.50" required>
                <div class="form-hint">Amount or percentage value</div>
            </div>

            <div class="form-group">
                <label for="payment_type">Payment Method</label>
                <select name="payment_type" id="payment_type" required>
                    <option value="">-- Select Payment Method --</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                </select>
                <div class="form-hint">How you'll pay for this order</div>
            </div>

            <div class="form-group">
                <label for="payment_credential">{{ $type === 'paypal' ? 'PayPal Email' : 'Card/Account' }}</label>
                <input type="text" name="payment_credential" id="payment_credential" 
                       placeholder="{{ $type === 'digital' ? '4532015112830366' : 'user@example.com' }}" required>
                <div class="form-hint">{{ $type === 'digital' ? 'Demo card number or email' : 'Your PayPal email or card number' }}</div>
            </div>

            <div class="button-group">
                <a href="{{ route('checkout.select') }}" class="btn-back" style="border: none; text-decoration: none; display: flex; align-items: center; justify-content: center;">Back</a>
                <button type="submit" class="btn-submit">Complete Checkout →</button>
            </div>
        </form>
    </div>
</body>
</html>
