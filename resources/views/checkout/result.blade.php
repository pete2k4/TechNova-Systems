<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Result</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 700px; margin: 0 auto; }
        .result-card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .success { border-left: 4px solid #27ae60; }
        .failure { border-left: 4px solid #e74c3c; }
        .status { font-size: 48px; text-align: center; margin-bottom: 20px; }
        h1 { text-align: center; margin-bottom: 30px; color: #2c3e50; }
        .factory-section, .details-section, .summary-section { margin-bottom: 30px; padding-bottom: 30px; border-bottom: 1px solid #ecf0f1; }
        .factory-section:last-child, .details-section:last-child, .summary-section:last-child { border-bottom: none; }
        .section-title { color: #2980b9; font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 15px; letter-spacing: 0.5px; }
        .factory-box { background: #ecf9ff; padding: 15px; border-radius: 4px; border-left: 3px solid #3498db; }
        .factory-box p { color: #2c3e50; margin: 8px 0; }
        .factory-box strong { color: #2980b9; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item { padding: 15px; background: #f9f9f9; border-radius: 4px; }
        .info-label { color: #7f8c8d; font-size: 12px; text-transform: uppercase; font-weight: 600; margin-bottom: 5px; }
        .info-value { color: #2c3e50; font-size: 16px; font-weight: 600; }

        .object-details { background: #f0f0f0; padding: 15px; border-radius: 4px; margin-top: 10px; }
        .object-details h4 { color: #2c3e50; font-size: 13px; margin-bottom: 10px; margin-top: 15px; }
        .object-details h4:first-child { margin-top: 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: #555; }
        .detail-row strong { color: #2c3e50; }

        .action-buttons { display: flex; gap: 10px; margin-top: 40px; }
        button, a { padding: 12px 20px; border-radius: 4px; text-decoration: none; border: none; font-weight: 600; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #3498db; color: white; flex: 1; }
        .btn-primary:hover { background: #2980b9; }
        .btn-secondary { background: #95a5a6; color: white; flex: 1; }
        .btn-secondary:hover { background: #7f8c8d; }

        .badge { display: inline-block; padding: 4px 10px; background: #ecf0f1; color: #2c3e50; border-radius: 12px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="result-card {{ $success ? 'success' : 'failure' }}">
            <div class="status">{{ $success ? '✅' : '❌' }}</div>
            <h1>{{ $success ? 'Checkout Successful!' : 'Checkout Failed' }}</h1>

            <div class="factory-section">
                <div class="section-title">🏭 Factory Used</div>
                <div class="factory-box">
                    <p><strong>Factory Class:</strong> {{ $factoryClass }}</p>
                    <p><strong>Family Name:</strong> {{ $factoryName }}</p>
                    <p><strong>Product Type:</strong> <span class="badge">{{ $productType }}</span></p>
                </div>
            </div>

            <div class="details-section">
                <div class="section-title">📦 Order Details</div>
                
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Product</div>
                        <div class="info-value">{{ $product['name'] }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Base Price</div>
                        <div class="info-value">${{ number_format($product['price'], 2) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Discount Type</div>
                        <div class="info-value">{{ ucfirst($discount['type']) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Discount Value</div>
                        <div class="info-value">
                            {{ $discount['type'] === 'percentage' ? $discount['value'] . '%' : '$' . number_format($discount['value'], 2) }}
                        </div>
                    </div>
                </div>

                <div class="object-details">
                    <h4>💰 Price Calculation</h4>
                    <div class="detail-row">
                        <strong>Base Price:</strong>
                        <span>${{ number_format($product['price'], 2) }}</span>
                    </div>
                    @if($discount['type'] === 'percentage')
                        <div class="detail-row">
                            <strong>Discount ({{ $discount['value'] }}%):</strong>
                            <span>-${{ number_format($product['price'] * $discount['value'] / 100, 2) }}</span>
                        </div>
                        <div class="detail-row" style="border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                            <strong>Final Price:</strong>
                            <span>${{ number_format($product['price'] - ($product['price'] * $discount['value'] / 100), 2) }}</span>
                        </div>
                    @else
                        <div class="detail-row">
                            <strong>Discount (Fixed):</strong>
                            <span>-${{ number_format(min($discount['value'], $product['price']), 2) }}</span>
                        </div>
                        <div class="detail-row" style="border-top: 1px solid #ddd; padding-top: 8px; margin-top: 8px;">
                            <strong>Final Price:</strong>
                            <span>${{ number_format(max(0, $product['price'] - $discount['value']), 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="summary-section">
                <div class="section-title">💳 Payment Method</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Method</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $payment['type'])) }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account</div>
                        <div class="info-value" style="word-break: break-all; font-size: 13px;">
                            {{ $payment['type'] === 'paypal' ? (str_contains($payment['credential'], '@') ? $payment['credential'] : '**** email') : '****' . substr($payment['credential'], -4) }}
                        </div>
                    </div>
                </div>

                <div class="object-details">
                    <h4>🏪 Repository Configuration</h4>
                    <div class="detail-row">
                        <strong>Type:</strong>
                        <span>{{ $productType === 'physical' ? 'Cached MySQL' : 'MySQL' }}</span>
                    </div>
                    <div class="detail-row">
                        <strong>Caching Enabled:</strong>
                        <span>{{ $productType === 'physical' ? '✅ Yes' : '❌ No' }}</span>
                    </div>
                    @if($productType === 'physical')
                        <div class="detail-row" style="font-size: 12px; color: #7f8c8d; margin-top: 10px; border-top: 1px solid #ddd; padding-top: 8px;">
                            <span>Physical products use caching for inventory optimization</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="action-buttons">
                <a href="{{ route('checkout.select') }}" class="btn-primary">← New Checkout</a>
                <a href="/" class="btn-secondary">Home →</a>
            </div>
        </div>
    </div>
</body>
</html>
