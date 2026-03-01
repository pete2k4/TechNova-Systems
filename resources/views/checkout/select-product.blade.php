<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factory Pattern - Select Product</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, serif; background: #f5f5f5; padding: 40px 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #333; margin-bottom: 30px; text-align: center; }
        .products { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .product-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        .product-card h2 { font-size: 24px; margin-bottom: 15px; color: #2c3e50; }
        .product-card p { color: #7f8c8d; margin-bottom: 20px; }
        .badge { display: inline-block; padding: 8px 16px; background: #3498db; color: white; border-radius: 20px; font-size: 12px; font-weight: bold; }
        @media (max-width: 600px) {
            .products { grid-template-columns: 1fr; }
            h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏭 Factory Pattern Demo</h1>
        <p style="text-align: center; color: #666; margin-bottom: 40px;">Select a product type to see different factories in action</p>
        
        <div class="products">
            <a href="{{ route('checkout.form', ['type' => 'digital']) }}" class="product-card">
                <h2>💾 Digital Product</h2>
                <p>Software licenses, downloads</p>
                <span class="badge">DigitalProductCommerceFactory</span>
            </a>

            <a href="{{ route('checkout.form', ['type' => 'physical']) }}" class="product-card">
                <h2>📦 Physical Product</h2>
                <p>Hardware with shipping & inventory</p>
                <span class="badge">PhysicalProductCommerceFactory</span>
            </a>
        </div>

        <div style="margin-top: 60px; padding: 20px; background: white; border-radius: 8px; border-left: 4px solid #3498db;">
            <h3 style="color: #2c3e50; margin-bottom: 10px;">ℹ️ About This Demo</h3>
            <p style="color: #7f8c8d; line-height: 1.6;">
                The Abstract Factory pattern provides a way to create families of related objects. 
                Each product type uses a different factory with specialized behavior:
            </p>
            <ul style="color: #7f8c8d; margin-top: 10px; margin-left: 20px;">
                <li><strong>Digital:</strong> No caching by default, instant delivery</li>
                <li><strong>Physical:</strong> Caching enabled, inventory awareness</li>
            </ul>
        </div>
    </div>
</body>
</html>
