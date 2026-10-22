<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function payload(array $override = []): array
    {
        $category = Category::factory()->create();

        return array_merge([
            'sku' => 'SKU-NEW-1',
            'name' => 'Produk Baru',
            'category_id' => $category->id,
            'purchase_price' => 1000,
            'selling_price' => 1500,
            'stock' => 10,
            'minimum_stock' => 2,
        ], $override);
    }

    public function test_admin_can_create_product(): void
    {
        $this->actingAs($this->admin())->post(route('products.store'), $this->payload())->assertRedirect();

        $this->assertDatabaseHas('products', ['sku' => 'SKU-NEW-1', 'stock' => 10]);
    }

    public function test_duplicate_sku_is_rejected(): void
    {
        Product::factory()->create(['sku' => 'DUP-1']);

        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->payload(['sku' => 'DUP-1']))
            ->assertSessionHasErrors('sku');

        $this->assertSame(1, Product::where('sku', 'DUP-1')->count());
    }

    public function test_negative_price_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->payload(['selling_price' => -5, 'purchase_price' => -1]))
            ->assertSessionHasErrors(['selling_price', 'purchase_price']);
    }

    public function test_invalid_category_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('products.store'), $this->payload(['category_id' => 9999]))
            ->assertSessionHasErrors('category_id');
    }

    public function test_product_used_in_transaction_cannot_be_deleted(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create(['stock' => 10]);
        app(SaleService::class)->create($admin, [['product_id' => $product->id, 'quantity' => 1]]);

        $this->actingAs($admin)->delete(route('products.destroy', $product))->assertSessionHas('error');

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_unused_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())->delete(route('products.destroy', $product))->assertRedirect();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
