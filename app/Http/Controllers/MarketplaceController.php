<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    /**
     * Display marketplace homepage with featured products.
     */
    public function index(): View
    {
        $categories = Category::all();
        $products = Product::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('marketplace.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Display products in a specific category.
     */
    public function category(string $slug): View
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = $category->products()
            ->where('is_active', true)
            ->paginate(12);

        $categories = Category::all();

        return view('marketplace.category', [
            'category' => $category,
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Display single product details.
     */
    public function show(string $slug): View
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $relatedProducts = $product->category->products()
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('marketplace.product-detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($productId);

        if ($product->isPhysical() && $product->stock < $request->quantity) {
            return back()->with('error', 'Insufficient stock available');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $request->quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'type' => $product->type,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('marketplace.cart')
            ->with('success', $product->name . ' added to cart');
    }

    /**
     * Display shopping cart.
     */
    public function cart(): View
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('marketplace.cart', [
            'cart' => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);

        return back()->with('success', 'Item removed from cart');
    }

    /**
     * Clear entire cart.
     */
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('marketplace.index')
            ->with('success', 'Cart cleared');
    }
}
