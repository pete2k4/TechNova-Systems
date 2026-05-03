<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Products</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f6f8fb; color: #223; }
        .wrap { max-width: 1280px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(25, 35, 55, 0.08); }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .grid input, .grid select, .grid button { padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ebeff7; font-size: 14px; vertical-align: top; }
        th { background: #f1f5fc; }
        .chips { display: flex; gap: 6px; flex-wrap: wrap; }
        .chip { background: #e8f0ff; color: #1746a2; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .nav a { color: #1746a2; text-decoration: none; margin-right: 12px; }
        .btn { display:inline-block; padding:8px 12px; border-radius:8px; text-decoration:none; border:1px solid #1746a2; background:#fff; color:#1746a2; }
        .btn.primary { background:#1746a2; color:#fff; }
        .btn.danger { border-color:#b42318; color:#b42318; }
        .actions { display:flex; gap:8px; align-items:center; }
        .thumb { width: 64px; height: 64px; object-fit: cover; border: 1px solid #d8deeb; border-radius: 8px; }
        .thumb-empty { width: 64px; height: 64px; border: 1px dashed #d8deeb; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:11px; }
    </style>
</head>
<body>
<div class="wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0;">Admin Dashboard</h1>
        <a href="{{ route('home') }}" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">← Back to Store</a>
    </div>

    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('admin.products.index') }}" style="background: #3498db; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;">📦</span>
            <span>Products</span>
        </a>
        <a href="{{ route('admin.discounts.index') }}" style="background: #9b59b6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;">🏷️</span>
            <span>Discounts</span>
        </a>
    </div>

    <h2 style="margin-top: 0;">Products</h2>

    @if(session('status'))
        <div class="card" style="background:#edf7ed; border:1px solid #cde8cd; margin-bottom:12px;">{{ session('status') }}</div>
    @endif

    <div style="margin-bottom:12px;">
        <a class="btn primary" href="{{ route('admin.products.create') }}">Create Product</a>
    </div>

    <div class="card">
        <form method="GET" class="grid">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search name or SKU">
            <select name="type">
                <option value="">All types</option>
                <option value="digital" @selected(($filters['type'] ?? '') === 'digital')>Digital</option>
                <option value="physical" @selected(($filters['type'] ?? '') === 'physical')>Physical</option>
            </select>
            <select name="category_id">
                <option value="">All categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" @selected((string)($filters['category_id'] ?? '') === (string)$category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="stock_status">
                <option value="">All stock</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="low_stock" @selected(($filters['stock_status'] ?? '') === 'low_stock')>Low stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <input type="number" step="0.01" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min price">
            <input type="number" step="0.01" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max price">
            <select name="sort">
                <option value="created_at" @selected(($filters['sort'] ?? 'created_at') === 'created_at')>Sort by newest</option>
                <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Sort by name</option>
                <option value="price" @selected(($filters['sort'] ?? '') === 'price')>Sort by price</option>
                <option value="stock" @selected(($filters['sort'] ?? '') === 'stock')>Sort by stock</option>
            </select>
            <select name="direction">
                <option value="desc" @selected(($filters['direction'] ?? 'desc') === 'desc')>Desc</option>
                <option value="asc" @selected(($filters['direction'] ?? '') === 'asc')>Asc</option>
            </select>
            <button type="submit">Filter and Sort</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Discounts</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($products as $product)
                <tr>
                    <td>
                        @if(!empty($product->image_url))
                            <img class="thumb" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="thumb-empty">No image</div>
                        @endif
                    </td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ ucfirst($product->type) }}</td>
                    <td>${{ number_format((float) $product->price, 2) }}</td>
                    <td>{{ $product->type === 'digital' ? 'N/A' : (int) ($product->stock ?? 0) }}</td>
                    <td>
                        <div class="chips">
                            @forelse($product->discounts as $discount)
                                <span class="chip">
                                    {{ $discount->name }}
                                    ({{ $discount->type === 'percentage' ? ((float)$discount->amount)."%" : "$".number_format((float)$discount->amount, 2) }})
                                </span>
                            @empty
                                <span style="color:#6b7280;">No active discounts</span>
                            @endforelse
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <a class="btn" href="{{ route('admin.products.edit', $product) }}">Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn danger">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No products found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">{{ $products->links() }}</div>
    </div>
</div>
</body>
</html>
