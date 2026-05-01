<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Discount</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f6f8fb; color: #223; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(25, 35, 55, 0.08); }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .full { grid-column: 1 / -1; }
        input, select { width: 100%; padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; }
        button { padding: 10px 14px; border: 1px solid #1746a2; border-radius: 8px; background: #1746a2; color: #fff; cursor: pointer; }
        .error { background: #fff1f1; border: 1px solid #ffd6d6; color: #8a1f1f; padding: 12px; border-radius: 8px; margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <h1 style="margin-top:0;">Create Discount</h1>

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
        <form method="POST" action="{{ route('admin.discounts.store') }}" class="grid">
            @csrf
            <div>
                <label>Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
            </div>

            <div>
                <label>Type</label>
                <select name="type" required>
                    <option value="percentage" @selected(old('type') === 'percentage')>Percentage</option>
                    <option value="fixed" @selected(old('type') === 'fixed')>Fixed</option>
                </select>
            </div>

            <div>
                <label>Category</label>
                <select name="category" required>
                    <option value="high" @selected(old('category', 'high') === 'high')>High (applied from base price)</option>
                    <option value="low" @selected(old('category') === 'low')>Low (applied after High discounts)</option>
                </select>
            </div>

            <div>
                <label>Amount</label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required>
            </div>

            <div>
                <label>Automatic by Schedule?</label>
                <select name="is_automatic">
                    <option value="0" @selected(old('is_automatic', '0') === '0')>No (manual)</option>
                    <option value="1" @selected(old('is_automatic') === '1')>Yes (time-based)</option>
                </select>
            </div>

            <div>
                <label>Starts At</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}">
            </div>

            <div>
                <label>Ends At</label>
                <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}">
            </div>

            <div class="full">
                <label>Products (optional, multiple)</label>
                <select name="product_ids[]" multiple size="10">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                    @endforeach
                </select>
            </div>

            <div class="full">
                <label>Activate Immediately?</label>
                <select name="is_active">
                    <option value="0" @selected(old('is_active', '0') === '0')>No</option>
                    <option value="1" @selected(old('is_active') === '1')>Yes</option>
                </select>
            </div>

            <div class="full" style="display:flex; gap:10px;">
                <button type="submit">Create Discount</button>
                <a href="{{ route('admin.discounts.index') }}" style="padding:10px 14px; border:1px solid #d1d9ea; border-radius:8px; text-decoration:none; color:#223;">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
