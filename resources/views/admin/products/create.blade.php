<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f6f8fb; color: #223; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(25, 35, 55, 0.08); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .full { grid-column: 1 / -1; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; }
        .checkbox-group { display: grid; gap: 10px; margin-top: 10px; }
        .checkbox-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; background: #fbfcff; }
        .checkbox-item input { width: auto; margin-top: 3px; }
        .checkbox-title { font-weight: 600; }
        .checkbox-meta { color: #6b7280; font-size: 13px; margin-top: 3px; }
        button { padding: 10px 14px; border: 1px solid #1746a2; border-radius: 8px; background: #1746a2; color: #fff; cursor: pointer; }
        .error { background: #fff1f1; border: 1px solid #ffd6d6; color: #8a1f1f; padding: 12px; border-radius: 8px; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <h1 style="margin-top:0;">Create Product</h1>

    @if($errors->any())
        <div class="error">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('admin.products.store') }}" class="grid">
            @csrf
            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div>
                <label>SKU</label>
                <input type="text" name="sku" value="{{ old('sku') }}" required>
            </div>

            <div>
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string)old('category_id') === (string)$category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Type</label>
                <select name="type" required>
                    <option value="digital" @selected(old('type') === 'digital')>Digital</option>
                    <option value="physical" @selected(old('type') === 'physical')>Physical</option>
                </select>
            </div>

            <div>
                <label>Price</label>
                <input type="number" name="price" step="0.01" min="0.01" value="{{ old('price') }}" required>
            </div>

            <div>
                <label>Stock (for physical)</label>
                <input type="number" name="stock" min="0" value="{{ old('stock') }}">
            </div>

            <div class="full">
                <label>Image URL (link de pe internet, optional)</label>
                <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg">
            </div>

            <div class="full">
                <label>Slug (optional, auto-generated if empty)</label>
                <input type="text" name="slug" value="{{ old('slug') }}">
            </div>

            <div class="full">
                <label>Description</label>
                <textarea name="description" rows="5" required>{{ old('description') }}</textarea>
            </div>

            <div class="full">
                <label>Applicable Discounts</label>
                @php
                    $selectedDiscountIds = array_map('strval', old('discount_ids', []));
                @endphp
                <div class="checkbox-group">
                    @foreach($discounts as $discount)
                        <label class="checkbox-item">
                            <input type="checkbox" name="discount_ids[]" value="{{ $discount->id }}" @checked(in_array((string) $discount->id, $selectedDiscountIds, true))>
                            <span>
                                <span class="checkbox-title">{{ $discount->name }}</span>
                                <div class="checkbox-meta">{{ ucfirst($discount->type) }}: {{ $discount->type === 'percentage' ? ((float) $discount->amount).'%' : '$'.number_format((float) $discount->amount, 2) }}</div>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="full">
                <label>Active?</label>
                <select name="is_active">
                    <option value="1" @selected(old('is_active', '1') === '1')>Yes</option>
                    <option value="0" @selected(old('is_active') === '0')>No</option>
                </select>
            </div>

            <div class="full" style="display:flex; gap:10px;">
                <button type="submit">Create Product</button>
                <a href="{{ route('admin.products.index') }}" style="padding:10px 14px; border:1px solid #d1d9ea; border-radius:8px; text-decoration:none; color:#223;">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
