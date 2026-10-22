<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * Buat pembelian (stock-in). Stok bertambah hanya jika seluruh transaksi berhasil.
     *
     * @param  array<int, array{product_id: int|string, quantity: int|string, unit_cost: int|float|string}>  $items
     */
    public function create(User $user, int $supplierId, array $items): Purchase
    {
        $lines = collect($items)
            ->map(fn ($i) => [
                'product_id' => (int) $i['product_id'],
                'quantity' => (int) $i['quantity'],
                'unit_cost' => (float) $i['unit_cost'],
            ])
            ->sortBy('product_id')
            ->values()
            ->all();

        return DB::transaction(function () use ($user, $supplierId, $lines) {
            $purchase = Purchase::create([
                'supplier_id' => $supplierId,
                'user_id' => $user->id,
                'purchase_number' => Purchase::generateNumber(),
                'total' => 0,
                'status' => Purchase::STATUS_COMPLETED,
                'purchased_at' => now(),
            ]);

            $total = 0.0;

            foreach ($lines as $line) {
                if ($line['quantity'] < 1 || $line['unit_cost'] < 0) {
                    throw ValidationException::withMessages([
                        'items' => 'Kuantitas harus lebih dari 0 dan harga tidak boleh negatif.',
                    ]);
                }

                $product = Product::lockForUpdate()->findOrFail($line['product_id']);
                $subtotal = round($line['unit_cost'] * $line['quantity'], 2);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $line['quantity'],
                    'unit_cost' => $line['unit_cost'],
                    'subtotal' => $subtotal,
                ]);

                $product->increment('stock', $line['quantity']);
                $total += $subtotal;
            }

            $purchase->update(['total' => round($total, 2)]);

            return $purchase;
        });
    }

    /**
     * Batalkan pembelian: stok dikurangi kembali. Ditolak jika stok hasil
     * pembelian tersebut sudah terpakai (agar stok tidak pernah negatif).
     */
    public function cancel(Purchase $purchase, User $user): Purchase
    {
        return DB::transaction(function () use ($purchase, $user) {
            $locked = Purchase::lockForUpdate()->findOrFail($purchase->id);

            if (! $locked->isCompleted()) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi ini sudah dibatalkan sebelumnya.',
                ]);
            }

            foreach ($locked->items()->orderBy('product_id')->get() as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'status' => "Pembelian tidak dapat dibatalkan: stok \"{$product->name}\" saat ini ({$product->stock}) lebih kecil dari kuantitas pembelian ({$item->quantity}).",
                    ]);
                }

                $product->decrement('stock', $item->quantity);
            }

            $locked->update([
                'status' => Purchase::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'cancelled_by' => $user->id,
            ]);

            return $locked;
        });
    }
}
