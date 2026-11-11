<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// ==========================================
// ✅ ROUTE STORAGE - HARUS DI PALING ATAS!
// ==========================================
Route::get('/storage/{path}', function ($path) {
    try {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404, 'File tidak ditemukan: ' . $fullPath);
        }

        $mimeType = mime_content_type($fullPath);

        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    } catch (\Exception $e) {
        abort(500, 'Error: ' . $e->getMessage());
    }
})->where('path', '.*');

// ==========================================
// 1. SYSTEM & HEALTH CHECK
// ==========================================
Route::get('/health', [HealthController::class, 'index'])->name('health');

// ==========================================
// 2. PUBLIC - CATALOG & KONTAK
// ==========================================
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/products/{product}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/kontak', function () {
    $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
    $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
    return view('contact.index', compact('data'));
})->name('contact.index');

// ==========================================
// 3. CART (KERANJANG BELANJA) - PUBLIK
// ==========================================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// ==========================================
// 4. CUSTOMER CHECKOUT FLOW (TANPA LOGIN / GUEST)
// ==========================================
Route::get('/checkout', [OrderController::class, 'create'])->name('orders.create');
Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');

Route::post('/promo/check', [OrderController::class, 'checkPromo'])->name('promo.check');

Route::get('/orders/{order}/success', function (Order $order) {
    $order->load(['promo', 'orderItems.product', 'payment']);
    session(['last_order_id' => $order->id]);
    return view('orders.success', compact('order'));
})->name('orders.success');

// ==========================================
// STATUS TRACKING (PUBLIK)
// ==========================================
Route::get('/status', function (Request $request) {
    $order = null;

    if ($request->has('ref') && !empty($request->ref)) {
        $order = Order::with(['orderItems.product', 'promo'])
            ->where('order_ref', strtoupper(trim($request->ref)))
            ->first();
    }

    if (!$order) {
        $orderId = session('last_order_id');
        if ($orderId) {
            $order = Order::with(['orderItems.product', 'promo'])->find($orderId);
        }
    }

    return view('orders.status', compact('order'));
})->name('orders.status');

Route::get('/status/{order}', function (Order $order) {
    $order->load(['orderItems.product', 'promo']);
    return view('orders.status', compact('order'));
})->name('orders.status.show');

// ==========================================
// 5. PUBLIC PAYMENT UPLOAD (KHUSUS PELANGGAN)
// ==========================================
Route::post('/payments/guest-upload', [PaymentController::class, 'storeGuest'])->name('payments.guest.store');

// ✅ TAMBAHAN: Route untuk upload bukti pembayaran di halaman success
Route::post('/orders/{order}/upload-bukti', [OrderController::class, 'uploadBukti'])->name('orders.uploadBukti');

// ==========================================
// 6. AUTHENTICATION (KHUSUS ADMIN/OWNER)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================================
// 7. ADMIN PANEL (WAJIB LOGIN & WAJIB ROLE: ADMIN)
// ==========================================
Route::middleware(['auth', 'role:ADMIN'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');

    // Promos
    Route::get('/promos', [AdminController::class, 'promos'])->name('promos');
    Route::post('/promos', [AdminController::class, 'storePromo'])->name('promos.store');
    Route::put('/promos/{promo}', [AdminController::class, 'updatePromo'])->name('promos.update');
    Route::delete('/promos/{promo}', [AdminController::class, 'destroyPromo'])->name('promos.destroy');

    // Orders (Admin View)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Payments (Admin View)
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/payments/create/{order}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Route untuk Admin memverifikasi bukti pembayaran & hitung kembalian
    Route::post('/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');

    // Contact & Outlet (Admin View)
    Route::get('/contact', [AdminController::class, 'contact'])->name('contact');
    Route::post('/contact', [AdminController::class, 'updateContact'])->name('contact.update');

    // ✅ TAMBAHAN: User Management (Staf)
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});

// ==========================================
// 8. OWNER PORTAL (WAJIB LOGIN & WAJIB ROLE: OWNER)
// ==========================================
Route::get('/owner-login', function () {
    if (Auth::check() && strtoupper(Auth::user()->role) === 'OWNER') {
        return redirect()->route('owner.dashboard');
    }
    return view('admin.owner.login');
})->name('owner.login');

Route::post('/owner-login', [AdminController::class, 'ownerLogin'])->name('owner.login.post');

Route::middleware(['auth', 'role:OWNER'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'ownerDashboard'])->name('dashboard');
    Route::get('/reports', [AdminController::class, 'ownerReports'])->name('reports');

    // ✅ TAMBAHAN: Route untuk Export Laporan Owner ke Excel/CSV
    Route::get('/export-laporan', [AdminController::class, 'exportLaporan'])->name('export.laporan');
});

// ✅ DIPERBAIKI: Diubah dari Route::post menjadi Route::get agar bisa dipanggil via link <a>
Route::get('/owner-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('owner.login')->with('success', 'Anda telah keluar dari Portal Owner dengan aman.');
})->name('owner.logout');
