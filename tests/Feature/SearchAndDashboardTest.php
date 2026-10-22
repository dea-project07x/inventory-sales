<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_search_filters_by_sku_and_name(): void
    {
        Product::factory()->create(['sku' => 'ABC-111', 'name' => 'Gula Pasir']);
        Product::factory()->create(['sku' => 'XYZ-222', 'name' => 'Minyak Goreng']);
        $this->actingAs($this->admin());

        $bySku = $this->get(route('products.index', ['q' => 'ABC']))->viewData('products');
        $this->assertSame(1, $bySku->total());
        $this->assertSame('Gula Pasir', $bySku->first()->name);

        $byName = $this->get(route('products.index', ['q' => 'goreng']))->viewData('products');
        $this->assertSame(1, $byName->total());
    }

    public function test_category_filter_works(): void
    {
        $a = Category::factory()->create();
        $b = Category::factory()->create();
        Product::factory()->count(2)->create(['category_id' => $a->id]);
        Product::factory()->count(3)->create(['category_id' => $b->id]);
        $this->actingAs($this->admin());

        $this->assertSame(3, $this->get(route('products.index', ['category_id' => $b->id]))->viewData('products')->total());
    }

    public function test_pagination_limits_rows_and_keeps_filters(): void
    {
        Product::factory()->count(25)->create();
        $this->actingAs($this->admin());

        $page1 = $this->get(route('products.index', ['q' => 'a']))->viewData('products');
        $this->assertLessThanOrEqual(10, $page1->count());

        $page2 = $this->get(route('products.index', ['page' => 3]))->viewData('products');
        $this->assertSame(5, $page2->count());
    }

    public function test_transaction_status_and_date_filters(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create(['stock' => 50]);
        $svc = app(SaleService::class);
        $old = $svc->create($admin, [['product_id' => $product->id, 'quantity' => 1]]);
        $old->update(['sold_at' => now()->subDays(10)]);
        $cancelled = $svc->create($admin, [['product_id' => $product->id, 'quantity' => 1]]);
        $svc->cancel($cancelled, $admin);
        $svc->create($admin, [['product_id' => $product->id, 'quantity' => 1]]);
        $this->actingAs($admin);

        $this->assertSame(1, $this->get(route('sales.index', ['status' => 'cancelled']))->viewData('sales')->total());
        $this->assertSame(2, $this->get(route('sales.index', ['from' => now()->subDays(1)->toDateString()]))->viewData('sales')->total());
        $this->assertSame(1, $this->get(route('sales.index', ['to' => now()->subDays(5)->toDateString()]))->viewData('sales')->total());
    }

    public function test_invalid_filter_input_is_rejected(): void
    {
        $this->actingAs($this->admin());

        $this->get(route('sales.index', ['status' => 'hack', 'from' => 'bukan-tanggal']))
            ->assertSessionHasErrors(['status', 'from']);
    }

    public function test_dashboard_numbers_come_from_database(): void
    {
        $admin = $this->admin();
        Product::factory()->count(3)->create(['stock' => 50, 'selling_price' => 25000]);
        Product::factory()->create(['stock' => 1, 'minimum_stock' => 5]); // stok rendah
        $product = Product::where('stock', 50)->first();
        app(SaleService::class)->create($admin, [['product_id' => $product->id, 'quantity' => 2]]); // 50.000

        $response = $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $stats = $response->viewData('stats');

        $this->assertSame(4, $stats['total_products']);
        $this->assertEquals(50000, $stats['sales_today']);
        $this->assertEquals(50000, $stats['sales_month']);
        $this->assertSame(1, $stats['sales_count']);
        $this->assertSame(1, $stats['low_stock_count']);
        $response->assertSee('50.000');
    }
}
