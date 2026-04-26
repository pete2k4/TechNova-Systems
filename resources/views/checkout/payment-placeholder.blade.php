<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Placeholder - NovaTech</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(130deg, #f0f6ff, #e8fff6);
            color: #1f2937;
            padding: 24px;
        }

        .card {
            width: min(720px, 100%);
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.1);
            padding: 28px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 28px;
        }

        p {
            margin: 8px 0;
            line-height: 1.55;
        }

        .badge {
            display: inline-block;
            margin-top: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #eef2ff;
            color: #312e81;
            font-weight: 600;
            font-size: 14px;
        }

        .actions {
            margin-top: 24px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }
    </style>
</head>
<body>
    <main class="card">
        <h1>Still working on payment integration</h1>
        <p>This page intentionally simulates the external payment provider redirect required by the project constraints.</p>
        <p>No real payment is processed here.</p>
        <p class="badge">Order Ref: {{ request()->route('orderId') }}</p>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('marketplace.index') }}">Back to marketplace</a>
            <a class="btn btn-secondary" href="{{ route('checkout.show-checkout') }}">Start new checkout</a>
        </div>
    </main>
</body>
</html>
