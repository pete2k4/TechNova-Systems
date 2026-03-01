<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - NovaTech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .navbar { background: white; padding: 20px 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: 600; margin: 0 20px; }
        .navbar a:hover { color: #3498db; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        h1 { margin-bottom: 30px; color: #2c3e50; }
        .checkout-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .checkout-form { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-section { margin-bottom: 30px; }
        .form-section h3 { margin-bottom: 20px; color: #2c3e50; font-size: 18px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #2c3e50; font-weight: 600; }
        select, input { width: 100%; padding: 12px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 14px; }
        select:focus, input:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52,152,219,0.1); }
        .form-hint { color: #7f8c8d; font-size: 12px; margin-top: 5px; }
        .order-summary { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); position: sticky; top: 20px; }
        .order-summary h3 { margin-bottom: 20px; color: #2c3e50; font-size: 18px; }
        .order-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #ecf0f1; font-size: 14px; }
        .order-item:last-child { border-bottom: none; }
        .summary-row { display: flex; justify-content: space-between; padding: 12px 0; font-size: 14px; }
        .summary-row.total { font-size: 18px; font-weight: bold; color: #27ae60; padding-top: 15px; border-top: 2px solid #ecf0f1; margin-top: 15px; }
        .button-group { display: flex; gap: 10px; margin-top: 30px; }
        button, a { padding: 12px 20px; border: none; border-radius: 4px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-submit { background: #27ae60; color: white; flex: 1; }
        .btn-submit:hover { background: #229954; }
        .btn-back { background: #95a5a6; color: white; flex: 1; }
        .btn-back:hover { background: #7f8c8d; }
        @media (max-width: 768px) {
            .checkout-layout { grid-template-columns: 1fr; }
            .order-summary { position: static; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h2><a href="{{ route('marketplace.index') }}" style="color: #3498db; margin: 0;">🏪 NovaTech</a></h2>
            <div>
                <a href="{{ route('marketplace.index') }}">Home</a>
                <a href="{{ route('marketplace.cart') }}">Cart</a>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>💳 Checkout</h1>

        <form method="POST" action="{{ route('checkout.process') }}" class="checkout-layout">
            @csrf

            <div class="checkout-form">
                <div class="form-section">
                    <h3>Discount</h3>
                    
                    <div class="form-group">
                        <label for="discount_type">Discount Type</label>
                        <select name="discount_type" id="discount_type" required>
                            <option value="">-- No Discount --</option>
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount ($)</option>
                        </select>
                        <div class="form-hint">Optional: Apply a discount code or coupon</div>
                    </div>

                    <div class="form-group">
                        <label for="discount_value">Discount Value</label>
                        <input type="number" name="discount_value" id="discount_value" min="0" step="0.01" value="0" placeholder="0">
                        <div class="form-hint">Enter percentage or dollar amount</div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Payment Information</h3>

                    <div class="form-group">
                        <label for="payment_type">Payment Method</label>
                        <select name="payment_type" id="payment_type" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="paypal">PayPal</option>
                        </select>
                        <div class="form-hint">Choose how you'll pay</div>
                    </div>

                    <div class="form-group">
                        <label for="payment_credential">Payment Details</label>
                        <input type="text" name="payment_credential" id="payment_credential" placeholder="Card number or PayPal email" required>
                        <div class="form-hint">Demo only - no real charges</div>
                    </div>
                </div>

                <div class="button-group">
                    <a href="{{ route('marketplace.cart') }}" class="btn-back">← Back to Cart</a>
                    <button type="submit" class="btn-submit">Complete Order →</button>
                </div>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>

                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                    @foreach($cart as $item)
                        <div class="order-item">
                            <div>
                                <strong>{{ $item['name'] }}</strong>
                                <div style="color: #7f8c8d; font-size: 12px;">Qty: {{ $item['quantity'] }}</div>
                            </div>
                            <div style="text-align: right;">
                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Discount</span>
                    <span>-$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                <div style="padding: 15px; background: #f0f0f0; border-radius: 4px; margin-top: 20px; font-size: 12px; color: #555;">
                    <p><strong>🔒 Secure Checkout</strong></p>
                    <p style="margin-top: 8px;">Your payment information is encrypted and never stored.</p>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
