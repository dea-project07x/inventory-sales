<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_open_report_and_staff_cannot(): void
    {
        $this->actingAs($this->admin())->get(route('reports.index'))->assertOk()->assertSee('Riwayat');

        $this->actingAs(User::factory()->create(['role' => 'staff']))
            ->get(route('reports.index'))->assertForbidden();
    }

    public function test_history_lists_purchase_and_sale_movements(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create(['stock' => 10]);
        $supplier = Supplier::factory()->create();

        app(PurchaseService::class)->create($admin, $supplier->id, [
            ['product_id' => $product->id, 'quantity' => 5, 'unit_cost' => 1000],
        ]);
        app(SaleService::class)->create($admin, [['product_id' => $product->id, 'quantity' => 3]]);

        $rows = $this->actingAs($admin)->get(route('reports.index'))->viewData('movements');

        $this->assertSame(2, $rows->total());
        $this->assertEqualsCanonicalizing(['in', 'out'], collect($rows->items())->pluck('direction')->all());
    }

    public function test_cancelled_sale_stays_in_history_but_not_in_summary(): void
    {
        $admin = $this->admin();
        $product = Product::factory()->create(['stock' => 10, 'selling_price' => 10000]);
        $svc = app(SaleService::class);

        $svc->create($admin, [['product_id' => $product->id, 'quantity' => 2]]);          // 20.000
        $cancelled = $svc->create($admin, [['product_id' => $product->id, 'quantity' => 1]]);
        $svc->cancel($cancelled, $admin);

        $response = $this->actingAs($admin)->get(route('reports.index'));

        $this->assertSame(2, $response->viewData('movements')->total());
        $this->assertSame(1, (int) $response->viewData('salesSummary')->c);
        $this->assertEquals(20000, $response->viewData('salesSummary')->t);
    }

    public function test_filters_by_product_and_direction(): void
    {
        $admin = $this->admin();
        $a = Product::factory()->create(['stock' => 10]);
        $b = Product::factory()->create(['stock' => 10]);
        $svc = app(SaleService::class);
        $svc->create($admin, [['product_id' => $a->id, 'quantity' => 1]]);
        $svc->create($admin, [['product_id' => $b->id, 'quantity' => 1]]);
        $this->actingAs($admin);

        $this->assertSame(1, $this->get(route('reports.index', ['product_id' => $a->id]))->viewData('movements')->total());
        $this->assertSame(0, $this->get(route('reports.index', ['direction' => 'in']))->viewData('movements')->total());
        $this->assertSame(2, $this->get(route('reports.index', ['direction' => 'out']))->viewData('movements')->total());
    }

    public function test_invalid_report_filters_are_rejected(): void
    {
        $this->actingAs($this->admin())
            ->get(route('reports.index', ['direction' => 'hack', 'from' => 'bukan-tanggal']))
            ->assertSessionHasErrors(['direction', 'from']);
    }
}
