<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\OrderItem;
class ManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => 'secret123']);
        $this->actingAs($this->user);
    }

    public function test_orders_grid_lists_all_and_filters_by_email(): void
    {
        $a = Customer::factory()->create(['email' => 'a@example.com']);
        $b = Customer::factory()->create(['email' => 'b@example.com']);
        Order::create(['customer_id' => $a->id]);
        Order::create(['customer_id' => $a->id]);
        Order::create(['customer_id' => $b->id]);

        $this->getJson('/api/orders')->assertOk()->assertJsonPath('total', 3);
        $this->getJson('/api/orders?email=a@example.com')->assertOk()->assertJsonPath('total', 2);
        $this->getJson('/api/orders?email=nobody@example.com')->assertOk()->assertJsonPath('total', 0);
    }

    public function test_customers_list_with_search(): void
    {
        Customer::factory()->create(['name' => 'Asha Raman', 'email' => 'asha@example.com']);
        Customer::factory()->create(['name' => 'Ravi Kumar', 'email' => 'ravi@example.com']);

        $this->getJson('/api/customers')->assertOk()->assertJsonCount(2);
        $this->getJson('/api/customers?search=asha')->assertOk()->assertJsonCount(1);
    }
    public function test_product_without_orders_can_be_deleted(): void
    {
        $p = Product::factory()->create();

        $this->deleteJson("/api/products/{$p->id}")->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $p->id]);
    }

    public function test_product_in_past_orders_cannot_be_deleted(): void
    {
        $p = Product::factory()->create();
        $order = Order::create(['customer_id' => Customer::factory()->create()->id]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $p->id,
            'quantity' => 1,
            'unit_price' => 10,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'line_total' => 10,
        ]);

        $this->deleteJson("/api/products/{$p->id}")->assertStatus(422);
        $this->assertDatabaseHas('products', ['id' => $p->id]);
    }

    public function test_category_delete_message_mentions_active_products(): void
    {
        $p = Product::factory()->create();

        $this->deleteJson('/api/categories/' . $p->category_id)
            ->assertStatus(422)
            ->assertJsonPath('message', 'You have active products in this category. Move or delete them first.');
    }
    public function test_category_can_be_renamed_but_not_to_a_duplicate(): void
    {
        $cat = Category::factory()->create(['name' => 'Old']);
        Category::factory()->create(['name' => 'Taken']);

        $this->putJson("/api/categories/{$cat->id}", ['name' => 'New'])->assertOk();
        $this->assertDatabaseHas('categories', ['id' => $cat->id, 'name' => 'New']);

        $this->putJson("/api/categories/{$cat->id}", ['name' => 'Taken'])->assertStatus(422);
        $this->putJson("/api/categories/{$cat->id}", ['name' => 'New'])->assertOk();
    }

    public function test_product_can_be_edited_and_code_stays_unique(): void
    {
        $p = Product::factory()->create(['code' => 'AAA-111']);
        Product::factory()->create(['code' => 'BBB-222']);

        $payload = [
            'category_id' => $p->category_id,
            'name' => 'Renamed',
            'code' => 'AAA-111',
            'price' => 99.5,
            'tax_percent' => 12,
            'stock' => 7,
        ];

        $this->putJson("/api/products/{$p->id}", $payload)->assertOk();
        $this->assertSame('Renamed', $p->fresh()->name);
        $this->assertSame(7, $p->fresh()->stock);

        $this->putJson("/api/products/{$p->id}", [...$payload, 'code' => 'BBB-222'])->assertStatus(422);
    }

    public function test_currency_can_be_changed(): void
    {
        $this->assertSame('₹', Setting::symbol());

        $this->putJson('/api/settings/currency', ['currency' => 'USD'])->assertOk();
        $this->assertSame('$', Setting::symbol());

        $this->putJson('/api/settings/currency', ['currency' => 'XXX'])->assertStatus(422);
    }

    public function test_account_update_needs_current_password(): void
    {
        $this->putJson('/api/settings/account', [
            'name' => 'Owner',
            'email' => 'owner@store.test',
            'current_password' => 'wrong',
        ])->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->putJson('/api/settings/account', [
            'name' => 'Owner',
            'email' => 'owner@store.test',
            'current_password' => 'secret123',
            'password' => 'newsecret123',
            'password_confirmation' => 'newsecret123',
        ])->assertOk();

        $fresh = $this->user->fresh();
        $this->assertSame('owner@store.test', $fresh->email);
        $this->assertTrue(Hash::check('newsecret123', $fresh->password));
    }
}