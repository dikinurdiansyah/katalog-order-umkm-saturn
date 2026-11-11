<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_ref')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('shipping_address');

            // ✅ KOLOM BARU DITAMBAHKAN DI SINI
            $table->enum('fulfillment_method', ['DELIVERY', 'PICKUP'])->default('DELIVERY');

            $table->foreignId('promo_id')->nullable()->constrained('promos')->nullOnDelete()->onUpdate('cascade');
            $table->decimal('subtotal_amount', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->enum('payment_method', ['TRANSFER_BANK', 'COD'])->default('TRANSFER_BANK');
            $table->string('payment_provider')->nullable();
            $table->enum('status', [
                'MENUNGGU_KONFIRMASI',
                'DIPROSES',
                'DIKIRIM',
                'SELESAI',
                'DIBATALKAN',
            ])->default('MENUNGGU_KONFIRMASI');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('managed_by')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
