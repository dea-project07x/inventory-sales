<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Admin melihat angka seluruh sistem; Staff hanya transaksi miliknya.
        $sales = fn () => Sale::completed()->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));
        $purchases = fn () => Purchase::completed()->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'total_products' => Product::count(),
            'sales_today' => $sales()->whereDate('sold_at', today())->sum('total'),
            'sales_month' => $sales()->whereBetween('sold_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'sales_count' => $sales()->count(),
            'purchases_count' => $purchases()->count(),
            'low_stock_count' => Product::lowStock()->count(),
        ];

        $lowStock = Product::with('category')->lowStock()->orderBy('stock')->limit(5)->get();
        $recentSales = Sale::with('user')
            ->when(! $user->isAdmin(), fn ($q) => $q->where('user_id', $user->id))
            ->latest('sold_at')->limit(5)->get();

        return view('dashboard', compact('stats', 'lowStock', 'recentSales'));
    }
}
