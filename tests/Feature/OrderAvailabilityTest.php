<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_unavailable_product_cannot_be_added_to_cart(): void
    {
        $product = $this->createProduct(isAvailable: false);

        $this->get(route('catalog.show', $product))
            ->assertOk()
            ->assertSee('HABIS')
            ->assertDontSee('Tambah ke Keranjang');

        $this->from('/products/'.$product->id)
            ->post(route('cart.add'), [
                'product_id' => $product->id,
                'quantity' => 1,
            ])
            ->assertRedirect('/products/'.$product->id)
            ->assertSessionHas('error');

        $this->assertSame([], session()->get('cart', []));
    }

    public function test_checkout_rejects_product_disabled_after_being_added_to_cart(): void
    {
        $product = $this->createProduct(isAvailable: false);

        $this->withSession([
            'cart' => [
                $product->id => ['quantity' => 1],
            ],
        ])->post(route('orders.store'), [
            'customer_name' => 'Pembeli',
            'customer_phone' => '08123456789',
            'fulfillment_method' => 'PICKUP',
            'pickup_note' => 'Ambil di kasir',
            'metode_pembayaran' => 'Tunai',
        ])
            ->assertRedirect()
            ->assertSessionHas('error', "Produk {$product->name} sedang habis dan tidak dapat dipesan.");

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
    }

    public function test_admin_can_toggle_product_availability(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $product = $this->createProduct();

        $this->actingAs($admin)
            ->post(route('admin.products.toggleAvailability', $product))
            ->assertRedirect(route('admin.products'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_available' => false]);
    }

    public function test_admin_order_filters_limit_rows_and_total(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);

        $matchingOrder = $this->createOrder('QRIS', 'SELESAI', 15000);
        $unmatchedOrder = $this->createOrder('QRIS', 'DIPROSES', 6000);
        $this->createOrder('Tunai', 'SELESAI', 5000);

        $this->actingAs($admin)
            ->get(route('admin.orders', [
                'metode_pembayaran' => 'QRIS',
                'status' => 'SELESAI',
            ]))
            ->assertOk()
            ->assertSee($matchingOrder->order_ref)
            ->assertDontSee($unmatchedOrder->order_ref)
            ->assertViewHas('totalTerfilter', 15000);
    }

    private function createProduct(bool $isAvailable = true): Product
    {
        $category = Category::create(['name' => 'Minuman']);

        return Product::create([
            'id_category' => $category->id,
            'name' => 'Kopi Susu',
            'price' => 10000,
            'stock' => 10,
            'is_active' => true,
            'is_available' => $isAvailable,
        ]);
    }

    private function createOrder(string $metodePembayaran, string $status, int $totalAmount): Order
    {
        return Order::create([
            'customer_name' => 'Pembeli',
            'customer_phone' => '08123456789',
            'shipping_address' => 'Alamat',
            'fulfillment_method' => 'PICKUP',
            'metode_pembayaran' => $metodePembayaran,
            'payment_method' => $metodePembayaran === 'Tunai' ? 'COD' : 'TRANSFER_BANK',
            'subtotal_amount' => $totalAmount,
            'discount_amount' => 0,
            'total_amount' => $totalAmount,
            'status' => $status,
        ]);
    }
}
