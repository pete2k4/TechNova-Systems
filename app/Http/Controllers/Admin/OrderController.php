<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->with(['user', 'items.product']);

        if ($request->filled('q')) {
            $search = trim((string) $request->string('q'));
            $query->where(function ($inner) use ($search): void {
                $inner->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->string('payment_method'));
        }

        $orders = $query->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->query());

        return view('admin.orders.index', [
            'orders' => $orders,
            'filters' => $request->all(),
        ]);
    }
}
