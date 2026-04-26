<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - NovaTech Marketplace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .navbar { background: white; padding: 20px 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: 600; margin: 0 20px; }
        .navbar a:hover { color: #3498db; }
        .cart-badge { background: #e74c3c; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .breadcrumb { margin-bottom: 30px; color: #7f8c8d; }
        .breadcrumb a { color: #3498db; text-decoration: none; }
        .product-section { background: white; padding: 40px; border-radius: 8px; margin-bottom: 40px; }
        .product-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .product-image { background: #f0f0f0; padding: 40px; border-radius: 8px; text-align: center; font-size: 100px; }
        .product-details h1 { margin-bottom: 15px; color: #2c3e50; }
        .product-category { color: #3498db; text-decoration: none; font-size: 14px; margin-bottom: 15px; display: inline-block; }
        .product-category:hover { text-decoration: underline; }
        .product-type-badge { display: inline-block; padding: 8px 16px; background: #ecf0f1; border-radius: 4px; font-size: 12px; font-weight: bold; margin-bottom: 20px; margin-left: 10px; }
        .rating { color: #f39c12; margin-bottom: 20px; }
        .product-price { font-size: 36px; color: #27ae60; font-weight: bold; margin: 20px 0; }
        .product-stock { font-size: 14px; margin-bottom: 20px; }
        .product-stock.available { color: #27ae60; }
        .product-stock.low { color: #f39c12; }
        .product-stock.unavailable { color: #e74c3c; }
        .product-description { color: #555; line-height: 1.6; margin-bottom: 30px; }
        .add-to-cart-form { margin-bottom: 30px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50; }
        input[type="number"] { padding: 10px; border: 1px solid #bdc3c7; border-radius: 4px; width: 100px; }
        button { background: #27ae60; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; width: 100%; }
        button:hover { background: #229954; }
        button:disabled { background: #95a5a6; cursor: not-allowed; }
        .related-section { margin-top: 60px; }
        .related-section h2 { margin-bottom: 20px; color: #2c3e50; }
        .related-products { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .related-card { background: white; border-radius: 8px; padding: 15px; overflow: hidden; }
        .related-card a { text-decoration: none; color: #3498db; font-weight: 600; }
        .related-card a:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .product-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h2><a href="{{ route('marketplace.index') }}" style="color: #3498db; margin: 0;">🏪 NovaTech</a></h2>
            <div>
                <a href="{{ route('marketplace.index') }}">Home</a>
                <a href="{{ route('marketplace.cart') }}">Cart
                    @if(count(session('cart', [])) > 0)
                        <span class="cart-badge">{{ count(session('cart', [])) }}</span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('marketplace.index') }}">Home</a>
            / <a href="{{ route('marketplace.category', $product->category->slug) }}">{{ $product->category->name }}</a>
            / <strong>{{ $product->name }}</strong>
        </div>

        <div class="product-section">
            <div class="product-layout">
                <div class="product-image">
                    @if($product->isDigital()) 💾 @else 🖥️ @endif
                </div>
                <div class="product-details">
                    <a href="{{ route('marketplace.category', $product->category->slug) }}" class="product-category">← {{ $product->category->name }}</a>
                    <span class="product-type-badge">{{ $product->isDigital() ? 'DIGITAL' : 'PHYSICAL' }}</span>
                    
                    <h1 style="margin-top: 10px;">{{ $product->name }}</h1>
                    <div class="rating">⭐⭐⭐⭐⭐ (Highly Rated)</div>

                    <div class="product-price">${{ number_format($product->price, 2) }}</div>

                    @if($product->isPhysical())
                        <div class="product-stock @if($product->stock > 5) available @elseif($product->stock > 0) low @else unavailable @endif">
                            @if($product->stock > 5)
                                ✓ {{ $product->stock }} in stock - Ships within 2-3 business days
                            @elseif($product->stock > 0)
                                ⚠ Only {{ $product->stock }} left in stock - Order now!
                            @else
                                ✗ Out of stock - Check back soon
                            @endif
                        </div>
                    @else
                        <div class="product-stock available">✓ Instant Digital Delivery - Available 24/7</div>
                    @endif

                    <div class="product-description">
                        <h3 style="margin-bottom: 10px; color: #2c3e50;">Description</h3>
                        {{ $product->description }}
                    </div>

                    @if($product->isPhysical() && $product->stock === 0)
                        <button disabled>Out of Stock</button>
                    @else
                        <form method="POST" action="{{ route('marketplace.addToCart', $product->id) }}" class="add-to-cart-form">
                            @csrf
                            <div class="form-group">
                                <label for="quantity">Quantity</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="{{ $product->isPhysical() ? $product->stock : 100 }}" required>
                            </div>
                            <button type="submit">🛒 Add to Cart</button>
                        </form>
                    @endif

                    <div style="padding: 20px; background: #f9f9f9; border-radius: 4px; font-size: 14px; color: #555;">
                        <h4 style="margin-bottom: 10px; color: #2c3e50;">📦 Product Info</h4>
                        <p><strong>SKU:</strong> {{ $product->sku }}</p>
                        <p><strong>Type:</strong> {{ $product->isDigital() ? 'Digital (Instant Access)' : 'Physical (Shipped)' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($relatedProducts->count() > 0)
            <div class="related-section">
                <h2>Related Products</h2>
                <div class="related-products">
                    @foreach($relatedProducts as $related)
                        <div class="related-card">
                            <div style="background: #f0f0f0; padding: 20px; text-align: center; font-size: 40px; margin-bottom: 10px;">
                                @if($related->isDigital()) 💾 @else 🖥️ @endif
                            </div>
                            <p style="font-weight: 600; color: #2c3e50; margin-bottom: 8px;">{{ $related->name }}</p>
                            <p style="color: #27ae60; font-weight: bold; margin-bottom: 10px;">${{ number_format($related->price, 2) }}</p>
                            <a href="{{ route('marketplace.product', $related->slug) }}">View →</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</body>
</html>
