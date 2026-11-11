<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan sesuai revisi
            if (Schema::hasColumn('orders', 'courier_name')) {
                $table->dropColumn('courier_name');
            }
            if (Schema::hasColumn('orders', 'courier_service')) {
                $table->dropColumn('courier_service');
            }
            if (Schema::hasColumn('orders', 'tracking_number')) {
                $table->dropColumn('tracking_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier_name')->nullable();
            $table->string('courier_service')->nullable();
            $table->string('tracking_number')->nullable();
        });
    }
};
