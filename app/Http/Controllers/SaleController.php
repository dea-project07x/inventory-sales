<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\TransactionFilterRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SaleController extends Controller
{
    public function __construct(private SaleService $sales)
    {
    }

    public function index(TransactionFilterRequest $request)
    {
        $f = $request->validated();
        $user = $request->user();

        $sales = Sale::with('user')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where('sale_number', 'like', '%' . $v . '%'))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->whereDate('sold_at', '>=', $v))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->whereDate('sold_at', '<=', $v))
            ->latest('sold_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')
            ->get(['id', 'sku', 'name', 'stock', 'purchase_price', 'selling_price']);

        return view('sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = $this->sales->create($request->user(), $request->validated('items'));

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan berhasil disimpan dan stok diperbarui.');
    }

    public function show(Sale $sale)
    {
        Gate::authorize('view-transaction', $sale);
        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }

    public function cancel(Sale $sale): RedirectResponse
    {
        $this->sales->cancel($sale, auth()->user());

        return redirect()->route('sales.show', $sale)->with('success', 'Penjualan dibatalkan dan stok dipulihkan.');
    }
}
