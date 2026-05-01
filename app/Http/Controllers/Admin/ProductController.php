<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with([
            'category',
            'discounts' => fn ($discounts) => $discounts->active(),
        ]);

        if ($request->filled('q')) {
            $search = trim((string) $request->string('q'));
            $query->where(function ($inner) use ($search): void {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->filled('stock_status')) {
            $stockStatus = (string) $request->input('stock_status');

            if ($stockStatus === 'in_stock') {
                $query->where(function ($inner): void {
                    $inner->where('type', 'digital')->orWhere('stock', '>', 0);
                });
            }

            if ($stockStatus === 'out_of_stock') {
                $query->where('type', 'physical')->where('stock', '<=', 0);
            }

            if ($stockStatus === 'low_stock') {
                $query->where('type', 'physical')->whereBetween('stock', [1, 5]);
            }
        }

        $allowedSorts = ['name', 'price', 'stock', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'created_at';
        $direction = $request->input('direction') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $direction);

        $products = $query->paginate(15)->appends($request->query());
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('admin.products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->all(),
        ]);
    }
}
