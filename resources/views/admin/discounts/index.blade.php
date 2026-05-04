<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Discounts</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f6f8fb; color: #223; }
        .wrap { max-width: 1200px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(25, 35, 55, 0.08); margin-bottom: 16px; }
        .nav a { color: #1746a2; text-decoration: none; margin-right: 12px; }
        .actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { display: inline-block; padding: 10px 14px; border-radius: 8px; border: 1px solid #d1d9ea; background: #fff; cursor: pointer; }
        .btn.primary { background: #1746a2; color: #fff; border-color: #1746a2; text-decoration: none; }
                    .btn.danger { background: #dc2626; color: #fff; border-color: #dc2626; }
        .checkbox-group { display: grid; gap: 10px; margin-top: 10px; }
        .checkbox-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; background: #fbfcff; }
        .checkbox-item input { width: auto; margin-top: 3px; }
        .checkbox-title { font-weight: 600; }
        .checkbox-meta { color: #6b7280; font-size: 13px; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ebeff7; font-size: 14px; vertical-align: top; }
        th { background: #f1f5fc; }
        .muted { color: #6b7280; font-size: 13px; }
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
        <a href="{{ route('admin.orders.index') }}" style="background: #1f2937; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span style="font-size: 18px;">🧾</span>
            <span>Orders</span>
        </a>
    </div>

    <h2 style="margin-top: 0;">Discounts</h2>

    @if(session('status'))
        <div class="card" style="background:#edf7ed; border:1px solid #cde8cd;">{{ session('status') }}</div>
    @endif

    <div class="card">
        <div class="actions">
            <a class="btn primary" href="{{ route('admin.discounts.create') }}">Create Discount</a>
            <form method="POST" action="{{ route('admin.discounts.run-schedule') }}">
                @csrf
                <button type="submit" class="btn">Run Automatic Discount Schedule</button>
            </form>
        </div>
        <div class="muted" style="margin-top:10px;">Manual Apply activates a discount immediately on the selected products. Automatic discounts only start once their schedule window is reached.</div>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th>Products</th>
                        <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($discounts as $discount)
                <tr>
                    <td>
                        <strong>{{ $discount->name }}</strong>
                        <div class="muted">{{ ucfirst($discount->type) }}: {{ $discount->type === 'percentage' ? ((float)$discount->amount)."%" : "$".number_format((float)$discount->amount, 2) }}</div>
                    </td>
                    <td>
                        <div class="muted">Automatic: {{ $discount->is_automatic ? 'Yes' : 'No' }}</div>
                        <div class="muted">Start: {{ optional($discount->starts_at)->toDateTimeString() ?? 'N/A' }}</div>
                        <div class="muted">End: {{ optional($discount->ends_at)->toDateTimeString() ?? 'N/A' }}</div>
                    </td>
                    <td>{{ $discount->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $discount->products_count }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.discounts.destroy', $discount) }}" onsubmit="return confirm('Are you sure you want to delete this discount? All product associations will be removed.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                        <td colspan="5">No discounts yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">{{ $discounts->links() }}</div>
    </div>
</div>
</body>
</html>
