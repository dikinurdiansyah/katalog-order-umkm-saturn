<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Exception;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = "CONNECTED";
            $products = DB::table('products')->count();
            $promos = DB::table('promos')->count();
        } catch (Exception $e) {
            $dbStatus = "DISCONNECTED: " . $e->getMessage();
            $products = 0;
            $promos = 0;
        }

        return response()->json([
            'system' => 'SATURN UMKM Core Engine',
            'sprint' => 'Sprint 1 - Foundation Ready',
            'database_status' => $dbStatus,
            'seeded_metrics' => [
                'products_count' => $products,
                'promos_count' => $promos
            ],
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
