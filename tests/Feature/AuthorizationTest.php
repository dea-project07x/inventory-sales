<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function staff(): User
    {
        return User::factory()->create(['role' => 'staff']);
    }

    public function test_staff_cannot_open_admin_pages_via_direct_url(): void
    {
        $this->actingAs($this->staff());

        $this->get(route('categories.index'))->assertForbidden();
        $this->get(route('suppliers.index'))->assertForbidden();
        $this->get(route('users.index'))->assertForbidden();
        $this->get(route('products.create'))->assertForbidden();
    }

    public function test_staff_cannot_modify_master_data(): void
    {
        $product = Product::factory()->create();
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();
        $this->actingAs($this->staff());

        $this->post(route('products.store'), ['sku' => 'X'])->assertForbidden();
        $this->put(route('products.update', $product), ['name' => 'Hack'])->assertForbidden();
        $this->delete(route('products.destroy', $product))->assertForbidden();
        $this->delete(route('categories.destroy', $category))->assertForbidden();
        $this->delete(route('suppliers.destroy', $supplier))->assertForbidden();
        $this->post(route('users.store'), ['name' => 'x'])->assertForbidden();

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_staff_can_view_products_and_create_transactions(): void
    {
        $product = Product::factory()->create();
        $this->actingAs($this->staff());

        $this->get(route('products.index'))->assertOk();
        $this->get(route('products.show', $product))->assertOk();
        $this->get(route('sales.create'))->assertOk();
        $this->get(route('purchases.create'))->assertOk();
    }

    public function test_admin_can_open_admin_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('categories.index'))->assertOk();
        $this->get(route('suppliers.index'))->assertOk();
        $this->get(route('users.index'))->assertOk();
    }

    public function test_staff_cannot_view_another_users_transaction_but_admin_can(): void
    {
        $owner = $this->staff();
        $other = $this->staff();
        $admin = User::factory()->create(['role' => 'admin']);
        $product = Product::factory()->create(['stock' => 10]);
        $sale = app(SaleService::class)->create($owner, [['product_id' => $product->id, 'quantity' => 1]]);

        $this->actingAs($other)->get(route('sales.show', $sale))->assertForbidden();
        $this->actingAs($owner)->get(route('sales.show', $sale))->assertOk();
        $this->actingAs($admin)->get(route('sales.show', $sale))->assertOk();
    }
}
