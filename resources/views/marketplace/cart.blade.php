<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - NovaTech</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        .navbar { background: white; padding: 20px 40px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .navbar-content { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar a { text-decoration: none; color: #333; font-weight: 600; margin: 0 20px; }
        .navbar a:hover { color: #3498db; }
        .cart-badge { background: #e74c3c; color: white; padding: 4px 8px; border-radius: 12px; font-size: 12px; margin-left: 5px; }
        .container { max-width: 1000px; margin: 0 auto; padding: 40px 20px; }
        h1 { margin-bottom: 30px; color: #2c3e50; }
        .cart-empty { text-align: center; padding: 60px 20px; }
        .cart-empty p { color: #7f8c8d; margin-bottom: 20px; font-size: 18px; }
        .cart-empty a { color: #3498db; text-decoration: none; font-weight: 600; }
        .cart-table { width: 100%; background: white; border-collapse: collapse; margin-bottom: 30px; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .cart-table th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #ecf0f1; }
        .cart-table td { padding: 15px; border-bottom: 1px solid #ecf0f1; }
        .cart-table tr:last-child td { border-bottom: none; }
        .product-column { display: flex; align-items: center; gap: 15px; }
        .product-icon { font-size: 24px; }
        .remove-btn { background: #e74c3c; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; transition: all 0.2s; }
        .remove-btn:hover { background: #c0392b; }
        .summary { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ecf0f1; }
        .summary-row:last-child { border-bottom: none; }
        .summary-row.total { font-size: 18px; font-weight: bold; color: #27ae60; }
        .action-buttons { display: flex; gap: 10px; margin-top: 25px; }
        button, a { padding: 12px 20px; border: none; border-radius: 4px; text-decoration: none; font-weight: 600; cursor: pointer; transition: all 0.2s; font-size: 14px; }
        .btn-checkout { background: #27ae60; color: white; flex: 1; }
        .btn-checkout:hover { background: #229954; }
        .btn-continue { background: #95a5a6; color: white; flex: 1; }
        .btn-continue:hover { background: #7f8c8d; }
        .btn-clear { background: #bdc3c7; color: white; }
        .btn-clear:hover { background: #95a5a6; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
        <h1>🛒 Shopping Cart</h1>

        @if($message = session('success'))
            <div class="message success">{{ $message }}</div>
        @endif

        @if(empty($cart))
            <div class="cart-empty">
                <p style="font-size: 48px; margin-bottom: 20px;">🛒</p>
                <p>Your cart is empty</p>
                <a href="{{ route('marketplace.index') }}" style="display: inline-block; margin-top: 20px;">← Continue Shopping</a>
            </div>
        @else
            <table class="cart-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 15%;">Price</th>
                        <th style="width: 15%;">Quantity</th>
                        <th style="width: 15%;">Total</th>
                        <th style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $productId => $item)
                        <tr>
                            <td>
                                <div class="product-column">
                                    <div class="product-icon">
                                        @if($item['type'] === 'digital') 💾 @else 🖥️ @endif
                                    </div>
                                    <div>
                                        <strong>{{ $item['name'] }}</strong>
                                        <div style="font-size: 12px; color: #7f8c8d; margin-top: 4px;">
                                            {{ $item['type'] === 'digital' ? 'Digital' : 'Physical' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>${{ number_format($item['price'], 2) }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td><strong>${{ number_format($item['price'] * $item['quantity'], 2) }}</strong></td>
                            <td>
                                <form method="POST" action="{{ route('marketplace.removeFromCart', $productId) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span>Calculated at checkout</span>
                </div>
                <div class="summary-row total">
                    <span>Estimated Total</span>
                    <span>${{ number_format($subtotal, 2) }}</span>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('marketplace.index') }}" class="btn-continue">← Continue Shopping</a>
                    <a href="{{ route('checkout.show-checkout') }}" class="btn-checkout">Proceed to Checkout →</a>
                </div>

                <div style="margin-top: 20px;">
                    <form method="POST" action="{{ route('marketplace.clearCart') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-clear">Clear Cart</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
