<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - NovaTech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .navbar { background: white; padding: 20px 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: 600; margin: 0 20px; }
        .navbar a:hover { color: #3498db; }
        .container { max-width: 900px; margin: 0 auto; padding: 40px 20px; }
        .confirmation { background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; }
        .confirmation-header { background: #27ae60; color: white; padding: 40px; text-align: center; }
        .confirmation-header h1 { font-size: 36px; margin-bottom: 10px; }
        .confirmation-header p { font-size: 16px; opacity: 0.9; }
        .confirmation-content { padding: 40px; }
        .order-number { background: #f0f0f0; padding: 20px; border-radius: 8px; margin-bottom: 30px; text-align: center; }
        .order-number label { font-size: 12px; color: #7f8c8d; display: block; margin-bottom: 8px; }
        .order-number code { font-size: 24px; font-weight: bold; color: #2c3e50; }
        .factory-info { background: #e8f4f8; padding: 15px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #3498db; }
        .factory-info p { color: #2c3e50; margin: 5px 0; }
        .factory-info strong { color: #2980b9; }
        .section-title { color: #2c3e50; font-size: 16px; font-weight: 600; margin-bottom: 15px; margin-top: 30px; }
        .section-title:first-of-type { margin-top: 0; }
        .order-items { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .order-items th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ecf0f1; font-weight: 600; color: #2c3e50; }
        .order-items td { padding: 12px; border-bottom: 1px solid #ecf0f1; }
        .order-items tr:last-child td { border-bottom: none; }
        .summary-line { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ecf0f1; }
        .summary-line.total { font-size: 18px; font-weight: bold; color: #27ae60; border-bottom: none; margin-top: 15px; padding-top: 15px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .info-box { background: #f9f9f9; padding: 15px; border-radius: 8px; }
        .info-box label { font-size: 12px; color: #7f8c8d; text-transform: uppercase; display: block; margin-bottom: 5px; }
        .info-box value { font-size: 16px; font-weight: 600; color: #2c3e50; display: block; }
        .action-buttons { display: flex; gap: 10px; margin-top: 30px; }
        a { text-decoration: none; padding: 12px 20px; border-radius: 4px; font-weight: 600; transition: all 0.2s; display: inline-block; }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h2><a href="{{ route('marketplace.index') }}" style="color: #3498db; margin: 0;">🏪 NovaTech</a></h2>
            <div>
                <a href="{{ route('marketplace.index') }}">Home</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="confirmation">
            <div class="confirmation-header">
                <h1>✅ Order Confirmed!</h1>
                <p>Thank you for your purchase</p>
            </div>

            <div class="confirmation-content">
                <div class="order-number">
                    <label>Order Number</label>
                    <code>{{ $order->order_number }}</code>
                </div>

                <div class="factory-info">
                    <p><strong>🏭 Factory Pattern in Action:</strong></p>
                    <p>This order was processed using: <strong>{{ $factoryClass }}</strong></p>
                    <p style="margin-top: 8px; font-size: 13px;">Family: {{ $factoryName }}</p>
                </div>

                <h3 class="section-title">Order Items</h3>
                <table class="order-items">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align: right;">Price</th>
                            <th style="text-align: center;">Qty</th>
                            <th style="text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    <div style="font-size: 12px; color: #7f8c8d; margin-top: 4px;">
                                        @if($item->product->isDigital()) 💾 Digital @else 🖥️ Physical @endif
                                    </div>
                                </td>
                                <td style="text-align: right;">${{ number_format($item->price, 2) }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right; font-weight: 600;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount > 0)
                    <div class="summary-line">
                        <span>Discount ({{ $discount['type'] === 'percentage' ? $discount['value'].'%' : '$'.number_format($discount['value'], 2) }})</span>
                        <span>-${{ number_format($order->discount, 2) }}</span>
                    </div>
                @endif
                <div class="summary-line total">
                    <span>Total</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>

                <h3 class="section-title">Payment Information</h3>
                <div class="info-grid">
                    <div class="info-box">
                        <label>Payment Method</label>
                        <value>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</value>
                    </div>
                    <div class="info-box">
                        <label>Order Status</label>
                        <value style="color: #27ae60;">✓ {{ ucfirst($order->status) }}</value>
                    </div>
                </div>

                <h3 class="section-title">What's Next?</h3>
                <div style="background: #f9f9f9; padding: 15px; border-radius: 8px; line-height: 1.6; color: #555; margin-bottom: 30px;">
                    @php
                        $hasPhysical = $order->items->some(fn($item) => $item->product->isPhysical());
                        $hasDigital = $order->items->some(fn($item) => $item->product->isDigital());
                    @endphp

                    @if($hasDigital)
                        <p>✓ <strong>Digital products are ready now!</strong> Check your email for download links and license keys.</p>
                    @endif

                    @if($hasPhysical)
                        <p style="margin-top: 8px;">📦 <strong>Physical items will ship within 2-3 business days.</strong> You'll receive a tracking number via email.</p>
                    @endif

                    <p style="margin-top: 8px;">📧 A confirmation email has been sent to your account.</p>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('marketplace.index') }}" class="btn-primary">← Continue Shopping</a>
                    <a href="#" class="btn-secondary">Download Invoice (Coming Soon)</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
