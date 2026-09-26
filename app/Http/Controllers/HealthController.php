<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthCheckController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            $startTime = microtime(true);

            // Ping koneksi PDO MySQL
            $pdo = DB::connection()->getPdo();
            $latency = round((microtime(true) - $startTime) * 1000, 2);

            // Ambil data produk & versi database
            $productCount = DB::table('products')->count();
            $versionResult = DB::select('SELECT VERSION() as version');
            $version = $versionResult[0]->version ?? 'Unknown';

            return response()->json([
                'status' => 'ok',
                'message' => 'Terhubung ke database MySQL db_katalog_order_online_umkm dengan sukses.',
                'database' => [
                    'driver' => config('database.default'),
                    'database_name' => DB::connection()->getDatabaseName(),
                    'active_products_seeded' => $productCount,
                    'latency_ms' => $latency,
                    'version' => $version,
                ],
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke database MySQL.',
                'error_detail' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }
    }
}
