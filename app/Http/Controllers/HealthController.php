<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $pdo = DB::connection()->getPdo();
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            $productCount = DB::table('products')->count();

            return response()->json([
                'status' => 'ok',
                'system_name' => 'Katalog UMKM Saturn Core System',
                'sprint_stage' => 'Sprint 1 - Foundation Ready',
                'database_status' => 'CONNECTED (MySQL 8.0 InnoDB)',
                'database_name' => DB::connection()->getDatabaseName(),
                'active_products_seeded' => $productCount,
                'latency_ms' => $latency,
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'database_status' => 'DISCONNECTED: ' . $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }
}
