<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaTech Marketplace - Tech Products & Software</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .navbar { background: white; padding: 20px 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: 600; margin: 0 20px; }
        .navbar a:hover { color: #3498db; }
        .cart-badge { background: #e74c3c; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        h1 { margin-bottom: 40px; color: #2c3e50; }
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 40px; }
        .category-card { background: white; padding: 20px; border-radius: 8px; text-align: center; text-decoration: none; color: #333; border: 2px solid transparent; transition: all 0.2s; }
        .category-card:hover { border-color: #3498db; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .products { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        .product-media { background: #f0f0f0; height: 180px; display: flex; align-items: center; justify-content: center; }
        .product-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .product-media-fallback { font-size: 40px; }
        .product-info { padding: 15px; }
        .product-name { font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
        .product-type { font-size: 12px; color: #7f8c8d; margin-bottom: 10px; }
        .product-type-badge { display: inline-block; padding: 4px 8px; background: #ecf0f1; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .product-price { color: #27ae60; font-size: 20px; font-weight: bold; margin: 10px 0; }
        .product-price .original { color: #7f8c8d; text-decoration: line-through; font-size: 13px; font-weight: 500; margin-right: 8px; }
        .product-price .sale { color: #c0392b; }
        .sale-badge { display: inline-block; margin-bottom: 8px; padding: 4px 8px; border-radius: 999px; background: #fff0f0; color: #c0392b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .product-stock { font-size: 12px; color: #7f8c8d; margin-bottom: 10px; }
        .product-link { display: inline-block; color: #3498db; text-decoration: none; font-size: 14px; font-weight: 600; }
        .product-link:hover { text-decoration: underline; }
        .pagination { display: flex; justify-content: center; gap: 10px; margin-top: 40px; }
        .pagination a, .pagination span { padding: 10px 15px; border-radius: 4px; text-decoration: none; color: #3498db; background: white; border: 1px solid #ecf0f1; }
        .pagination .active { background: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h2><a href="{{ route('marketplace.index') }}" style="color: #3498db; margin: 0;">&#x1F6EA; NovaTech</a></h2>
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
        <h1>&#x2728; Welcome to NovaTech Marketplace</h1>
        
        <h3 style="margin-bottom: 15px; color: #555;">Browse by Category</h3>
        <div class="categories">
            <a href="{{ route('marketplace.index') }}" class="category-card">
                <div style="font-size: 24px; margin-bottom: 8px;">&#x1F31F;</div>
                <div style="font-weight: 600;">All Products</div>
            </a>
            @foreach($categories as $category)
                <a href="{{ route('marketplace.category', $category->slug) }}" class="category-card">
                    <div style="font-size: 24px; margin-bottom: 8px;">
                        @if($category->id === 1) &#x1F4BF; @elseif($category->id === 2) &#x1F4DA; @elseif($category->id === 3) &#x1F5A5; @elseif($category->id === 4) &#x1F310; @else &#x1F527; @endif
                    </div>
                    <div style="font-weight: 600;">{{ $category->name }}</div>
                </a>
            @endforeach
        </div>

        <h3 style="margin: 40px 0 20px 0; color: #555;">Featured Products</h3>
        <div class="products">
            @forelse($products as $product)
                <div class="product-card">
                    <div class="product-media">
                        @if(!empty($product->image_url))
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-media-fallback">@if($product->isDigital()) &#x1F4BE; @else &#x1F5A5; @endif</div>
                        @endif
                    </div>
                    <div class="product-info">
                        <a href="{{ route('marketplace.product', $product->slug) }}" class="product-name" style="text-decoration: none; color: #2c3e50; display: block;">
                            {{ $product->name }}
                        </a>
                        <div class="product-type">
                            <span class="product-type-badge">
                                @if($product->isDigital()) DIGITAL @else PHYSICAL @endif
                            </span>
                        </div>
                        @if($product->discounted_price < $product->price)
                            <div class="sale-badge">Discounted</div>
                            <div class="product-price">
                                <span class="original">${{ number_format($product->price, 2) }}</span>
                                <span class="sale">${{ number_format($product->discounted_price, 2) }}</span>
                            </div>
                        @else
                            <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        @endif
                        @if($product->isPhysical())
                            <div class="product-stock">
                                @if($product->stock > 5)
                                    <span style="color: #27ae60;">&#x2713; {{ $product->stock }} in stock</span>
                                @elseif($product->stock > 0)
                                    <span style="color: #f39c12;">&#x26A0; Only {{ $product->stock }} left</span>
                                @else
                                    <span style="color: #e74c3c;">&#x2717; Out of stock</span>
                                @endif
                            </div>
                        @endif
                        <a href="{{ route('marketplace.product', $product->slug) }}" class="product-link">View Details &#x2192;</a>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1/-1; text-align: center; color: #7f8c8d;">No products found</p>
            @endforelse
        </div>

        @if($products->hasPages())
            <div class="pagination">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</body>
</html>