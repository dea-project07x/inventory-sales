<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ---------- Purchase ----------
    public function test_purchase_increases_stock(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($this->staff())->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 2000]],
        ])->assertRedirect();

        $this->assertSame(15, $product->fresh()->stock);
        $this->assertSame('10000.00', Purchase::first()->total);
    }

    public function test_purchase_with_invalid_quantity_is_rejected(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($this->staff())->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 0, 'unit_cost' => 2000]],
        ])->assertSessionHasErrors('items.0.quantity');

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(0, Purchase::count());
    }

    // ---------- Sale ----------
    public function test_sale_decreases_stock_and_total_is_computed_by_server(): void
    {
        $product = Product::factory()->create(['stock' => 10, 'selling_price' => 10000]);

        // Penyerang mencoba mengirim harga & total palsu: harus diabaikan server.
        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 3, 'unit_price' => 1]],
            'total' => 1,
        ])->assertRedirect();

        $this->assertSame(7, $product->fresh()->stock);
        $sale = Sale::first();
        $this->assertSame('30000.00', $sale->total);
        $this->assertSame('10000.00', SaleItem::first()->unit_price);
    }

    public function test_sale_with_insufficient_stock_is_rejected_without_changes(): void
    {
        $product = Product::factory()->create(['stock' => 4]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(4, $product->fresh()->stock);
        $this->assertSame(0, Sale::count());
        $this->assertSame(0, SaleItem::count());
    }

    public function test_sale_with_zero_or_negative_quantity_is_rejected(): void
    {
        $product = Product::factory()->create(['stock' => 4]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => -2]],
        ])->assertSessionHasErrors('items.0.quantity');

        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_failure_on_one_item_rolls_back_the_whole_transaction(): void
    {
        $ok = Product::factory()->create(['stock' => 10]);       // id lebih kecil, diproses lebih dulu
        $short = Product::factory()->create(['stock' => 1]);

        try {
            app(SaleService::class)->create($this->staff(), [
                ['product_id' => $ok->id, 'quantity' => 3],
                ['product_id' => $short->id, 'quantity' => 5],
            ]);
            $this->fail('ValidationException seharusnya dilempar.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('items', $e->errors());
        }

        $this->assertSame(10, $ok->fresh()->stock, 'Stok item pertama harus kembali (rollback).');
        $this->assertSame(1, $short->fresh()->stock);
        $this->assertSame(0, Sale::count(), 'Header penjualan tidak boleh tersimpan.');
        $this->assertSame(0, SaleItem::count());
    }

    public function test_duplicate_product_lines_are_merged_and_checked_together(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->actingAs($this->staff())->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
                ['product_id' => $product->id, 'quantity' => 3], // total 6 > stok 5
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(5, $product->fresh()->stock);
    }

    // ---------- Cancellation ----------
    public function test_admin_can_cancel_sale_and_stock_is_restored(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $sale = app(SaleService::class)->create($this->staff(), [['product_id' => $product->id, 'quantity' => 4]]);
        $this->assertSame(6, $product->fresh()->stock);

        $this->actingAs($this->admin())->post(route('sales.cancel', $sale))->assertRedirect();

        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame('cancelled', $sale->fresh()->status);
    }

    public function test_sale_cannot_be_cancelled_twice(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $sale = app(SaleService::class)->create($this->staff(), [['product_id' => $product->id, 'quantity' => 4]]);
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('sales.cancel', $sale));
        $this->actingAs($admin)->post(route('sales.cancel', $sale))->assertSessionHasErrors('status');

        $this->assertSame(10, $product->fresh()->stock, 'Stok tidak boleh bertambah dua kali.');
    }

    public function test_staff_cannot_cancel_transaction(): void
    {
        $staff = $this->staff();
        $product = Product::factory()->create(['stock' => 10]);
        $sale = app(SaleService::class)->create($staff, [['product_id' => $product->id, 'quantity' => 1]]);

        $this->actingAs($staff)->post(route('sales.cancel', $sale))->assertForbidden();

        $this->assertSame('completed', $sale->fresh()->status);
    }

    public function test_purchase_cancel_is_rejected_when_stock_already_used(): void
    {
        $supplier = Supplier::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);
        $staff = $this->staff();

        $this->actingAs($staff)->post(route('purchases.store'), [
            'supplier_id' => $supplier->id,
            'items' => [['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 1000]],
        ]);
        app(SaleService::class)->create($staff, [['product_id' => $product->id, 'quantity' => 4]]); // stok tinggal 1

        $purchase = Purchase::first();
        $this->actingAs($this->admin())->post(route('purchases.cancel', $purchase))->assertSessionHasErrors('status');

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertSame('completed', $purchase->fresh()->status);
    }
}
