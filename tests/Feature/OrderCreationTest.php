<?php

namespace Tests\Feature;

use App\Jobs\SendOrderConfirmation;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    private function payload(array $items): array
    {
        return [
            'customer_name' => 'Asha Raman',
            'customer_email' => 'asha@example.com',
            'items' => $items,
        ];
    }

    public function test_creates_order_computes_tax_and_deducts_stock(): void
    {
        Queue::fake();
        $pen = Product::factory()->create(['price' => 120, 'tax_percent' => 18, 'stock' => 10]);
        $tea = Product::factory()->create(['price' => 240, 'tax_percent' => 5, 'stock' => 5]);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $pen->id, 'quantity' => 3],
            ['product_id' => $tea->id, 'quantity' => 2],
        ]))->assertCreated()
            ->assertJsonPath('subtotal', '840.00')
            ->assertJsonPath('tax_total', '88.80')
            ->assertJsonPath('grand_total', '928.80');

        $this->assertSame(7, $pen->fresh()->stock);
        $this->assertSame(3, $tea->fresh()->stock);
        Queue::assertPushed(SendOrderConfirmation::class);
    }

    public function test_rejects_order_when_stock_is_insufficient(): void
    {
        Queue::fake();
        $product = Product::factory()->create(['stock' => 1]);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $product->id, 'quantity' => 2],
        ]))->assertStatus(422);

        $this->assertSame(1, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
        Queue::assertNothingPushed();
    }

    public function test_failed_order_rolls_back_other_lines(): void
    {
        $ok = Product::factory()->create(['stock' => 5]);
        $none = Product::factory()->create(['stock' => 0]);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $ok->id, 'quantity' => 2],
            ['product_id' => $none->id, 'quantity' => 1],
        ]))->assertStatus(422);

        $this->assertSame(5, $ok->fresh()->stock);
    }

    public function test_duplicate_lines_are_merged_before_stock_check(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $this->postJson('/api/orders', $this->payload([
            ['product_id' => $product->id, 'quantity' => 2],
            ['product_id' => $product->id, 'quantity' => 2],
        ]))->assertStatus(422);

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_last_unit_can_only_be_sold_once(): void
    {
        Queue::fake();
        $product = Product::factory()->create(['stock' => 1]);
        $items = [['product_id' => $product->id, 'quantity' => 1]];

        $this->postJson('/api/orders', $this->payload($items))->assertCreated();
        $this->postJson('/api/orders', $this->payload($items))->assertStatus(422);

        $this->assertSame(0, $product->fresh()->stock);
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_validation_errors(): void
    {
        $this->postJson('/api/orders', ['items' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['customer_email', 'customer_name', 'items']);
    }

    public function test_low_stock_and_history_endpoints(): void
    {
        Product::factory()->create(['stock' => 2]);
        Product::factory()->create(['stock' => 50]);

        $this->getJson('/api/products/low-stock?threshold=10')
            ->assertOk()
            ->assertJsonCount(1, 'products');

        Customer::factory()->create(['email' => 'x@example.com']);

        $this->getJson('/api/customers/orders?email=x@example.com')
            ->assertOk()
            ->assertJsonCount(0, 'orders');

        $this->getJson('/api/customers/orders?email=none@example.com')->assertNotFound();
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();
        $this->deleteJson('/api/categories/' . $product->category_id)->assertStatus(422);

        $empty = Category::factory()->create();
        $this->deleteJson('/api/categories/' . $empty->id)->assertNoContent();
    }
}