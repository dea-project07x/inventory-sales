<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\TransactionFilterRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchases)
    {
    }

    public function index(TransactionFilterRequest $request)
    {
        $f = $request->validated();
        $user = $request->user();

        $purchases = Purchase::with(['supplier', 'user'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where('purchase_number', 'like', '%' . $v . '%'))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->whereDate('purchased_at', '>=', $v))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->whereDate('purchased_at', '<=', $v))
            ->latest('purchased_at')->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $products = Product::orderBy('name')
            ->get(['id', 'sku', 'name', 'stock', 'purchase_price', 'selling_price']);
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.create', compact('products', 'suppliers'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $purchase = $this->purchases->create(
            $request->user(),
            (int) $request->validated('supplier_id'),
            $request->validated('items')
        );

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian berhasil disimpan dan stok bertambah.');
    }

    public function show(Purchase $purchase)
    {
        Gate::authorize('view-transaction', $purchase);
        $purchase->load(['supplier', 'user', 'items.product']);

        return view('purchases.show', compact('purchase'));
    }

    public function cancel(Purchase $purchase): RedirectResponse
    {
        $this->purchases->cancel($purchase, auth()->user());

        return redirect()->route('purchases.show', $purchase)->with('success', 'Pembelian dibatalkan dan stok dikurangi kembali.');
    }
}
