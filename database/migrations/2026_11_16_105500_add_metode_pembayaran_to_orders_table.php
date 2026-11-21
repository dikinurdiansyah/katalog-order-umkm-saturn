<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'metode_pembayaran')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('metode_pembayaran')->default('QRIS');
            });
        }
    }

    public function down(): void
    {
        // The column may have existed before this migration; preserve production data.
    }
};
