<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthController;

Route::get('/health', [HealthController::class, 'check']);
Route::get('/', function () {
    return view('welcome');
});
use Illuminate\Support\Facades\DB;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $productCount = DB::table('products')->count();
        return response()->json([
            'system_name' => 'Katalog UMKM Saturn Core System',
            'sprint_stage' => 'Sprint 1 - Foundation Ready',
            'database_status' => 'CONNECTED (MySQL 8.0 InnoDB)',
            'database_name' => DB::connection()->getDatabaseName(),
            'active_products_seeded' => $productCount,
            'timestamp' => now()->toIso8601String()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'database_status' => 'DISCONNECTED: ' . $e->getMessage()
        ], 500);
    }
});
