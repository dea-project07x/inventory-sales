<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $products = Product::with('category') // eager loading: hindari N+1
            ->search($filters['q'] ?? null)
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString(); // filter tetap terbawa saat pindah halaman

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function lowStock()
    {
        $products = Product::with('category')->lowStock()->orderBy('stock')->paginate(10);

        return view('products.low-stock', compact('products'));
    }

    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    public function create()
    {
        return view('products.create', [
            'product' => new Product(['stock' => 0, 'minimum_stock' => 5]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        return redirect()->route('products.show', $product)->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Produk yang sudah dipakai transaksi tidak boleh dihapus (histori harus utuh).
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->with('error', 'Produk sudah dipakai dalam transaksi dan tidak dapat dihapus.');
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
