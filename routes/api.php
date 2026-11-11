<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Setting;

// Health Check
Route::get('/health', function () {
    return response()->json([
        'status' => 'online',
        'application' => 'Dapur Th Sant API',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String()
    ]);
});

// API v1
Route::prefix('v1')->group(function () {

    Route::get('/products', function () {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'slug', 'price', 'stock', 'description', 'image_url', 'category_id']);

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil',
            'total' => $products->count(),
            'data' => $products
        ]);
    });

    Route::get('/categories', function () {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kategori berhasil diambil',
            'total' => $categories->count(),
            'data' => $categories
        ]);
    });

    Route::get('/orders/recent', function () {
        $orders = Order::with(['orderItems.product'])->latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'message' => '5 pesanan terbaru',
            'data' => $orders
        ]);
    });

    Route::get('/outlet', function () {
        $settings = Setting::firstWhere('key', 'outlet_info');
        $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    });
});
