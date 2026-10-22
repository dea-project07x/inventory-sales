<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Seluruh business logic penjualan ada di sini (bukan di Blade / controller).
 */
class SaleService
{
    /**
     * Buat penjualan. Header, item, dan pengurangan stok berada dalam SATU
     * database transaction: jika satu item gagal, semuanya di-rollback.
     *
     * @param  array<int, array{product_id: int|string, quantity: int|string}>  $items
     */
    public function create(User $user, array $items): Sale
    {
        $lines = $this->normalizeItems($items);

        return DB::transaction(function () use ($user, $lines) {
            $sale = Sale::create([
                'user_id' => $user->id,
                'sale_number' => Sale::generateNumber(),
                'total' => 0,
                'status' => Sale::STATUS_COMPLETED,
                'sold_at' => now(),
            ]);

            $total = 0.0;

            foreach ($lines as $line) {
                // lockForUpdate: transaksi lain pada produk yang sama menunggu,
                // sehingga tidak terjadi overselling pada transaksi bersamaan.
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);
                $qty = $line['quantity'];

                if ($qty < 1 || $product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok \"{$product->name}\" tidak cukup (tersedia {$product->stock}, diminta {$qty}).",
                    ]);
                }

                // Harga SELALU diambil dari database, bukan dari input browser.
                $unitPrice = (float) $product->selling_price;
                $subtotal = round($unitPrice * $qty, 2);

                $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);
                $total += $subtotal;
            }

            $sale->update(['total' => round($total, 2)]);

            return $sale;
        });
    }

    /**
     * Batalkan penjualan dan pulihkan stok secara konsisten.
     */
    public function cancel(Sale $sale, User $user): Sale
    {
        return DB::transaction(function () use ($sale, $user) {
            $locked = Sale::lockForUpdate()->findOrFail($sale->id);

            if (! $locked->isCompleted()) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah dibatalkan sebelumnya.',
                ]);
            }

            foreach ($locked->items()->orderBy('product_id')->get() as $item) {
                Product::lockForUpdate()->findOrFail($item->product_id)
                    ->increment('stock', $item->quantity);
            }

            $locked->update([
                'status' => Sale::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
            ]);

            return $locked;
        });
    }

    /**
     * Gabungkan baris produk yang sama dan urutkan berdasarkan product_id
     * (urutan konsisten mencegah deadlock antar transaksi bersamaan).
     */
    private function normalizeItems(array $items): array
    {
        return collect($items)
            ->groupBy(fn ($i) => (int) $i['product_id'])
            ->map(fn ($group, $productId) => [
                'product_id' => (int) $productId,
                'quantity' => (int) $group->sum(fn ($i) => (int) $i['quantity']),
            ])
            ->sortKeys()
            ->values()
            ->all();
    }
}
