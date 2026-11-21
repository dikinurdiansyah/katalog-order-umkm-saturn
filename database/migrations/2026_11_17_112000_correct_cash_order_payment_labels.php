<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

        DB::table('orders')
            ->where('payment_method', 'COD')
            ->update(['metode_pembayaran' => 'Tunai']);
    }

    public function down(): void
    {
        // Keep the corrected labels; reverting them would misrepresent paid orders.
    }
};
