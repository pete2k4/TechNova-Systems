@extends('layouts.app')

@section('title', 'My Orders - TechNova')

@section('content')
    <style>
        .orders-container { max-width: 1100px; margin: 0 auto; padding: 40px 20px; }
        .orders-card { background: #fff; border-radius: 10px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        h1 { font-size: 28px; color: #2c3e50; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ecf0f1; text-align: left; font-size: 14px; vertical-align: top; }
        th { background: #f8f9fa; color: #2c3e50; font-weight: 600; }
        .muted { color: #7f8c8d; font-size: 12px; }
        .status { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 12px; background: #eef2f7; color: #334155; }
        .empty { text-align: center; padding: 40px 0; color: #7f8c8d; }
        .btn-primary { display: inline-block; margin-top: 12px; padding: 10px 16px; background: #3498db; color: #fff; text-decoration: none; border-radius: 6px; }
    </style>

    <div class="orders-container">
        <div class="orders-card">
            <h1>My Orders</h1>

            @if($orders->isEmpty())
                <div class="empty">
                    <p>You have not placed any orders yet.</p>
                    <a href="{{ route('marketplace.index') }}" class="btn-primary">Browse products</a>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong>{{ $order->order_number }}</strong>
                                </td>
                                <td>
                                    {{ $order->created_at->format('Y-m-d H:i') }}
                                </td>
                                <td>
                                    <div class="muted">
                                        @foreach($order->items as $item)
                                            <div>{{ $item->product->name }} (x{{ $item->quantity }})</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td><span class="status">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top: 16px;">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
