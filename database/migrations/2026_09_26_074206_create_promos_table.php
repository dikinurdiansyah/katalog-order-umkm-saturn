<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('discount_type', ['FIXED', 'PERCENT'])->default('FIXED');
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('min_order_amount', 12, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->date('valid_until');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
