<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
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

    public function test_admin_can_mark_cash_order_paid_and_calculate_change(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $order = $this->createOrder('Tunai', 'DIKIRIM', 9000);

        $this->actingAs($admin)
            ->post(route('admin.orders.cashPayment', $order), ['amount_paid' => 10000])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'amount_paid' => 10000,
            'change_amount' => 1000,
            'receipt_image_path' => null,
            'payment_status' => 'VERIFIED',
        ]);
    }

    public function test_admin_cannot_mark_cash_order_paid_with_less_than_total(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $order = $this->createOrder('Tunai', 'DIKIRIM', 9000);

        $this->actingAs($admin)
            ->post(route('admin.orders.cashPayment', $order), ['amount_paid' => 8000])
            ->assertSessionHasErrors('amount_paid');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_admin_cannot_verify_payment_below_the_order_total(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'verify-admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);
        $payment = Payment::create([
            'order_id' => $order->id,
            'amount_paid' => 8000,
            'receipt_image_path' => null,
            'payment_status' => 'PENDING_REVIEW',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment), ['payment_status' => 'VERIFIED'])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'payment_status' => 'PENDING_REVIEW',
        ]);
    }

    public function test_admin_receipt_shows_fulfillment_specific_statuses(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $pickupOrder = $this->createOrder('Tunai', 'DIKIRIM', 9000);
        $deliveryOrder = $this->createOrder('Tunai', 'DIKIRIM', 10000);
        $deliveryOrder->update(['fulfillment_method' => 'DELIVERY']);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $pickupOrder))
            ->assertOk()
            ->assertSee('SIAP DIAMBIL')
            ->assertSee('AMBIL DI OUTLET')
            ->assertSee('CATATAN PENGAMBILAN')
            ->assertSee('Status Pesanan:')
            ->assertDontSee('DALAM PENGANTARAN');

        $this->get(route('admin.orders.show', $deliveryOrder))
            ->assertOk()
            ->assertSee('DELIVERY')
            ->assertSee('ALAMAT PENGIRIMAN')
            ->assertSee('Status Bayar:')
            ->assertDontSee('Status Pesanan:');
    }

    public function test_customer_can_upload_payment_receipt(): void
    {
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);

        $storedFile = null;
        try {
            $response = $this->post(route('orders.uploadBukti', ['order_ref' => $order->order_ref]), [
                'receipt_image' => UploadedFile::fake()->createWithContent(
                    'receipt.png',
                    base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r9sAAAAASUVORK5CYII=')
                ),
            ]);

            $payment = $order->payment()->first();
            if ($payment?->receipt_image_path) {
                $storedFile = storage_path('app/private/' . $payment->receipt_image_path);
            }

            $response
                ->assertRedirect(route('orders.status.show', ['order_ref' => $order->order_ref]))
                ->assertSessionHas('success');

            $this->assertNotNull($payment);
            $this->assertSame('PENDING_REVIEW', $payment->payment_status);
            $this->assertSame('9000.00', $payment->amount_paid);
            $this->assertStringStartsWith('receipts/', $payment->receipt_image_path);

            $this->assertNotNull($storedFile);
            $this->assertFileExists($storedFile);
        } finally {
            if ($storedFile && is_file($storedFile)) {
                unlink($storedFile);
            }
        }
    }

    public function test_customer_cannot_upload_non_image_as_payment_receipt(): void
    {
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);

        $this->post(route('orders.uploadBukti', ['order_ref' => $order->order_ref]), [
            'receipt_image' => UploadedFile::fake()->createWithContent('receipt.png', 'not an image'),
        ])
            ->assertSessionHasErrors('receipt_image');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_admin_can_view_uploaded_receipt_without_storage_symlink(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);
        $directory = storage_path('app/private/bukti_pembayaran');
        $filename = 'test-' . bin2hex(random_bytes(6)) . '.png';
        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents(
            $filePath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r9sAAAAASUVORK5CYII=', true)
        );

        Payment::create([
            'order_id' => $order->id,
            'amount_paid' => $order->total_amount,
            'receipt_image_path' => 'bukti_pembayaran/' . $filename,
            'payment_status' => 'PENDING_REVIEW',
        ]);

        try {
            $this->actingAs($admin)
                ->get(route('admin.orders.receipt', $order))
                ->assertOk()
                ->assertHeader('Content-Type', 'image/png');
        } finally {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function test_cash_order_cannot_upload_payment_receipt(): void
    {
        $order = $this->createOrder('Tunai', 'MENUNGGU_KONFIRMASI', 9000);

        $this->from(route('orders.success', ['order_ref' => $order->order_ref]))
            ->post(route('orders.uploadBukti', ['order_ref' => $order->order_ref]), [
                'receipt_image' => UploadedFile::fake()->create('receipt.txt'),
            ])
            ->assertRedirect(route('orders.success', ['order_ref' => $order->order_ref]))
            ->assertSessionHas('error', 'Pembayaran tunai tidak memerlukan upload bukti transfer.');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Owner Palsu',
            'email' => 'fake-owner@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'OWNER',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'fake-owner@example.test']);
    }

    public function test_default_demo_passwords_are_rejected_outside_development(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@umkm.com',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);
        User::create([
            'name' => 'Owner',
            'email' => 'owner@umkm.com',
            'password_hash' => bcrypt('password123'),
            'role' => 'OWNER',
        ]);

        $originalEnvironment = config('app.env');
        config(['app.env' => 'production']);

        try {
            $this->post('/login', [
                'email' => 'admin@umkm.com',
                'password' => 'password',
            ])->assertSessionHasErrors('email');

            $this->post('/owner-login', [
                'email' => 'owner@umkm.com',
                'password' => 'password123',
            ])->assertSessionHasErrors('email');
        } finally {
            config(['app.env' => $originalEnvironment]);
        }
    }

    public function test_customer_order_details_require_the_order_reference_not_numeric_id(): void
    {
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);

        $this->get('/status/' . $order->id)->assertNotFound();
        $this->get(route('orders.status.show', ['order_ref' => $order->order_ref]))
            ->assertOk()
            ->assertSee($order->order_ref);
    }

    public function test_success_page_requires_the_current_order_session(): void
    {
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);
        $successUrl = route('orders.success', ['order_ref' => $order->order_ref]);

        $this->get($successUrl)->assertNotFound();
        $this->withSession(['last_order_id' => $order->id])->get($successUrl)->assertOk();
    }

    public function test_customer_cannot_replace_a_verified_payment(): void
    {
        $order = $this->createOrder('QRIS', 'DIPROSES', 9000);
        Payment::create([
            'order_id' => $order->id,
            'amount_paid' => $order->total_amount,
            'receipt_image_path' => null,
            'payment_status' => 'VERIFIED',
        ]);

        $this->post(route('orders.uploadBukti', ['order_ref' => $order->order_ref]), [
            'receipt_image' => UploadedFile::fake()->createWithContent(
                'receipt.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/r9sAAAAASUVORK5CYII=')
            ),
        ])
            ->assertSessionHas('error', 'Pesanan ini tidak dapat menerima bukti pembayaran baru.');

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_status' => 'VERIFIED',
            'receipt_image_path' => null,
        ]);
    }

    public function test_customer_cannot_fetch_payment_receipts_from_public_storage_route(): void
    {
        $directory = storage_path('app/public/bukti_pembayaran');
        $filename = 'private-test-' . bin2hex(random_bytes(6)) . '.png';
        $filePath = $directory . DIRECTORY_SEPARATOR . $filename;
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        file_put_contents($filePath, 'private');

        try {
            $this->get('/storage/bukti_pembayaran/' . $filename)->assertForbidden();
        } finally {
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function test_percentage_promo_is_limited_to_one_hundred_percent(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'promo-admin@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'ADMIN',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.promos.store'), [
                'code' => 'OVER100',
                'discount_type' => 'PERCENT',
                'discount_amount' => 101,
                'min_order_amount' => 0,
                'valid_until' => now()->addDay()->toDateString(),
                'is_active' => true,
            ])
            ->assertSessionHasErrors('discount_amount');

        $this->assertDatabaseMissing('promos', ['code' => 'OVER100']);
    }

    public function test_owner_report_counts_only_completed_sales_and_quantities(): void
    {
        $owner = User::create([
            'name' => 'Owner',
            'email' => 'reports-owner@example.test',
            'password_hash' => bcrypt('password'),
            'role' => 'OWNER',
        ]);
        $product = $this->createProduct();
        $completedOrder = $this->createOrder('QRIS', 'SELESAI', 20000);
        $completedOrder->orderItems()->create([
            'product_id' => $product->id,
            'price_at_purchase' => 10000,
            'quantity' => 2,
            'subtotal' => 20000,
        ]);
        $this->createOrder('QRIS', 'DIBATALKAN', 9000);

        $response = $this->actingAs($owner)
            ->get(route('owner.reports'))
            ->assertOk();

        $this->assertEquals(20000, (float) $response->viewData('totalRevenue'));
        $this->assertEquals(2, (int) $response->viewData('topVolume')->total_qty);
        $this->assertEquals(2, (int) $response->viewData('products')->first()->total_sold);
        $response->assertSee('Belum tersedia');
    }

    public function test_payment_method_migration_preserves_an_existing_column_on_rollback(): void
    {
        $migration = require database_path('migrations/2026_11_16_105500_add_metode_pembayaran_to_orders_table.php');
        $migration->up();
        $this->assertTrue(Schema::hasColumn('orders', 'metode_pembayaran'));

        $migration->down();

        $this->assertTrue(Schema::hasColumn('orders', 'metode_pembayaran'));
    }

    public function test_deployed_migration_corrects_existing_cod_orders_to_cash(): void
    {
        $order = $this->createOrder('QRIS', 'MENUNGGU_KONFIRMASI', 9000);
        $order->update(['payment_method' => 'COD']);

        $migration = require database_path('migrations/2026_11_17_112000_correct_cash_order_payment_labels.php');
        $migration->up();

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'metode_pembayaran' => 'Tunai',
        ]);
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
