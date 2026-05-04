<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, sans-serif; margin: 0; background: #f6f8fb; color: #223; }
        .wrap { max-width: 1280px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 2px 10px rgba(25, 35, 55, 0.08); margin-bottom: 16px; }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .grid input, .grid select, .grid button { padding: 10px; border: 1px solid #d8deeb; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #ebeff7; font-size: 14px; vertical-align: top; }
        th { background: #f1f5fc; }
        .chips { display: flex; gap: 6px; flex-wrap: wrap; }
        .chip { background: #e8f0ff; color: #1746a2; padding: 4px 8px; border-radius: 999px; font-size: 12px; }
        .muted { color: #6b7280; font-size: 12px; }
    </style>
</head>
<body>
<div class="wrap">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="margin: 0;">Admin Dashboard</h1>
        <a href="{{ route('home') }}" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block;">Back to Store</a>
    </div>

    <div style="display: flex; gap: 12px; margin-bottom: 24px;">
        <a href="{{ route('admin.products.index') }}" style="background: #3498db; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span>Products</span>
        </a>
        <a href="{{ route('admin.discounts.index') }}" style="background: #9b59b6; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span>Discounts</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" style="background: #1f2937; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <span>Orders</span>
        </a>
    </div>

    <h2 style="margin-top: 0;">Orders</h2>

    <div class="card">
        <form method="GET" class="grid">
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search order number or customer">
            <select name="status">
                <option value="">All statuses</option>
                @foreach(['checkout_started','pending_payment_page','placed','canceled','completed','failed','refunded'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                @endforeach
            </select>
            <select name="payment_method">
                <option value="">All payment methods</option>
                @foreach(['credit_card','paypal','on_delivery'] as $method)
                    <option value="{{ $method }}" @selected(($filters['payment_method'] ?? '') === $method)>{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
                @endforeach
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>
                        <strong>{{ $order->order_number }}</strong>
                        <div class="muted">#{{ $order->id }}</div>
                    </td>
                    <td>
                        <div>{{ $order->user->name ?? 'Unknown' }}</div>
                        <div class="muted">{{ $order->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div class="chips">
                            @foreach($order->items as $item)
                                <span class="chip">{{ $item->product->name }} x{{ $item->quantity }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td>${{ number_format($order->total, 2) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No orders found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">{{ $orders->links() }}</div>
    </div>
</div>
</body>
</html>
