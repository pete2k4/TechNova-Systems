<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::query()
            ->withCount('products')
            ->orderByDesc('created_at')
            ->paginate(15);

        $products = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);

        return view('admin.discounts.index', [
            'discounts' => $discounts,
            'products' => $products,
        ]);
    }

    public function create()
    {
        $products = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);

        return view('admin.discounts.create', ['products' => $products]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:percentage,fixed'],
            'category' => ['required', 'in:high,low'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
            'is_automatic' => ['nullable', 'boolean'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $discount = Discount::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'category' => $validated['category'],
            'amount' => $validated['amount'],
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
            'is_automatic' => (bool) ($validated['is_automatic'] ?? false),
        ]);

        if (!empty($validated['product_ids'])) {
            $payload = [];
            $appliedAt = $discount->is_active ? now()->toDateTimeString() : null;

            foreach ($validated['product_ids'] as $productId) {
                $payload[$productId] = ['applied_at' => $appliedAt];
            }

            $discount->products()->sync($payload);
        }

        return redirect()
            ->route('admin.discounts.index')
            ->with('status', 'Discount created successfully.');
    }

    public function apply(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $discount->forceFill([
            'is_active' => true,
            'starts_at' => $discount->starts_at && $discount->starts_at->isFuture() ? now() : $discount->starts_at,
        ])->save();

        $payload = [];
        foreach ($validated['product_ids'] as $productId) {
            $payload[$productId] = ['applied_at' => now()->toDateTimeString()];
        }

        $discount->products()->syncWithoutDetaching($payload);

        return redirect()
            ->route('admin.discounts.index')
            ->with('status', 'Discount applied to selected products.');
    }

    public function destroy(Discount $discount)
    {
        $discountName = $discount->name;
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('status', "Discount '{$discountName}' deleted successfully. All product associations have been removed.");
    }

    public function runSchedule()
    {
        Artisan::call('discounts:activate-scheduled');

        return redirect()
            ->route('admin.discounts.index')
            ->with('status', trim(Artisan::output()) ?: 'Scheduled discounts command ran successfully.');
    }
}
