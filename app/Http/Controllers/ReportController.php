<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Riwayat pergerakan stok + laporan ringkas (khusus Admin).
 * Riwayat dibentuk dari tabel item transaksi yang sudah ada (tanpa tabel baru):
 *  - purchase_items  => stok MASUK
 *  - sale_items      => stok KELUAR
 */
class ReportController extends Controller
{
    public function index(Request $request)
    {
        $f = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'direction' => ['nullable', 'in:in,out'],
            'status' => ['nullable', 'in:completed,cancelled'],
        ]);

        $from = $f['from'] ?? null;
        $to = $f['to'] ?? null;

        // ---- Riwayat pergerakan stok (gabungan masuk + keluar) ----
        $in = DB::table('purchase_items as i')
            ->join('purchases as t', 't.id', '=', 'i.purchase_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->selectRaw("'in' as direction, t.purchased_at as occurred_at, t.purchase_number as trx_number, "
                . 't.status as status, i.product_id as product_id, p.sku as sku, p.name as product_name, '
                . 'i.quantity as quantity, i.unit_cost as unit_price, i.subtotal as subtotal, '
                . 'u.name as actor, t.id as transaction_id');

        $out = DB::table('sale_items as i')
            ->join('sales as t', 't.id', '=', 'i.sale_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->join('users as u', 'u.id', '=', 't.user_id')
            ->selectRaw("'out' as direction, t.sold_at as occurred_at, t.sale_number as trx_number, "
                . 't.status as status, i.product_id as product_id, p.sku as sku, p.name as product_name, '
                . 'i.quantity as quantity, i.unit_price as unit_price, i.subtotal as subtotal, '
                . 'u.name as actor, t.id as transaction_id');

        $movements = DB::query()
            ->fromSub($in->unionAll($out), 'm')
            ->when($f['product_id'] ?? null, fn ($q, $v) => $q->where('product_id', $v))
            ->when($f['direction'] ?? null, fn ($q, $v) => $q->where('direction', $v))
            ->when($f['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($from, fn ($q, $v) => $q->whereDate('occurred_at', '>=', $v))
            ->when($to, fn ($q, $v) => $q->whereDate('occurred_at', '<=', $v))
            ->orderByDesc('occurred_at')
            ->orderByDesc('transaction_id')
            ->paginate(15)
            ->withQueryString();

        // ---- Ringkasan periode (hanya transaksi berstatus selesai) ----
        $salesSummary = Sale::completed()
            ->when($from, fn ($q, $v) => $q->whereDate('sold_at', '>=', $v))
            ->when($to, fn ($q, $v) => $q->whereDate('sold_at', '<=', $v))
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(total), 0) as t')
            ->first();

        $purchaseSummary = Purchase::completed()
            ->when($from, fn ($q, $v) => $q->whereDate('purchased_at', '>=', $v))
            ->when($to, fn ($q, $v) => $q->whereDate('purchased_at', '<=', $v))
            ->selectRaw('COUNT(*) as c, COALESCE(SUM(total), 0) as t')
            ->first();

        // ---- 5 produk terlaris pada periode ----
        $topProducts = DB::table('sale_items as i')
            ->join('sales as s', 's.id', '=', 'i.sale_id')
            ->join('products as p', 'p.id', '=', 'i.product_id')
            ->where('s.status', Sale::STATUS_COMPLETED)
            ->when($from, fn ($q, $v) => $q->whereDate('s.sold_at', '>=', $v))
            ->when($to, fn ($q, $v) => $q->whereDate('s.sold_at', '<=', $v))
            ->selectRaw('p.sku, p.name, SUM(i.quantity) as qty, SUM(i.subtotal) as revenue')
            ->groupBy('p.id', 'p.sku', 'p.name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $products = Product::orderBy('name')->get(['id', 'sku', 'name']);

        return view('reports.index', compact('movements', 'salesSummary', 'purchaseSummary', 'topProducts', 'products'));
    }
}
