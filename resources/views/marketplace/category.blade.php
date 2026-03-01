<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $category->name }} - NovaTech Marketplace</title>
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
        .breadcrumb a:hover { text-decoration: underline; }
        h1 { margin-bottom: 10px; color: #2c3e50; }
        .category-description { color: #7f8c8d; margin-bottom: 30px; }
        .products { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        .product-info { padding: 15px; }
        .product-name { font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
        .product-type-badge { display: inline-block; padding: 4px 8px; background: #ecf0f1; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 10px; }
        .product-price { color: #27ae60; font-size: 20px; font-weight: bold; margin: 10px 0; }
        .product-stock { font-size: 12px; color: #7f8c8d; margin-bottom: 10px; }
        .product-link { display: inline-block; color: #3498db; text-decoration: none; font-size: 14px; font-weight: 600; }
        .product-link:hover { text-decoration: underline; }
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
            <a href="{{ route('marketplace.index') }}">Home</a> / <strong>{{ $category->name }}</strong>
        </div>

        <h1>{{ $category->name }}</h1>
        <p class="category-description">{{ $category->description }}</p>

        <div class="products">
            @forelse($products as $product)
                <div class="product-card">
                    <div style="background: #f0f0f0; padding: 20px; text-align: center; font-size: 40px;">
                        @if($product->isDigital()) 💾 @else 🖥️ @endif
                    </div>
                    <div class="product-info">
                        <a href="{{ route('marketplace.product', $product->slug) }}" style="text-decoration: none; color: #2c3e50; display: block; font-weight: 600; margin-bottom: 8px;">
                            {{ $product->name }}
                        </a>
                        <div style="margin-bottom: 10px;">
                            <span class="product-type-badge">
                                @if($product->isDigital()) DIGITAL @else PHYSICAL @endif
                            </span>
                        </div>
                        <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        @if($product->isPhysical())
                            <div class="product-stock">
                                @if($product->stock > 5)
                                    <span style="color: #27ae60;">✓ {{ $product->stock }} in stock</span>
                                @elseif($product->stock > 0)
                                    <span style="color: #f39c12;">⚠ Only {{ $product->stock }} left</span>
                                @else
                                    <span style="color: #e74c3c;">✗ Out of stock</span>
                                @endif
                            </div>
                        @endif
                        <a href="{{ route('marketplace.product', $product->slug) }}" class="product-link">View Details →</a>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1/-1; text-align: center; color: #7f8c8d; margin: 40px 0;">No products in this category</p>
            @endforelse
        </div>

        @if($products->hasPages())
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</body>
</html>
