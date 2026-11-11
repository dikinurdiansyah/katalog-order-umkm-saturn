<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category')->constrained('categories')->onDelete('restrict')->onUpdate('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock')->default(0);

            // ✅ KOLOM TAMBAHAN SESUAI ERD (Multiple Images)
            $table->string('image_url')->nullable(); // Foto Utama
            $table->string('image_2')->nullable();    // Foto Tambahan 1
            $table->string('image_3')->nullable();    // Foto Tambahan 2
            $table->string('image_4')->nullable();    // Foto Tambahan 3

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
