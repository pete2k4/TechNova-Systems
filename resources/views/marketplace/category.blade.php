@extends('layouts.app')

@section('title', $category->name . ' - NovaTech Marketplace')

@section('content')
    <style>
        .cat-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        .breadcrumb { margin-bottom: 30px; color: #7f8c8d; }
        .breadcrumb a { color: #3498db; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        h1 { margin-bottom: 10px; color: #2c3e50; }
        .category-description { color: #7f8c8d; margin-bottom: 30px; }
        .categories { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin: 20px 0 40px; }
        .category-card { background: white; padding: 16px; border-radius: 8px; text-align: center; text-decoration: none; color: #333; border: 2px solid transparent; transition: all 0.2s; font-weight: 600; }
        .category-icon { font-size: 22px; margin-bottom: 6px; }
        .category-card:hover { border-color: #3498db; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .category-card.active { border-color: #2c3e50; box-shadow: 0 4px 12px rgba(44, 62, 80, 0.15); }
        .products { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
        .product-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s; text-decoration: none; color: inherit; display: block; cursor: pointer; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 4px 16px rgba(0,0,0,0.15); }
        .product-media { background: #f0f0f0; height: 180px; display: flex; align-items: center; justify-content: center; }
        .product-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .product-media-fallback { font-size: 40px; }
        .product-info { padding: 15px; }
        .product-name { font-weight: 600; color: #2c3e50; margin-bottom: 8px; }
        .product-type-badge { display: inline-block; padding: 4px 8px; background: #ecf0f1; border-radius: 4px; font-size: 11px; font-weight: bold; margin-bottom: 10px; }
        .product-price { color: #27ae60; font-size: 20px; font-weight: bold; margin: 10px 0; }
        .product-stock { font-size: 12px; color: #7f8c8d; margin-bottom: 10px; }
        .product-link { display: inline-block; color: #3498db; text-decoration: none; font-size: 14px; font-weight: 600; }
        .product-link:hover { text-decoration: underline; }
    </style>

    <div class="cat-container">
        <div class="breadcrumb">
            <a href="{{ route('marketplace.index') }}">Home</a> / <strong>{{ $category->name }}</strong>
        </div>

        <h1>{{ $category->name }}</h1>
        <p class="category-description">{{ $category->description }}</p>

        <h3 style="margin: 10px 0 10px; color: #555;">Browse other categories</h3>
        <div class="categories">
            <a href="{{ route('marketplace.index') }}" class="category-card">
                <div class="category-icon">&#x1F31F;</div>
                <div>All Products</div>
            </a>
            @foreach($categories as $categoryItem)
                <a href="{{ route('marketplace.category', $categoryItem->slug) }}" class="category-card{{ $categoryItem->id === $category->id ? ' active' : '' }}">
                    <div class="category-icon">
                        @if($categoryItem->slug === 'software-licenses')
                            &#x1F4BF;
                        @elseif($categoryItem->slug === 'ebooks-courses')
                            &#x1F4DA;
                        @elseif($categoryItem->slug === 'computer-hardware')
                            &#x1F5A5;
                        @elseif($categoryItem->slug === 'networking-equipment')
                            &#x1F310;
                        @elseif($categoryItem->slug === 'accessories-peripherals')
                            &#x1F3A7;
                        @else
                            &#x1F527;
                        @endif
                    </div>
                    <div>{{ $categoryItem->name }}</div>
                </a>
            @endforeach
        </div>

        <div class="products">
            @forelse($products as $product)
                <a href="{{ route('marketplace.product', $product->slug) }}" class="product-card">
                    <div class="product-media">
                        @if(!empty($product->image_url))
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-media-fallback">@if($product->isDigital()) 💾 @else 🖥️ @endif</div>
                        @endif
                    </div>
                    <div class="product-info">
                        <div style="color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            {{ $product->name }}
                        </div>
                        <div style="margin-bottom: 10px;">
                            <span class="product-type-badge">
                                @if($product->isDigital()) DIGITAL @else PHYSICAL @endif
                            </span>
                        </div>
                        @if($product->discounted_price < $product->price)
                            <div style="display: inline-block; margin-bottom: 8px; padding: 4px 8px; border-radius: 999px; background: #fff0f0; color: #c0392b; font-size: 11px; font-weight: 700; text-transform: uppercase;">Discounted</div>
                            <div class="product-price">
                                <span style="color: #7f8c8d; text-decoration: line-through; font-size: 13px; font-weight: 500; margin-right: 8px;">${{ number_format($product->price, 2) }}</span>
                                <span style="color: #c0392b;">${{ number_format($product->discounted_price, 2) }}</span>
                            </div>
                        @else
                            <div class="product-price">${{ number_format($product->price, 2) }}</div>
                        @endif
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
                        <span class="product-link">View Details →</span>
                    </div>
                </a>
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
@endsection
