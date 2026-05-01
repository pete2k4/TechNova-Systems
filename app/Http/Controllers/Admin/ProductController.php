<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct()
    {
        $user = request()->getUser();
        $pass = request()->getPassword();
        $envUser = env('ADMIN_USER', 'admin');
        $envPass = env('ADMIN_PASS', 'password');

        if (! ($user === $envUser && $pass === $envPass)) {
            abort(response('Unauthorized', 401, ['WWW-Authenticate' => 'Basic realm="Admin Area"']));
        }
    }

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

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $discounts = Discount::query()->orderBy('name')->get(['id', 'name', 'type', 'amount']);

        return view('admin.products.create', [
            'categories' => $categories,
            'discounts' => $discounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:digital,physical'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'discount_ids' => ['nullable', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
        ]);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = $this->generateUniqueSlug($validated['name']);
        }

        $product = Product::query()->create([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
            'price' => $validated['price'],
            'type' => $validated['type'],
            'sku' => $validated['sku'],
            'stock' => $validated['type'] === 'physical' ? ($validated['stock'] ?? 0) : null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $this->syncProductDiscounts($product, $validated['discount_ids'] ?? []);

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);
        $discounts = Discount::query()->orderBy('name')->get(['id', 'name', 'type', 'amount']);

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories,
            'discounts' => $discounts,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,'.$product->id],
            'description' => ['required', 'string'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:digital,physical'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,'.$product->id],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'discount_ids' => ['nullable', 'array'],
            'discount_ids.*' => ['integer', 'exists:discounts,id'],
        ]);

        $slug = trim((string) ($validated['slug'] ?? ''));
        if ($slug === '') {
            $slug = $this->generateUniqueSlug($validated['name'], $product->id);
        }

        $product->update([
            'category_id' => (int) $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'image_url' => $validated['image_url'] ?? null,
            'price' => $validated['price'],
            'type' => $validated['type'],
            'sku' => $validated['sku'],
            'stock' => $validated['type'] === 'physical' ? ($validated['stock'] ?? 0) : null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        $this->syncProductDiscounts($product, $validated['discount_ids'] ?? []);

        return redirect()->route('admin.products.index')->with('status', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->discounts()->detach();
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully.');
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::query()
            ->when($ignoreId !== null, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $counter++;
            $slug = $baseSlug.'-'.$counter;
        }

        return $slug;
    }

    /**
     * @param array<int, int> $discountIds
     */
    private function syncProductDiscounts(Product $product, array $discountIds): void
    {
        $payload = [];
        $selectedDiscountIds = [];

        foreach (array_unique($discountIds) as $discountId) {
            $discountId = (int) $discountId;
            $selectedDiscountIds[] = $discountId;
            $payload[$discountId] = [
                'applied_at' => now()->toDateTimeString(),
            ];
        }

        if (!empty($selectedDiscountIds)) {
            Discount::query()
                ->whereIn('id', $selectedDiscountIds)
                ->get()
                ->each(function (Discount $discount): void {
                    $discount->forceFill([
                        'is_active' => true,
                        'starts_at' => $discount->starts_at && $discount->starts_at->isFuture() ? now() : $discount->starts_at,
                    ])->save();
                });
        }

        $product->discounts()->sync($payload);
    }
}