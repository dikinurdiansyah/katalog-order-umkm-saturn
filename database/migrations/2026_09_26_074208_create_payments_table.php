<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete()->onUpdate('cascade');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0); // ✅ UBAH JADI BAHASA INGGRIS
            $table->string('receipt_image_path');
            $table->enum('payment_status', ['PENDING_REVIEW', 'VERIFIED', 'REJECTED'])->default('PENDING_REVIEW');
            $table->text('admin_notes')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
