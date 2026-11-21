<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // ==================== DASHBOARD ====================
    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'MENUNGGU_KONFIRMASI')->count(),
            'completed_orders' => Order::where('status', 'SELESAI')->count(),
            'total_revenue' => Order::where('status', 'SELESAI')->sum('total_amount'),
            'active_promos' => Promo::where('is_active', true)->count(),
            'pending_payments' => Payment::where('payment_status', 'PENDING_REVIEW')->count(),
        ];

        $recentOrders = Order::with(['payment', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::where('stock', '<', 10)
            ->with('category')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'lowStockProducts'));
    }

    // ==================== CATEGORIES ====================
    public function categories()
    {
        $categories = Category::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        return redirect()->route('admin.categories')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.categories')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(Category $category)
    {
        $productCount = $category->products()->count();

        if ($productCount > 0) {
            return back()->with('error', "Tidak bisa hapus kategori yang memiliki {$productCount} produk.");
        }

        $category->delete();

        return redirect()->route('admin.categories')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    // ==================== PRODUCTS ====================
    public function products()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'id_category' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // ✅ UPLOAD LANGSUNG KE public/products/ (TANPA STORAGE LINK!)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '_main_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_url'] = 'products/' . $fileName;
        }

        if ($request->hasFile('image_2')) {
            $file = $request->file('image_2');
            $fileName = time() . '_img2_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_2'] = 'products/' . $fileName;
        }

        if ($request->hasFile('image_3')) {
            $file = $request->file('image_3');
            $fileName = time() . '_img3_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_3'] = 'products/' . $fileName;
        }

        if ($request->hasFile('image_4')) {
            $file = $request->file('image_4');
            $fileName = time() . '_img4_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_4'] = 'products/' . $fileName;
        }

        $validated['is_active'] = $request->boolean('is_active');

        Product::create($validated);

        return redirect()->route('admin.products')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'id_category' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // ✅ HANDLE GAMBAR UTAMA
        if ($request->hasFile('image')) {
            if ($product->image_url && file_exists(public_path($product->image_url))) {
                unlink(public_path($product->image_url));
            }
            $file = $request->file('image');
            $fileName = time() . '_main_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_url'] = 'products/' . $fileName;
        }

        // ✅ HANDLE GAMBAR 2
        if ($request->hasFile('image_2')) {
            if ($product->image_2 && file_exists(public_path($product->image_2))) {
                unlink(public_path($product->image_2));
            }
            $file = $request->file('image_2');
            $fileName = time() . '_img2_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_2'] = 'products/' . $fileName;
        }

        // ✅ HANDLE GAMBAR 3
        if ($request->hasFile('image_3')) {
            if ($product->image_3 && file_exists(public_path($product->image_3))) {
                unlink(public_path($product->image_3));
            }
            $file = $request->file('image_3');
            $fileName = time() . '_img3_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_3'] = 'products/' . $fileName;
        }

        // ✅ HANDLE GAMBAR 4
        if ($request->hasFile('image_4')) {
            if ($product->image_4 && file_exists(public_path($product->image_4))) {
                unlink(public_path($product->image_4));
            }
            $file = $request->file('image_4');
            $fileName = time() . '_img4_' . $file->getClientOriginalName();
            $file->move(public_path('products'), $fileName);
            $validated['image_4'] = 'products/' . $fileName;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);

        return redirect()->route('admin.products')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function toggleProductAvailability(Product $product)
    {
        $product->update(['is_available' => !$product->is_available]);

        return redirect()->route('admin.products')
            ->with('success', $product->is_available
                ? "Produk {$product->name} sekarang tersedia."
                : "Produk {$product->name} ditandai habis.");
    }

    public function destroyProduct(Product $product)
    {
        $hasOrderHistory = \App\Models\OrderItem::where('product_id', $product->id)->exists();

        if ($hasOrderHistory) {
            return redirect()->route('admin.products')
                ->with('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat pesanan. Silakan gunakan tombol "Edit" untuk mengubah status menjadi Nonaktif.');
        }

        // ✅ HAPUS SEMUA GAMBAR DARI public/products/
        foreach ([$product->image_url, $product->image_2, $product->image_3, $product->image_4] as $img) {
            if ($img && file_exists(public_path($img))) {
                unlink(public_path($img));
            }
        }

        $product->delete();

        return redirect()->route('admin.products')
            ->with('success', 'Produk berhasil dihapus.');
    }

    // ==================== PROMOS ====================
    public function promos()
    {
        $promos = Promo::orderBy('created_at', 'desc')->get();

        return view('admin.promos.index', compact('promos'));
    }

    public function storePromo(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code',
            'discount_type' => 'required|in:FIXED,PERCENT',
            'discount_amount' => 'required|numeric|min:0' . (
                $request->input('discount_type') === 'PERCENT' ? '|max:100' : ''
            ),
            'min_order_amount' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'valid_until' => 'required|date',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        Promo::create($validated);

        return redirect()->route('admin.promos')
            ->with('success', 'Promo berhasil ditambahkan.');
    }

    public function updatePromo(Request $request, Promo $promo)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:promos,code,' . $promo->id,
            'discount_type' => 'required|in:FIXED,PERCENT',
            'discount_amount' => 'required|numeric|min:0' . (
                $request->input('discount_type') === 'PERCENT' ? '|max:100' : ''
            ),
            'min_order_amount' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'valid_until' => 'required|date',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $promo->update($validated);

        return redirect()->route('admin.promos')
            ->with('success', 'Promo berhasil diperbarui.');
    }

    public function destroyPromo(Promo $promo)
    {
        $promo->delete();

        return redirect()->route('admin.promos')
            ->with('success', 'Promo berhasil dihapus.');
    }

    // ==================== CONTACT & OUTLET ====================
    public function contact()
    {
        $settings = Setting::firstOrCreate(
            ['key' => 'outlet_info'],
            ['value' => [
                'business_name' => 'Dapur Th Sant — Dimsum & Kuliner UMKM',
                'tagline' => 'Dimsum Kukus & Goreng Fresh Halal Tiap Hari Tanpa Pengawet',
                'whatsapp' => '081234567890',
                'hotline' => '081234567890',
                'wa_template' => 'Halo Admin Dapur Th Sant, saya ingin pesan/tanya seputar menu dimsum kukus & frozen pack...',
                'email' => 'kontak@dapurthsant.com',
                'address' => 'Jl. Ciumbuleuit No. 88, Hegarmanah, Kec. Cidadap, Kota Bandung, Jawa Barat 40141',
                'landmark' => '150m utara Kampus Unpar, seberang mini market kuning',
                'latitude' => '-6.879',
                'longitude' => '107.605',
                'operational_days' => 'Selasa - Minggu',
                'opening_hours' => '09:00 WIB',
                'closing_hours' => '21:00 WIB',
                'offline_days' => 'Senin (Khusus Pesanan Katering & PO Terjadwal)',
                'instagram' => 'dapurthsant.dimsum',
                'tiktok' => 'dapurthsant_official',
                'link_aggregator' => 'https://linktr.ee/dapurthsant',
            ]]
        );

        $data = is_array($settings->value) ? $settings->value : json_decode($settings->value, true);

        return view('admin.contact', compact('data'));
    }

    public function updateContact(Request $request)
    {
        $validated = $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'business_name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'whatsapp' => 'required|string|max:20',
            'hotline' => 'nullable|string|max:20',
            'wa_template' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string',
            'landmark' => 'nullable|string',
            'latitude' => 'nullable|string|max:50',
            'longitude' => 'nullable|string|max:50',
            'operational_days' => 'nullable|string|max:100',
            'opening_hours' => 'nullable|string|max:50',
            'closing_hours' => 'nullable|string|max:50',
            'offline_days' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:100',
            'tiktok' => 'nullable|string|max:100',
            'link_aggregator' => 'nullable|url|max:255',
        ]);

        $settings = Setting::firstOrCreate(['key' => 'outlet_info']);
        $currentData = is_array($settings->value) ? $settings->value : json_decode($settings->value, true);

        if ($request->hasFile('logo')) {
            if (!empty($currentData['logo_url']) && file_exists(public_path($currentData['logo_url']))) {
                unlink(public_path($currentData['logo_url']));
            }
            $file = $request->file('logo');
            $fileName = time() . '_logo_' . $file->getClientOriginalName();
            $file->move(public_path('logos'), $fileName);
            $validated['logo_url'] = 'logos/' . $fileName;
        } else {
            $validated['logo_url'] = $currentData['logo_url'] ?? null;
        }

        unset($validated['logo']);
        $newData = array_merge($currentData, $validated);
        $settings->update(['value' => $newData]);

        return back()->with('success', 'Informasi kontak & logo berhasil diperbarui secara realtime!');
    }

    // ==================== USER MANAGEMENT ====================
    public function users()
    {
        $users = \App\Models\User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:12',
            'role' => 'required|in:ADMIN,OWNER',
        ]);

        \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users')->with('success', 'User baru berhasil ditambahkan!');
    }

    public function updateUser(Request $request, \App\Models\User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:ADMIN,OWNER',
            'password' => 'nullable|string|min:12',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'Data user berhasil diperbarui!');
    }

    public function destroyUser(\App\Models\User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus!');
    }

    // ==================== OWNER PORTAL ====================
    public function ownerLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = \App\Models\User::where('email', $validated['email'])
                    ->where('role', 'OWNER')
                    ->first();

        if (
            $user
            && !$user->isUsingUnsafeSeedPassword($validated['password'])
            && Hash::check($validated['password'], $user->password_hash)
        ) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('owner.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah, atau akun ini bukan role Owner.'
        ])->withInput();
    }

    public function ownerDashboard()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $todayRevenue = (float) Order::where('status', 'SELESAI')->whereDate('created_at', $today)->sum('total_amount');
        $yesterdayRevenue = (float) Order::where('status', 'SELESAI')->whereDate('created_at', $yesterday)->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todayOrdersSuccess = Order::where('status', 'SELESAI')->whereDate('created_at', $today)->count();
        $todayOrdersCancelled = Order::where('status', 'DIBATALKAN')->whereDate('created_at', $today)->count();
        $todayOrdersPending = max(0, $todayOrders - $todayOrdersSuccess - $todayOrdersCancelled);
        $orderEfficiency = $todayOrders > 0 ? round(($todayOrdersSuccess / $todayOrders) * 100, 1) : 0;

        $revenueGrowth = $yesterdayRevenue > 0
            ? round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1)
            : null;

        $activeProducts = Product::where('is_active', true)->where('is_available', true)->count();
        $availableProducts = Product::where('is_active', true)->where('is_available', true)->where('stock', '>', 20)->count();
        $limitedProducts = Product::where('is_active', true)->where('is_available', true)->whereBetween('stock', [1, 20])->count();
        $soldOutProducts = Product::where('is_active', true)->where(function ($query) {
            $query->where('stock', '<=', 0)->orWhere('is_available', false);
        })->count();
        $hourlySales = [];
        $completedToday = Order::where('status', 'SELESAI')
            ->whereDate('created_at', $today)
            ->get(['created_at']);
        for ($hour = 9; $hour <= 21; $hour++) {
            $hourlySales[$hour] = $completedToday->filter(
                fn ($order) => $order->created_at->format('G') == $hour
            )->count();
        }
        $maxHourlyVolume = max(1, max($hourlySales));

        $whatsappOrders = Order::whereDate('created_at', $today)->where('payment_method', 'COD')->count();
        $webOrders = Order::whereDate('created_at', $today)->where('payment_method', 'TRANSFER_BANK')->count();
        $onlineOrders = Order::whereDate('created_at', $today)->whereIn('payment_method', ['GOPAY', 'OVO', 'SHOPEEPAY', 'QRIS'])->count();
        $channelTotal = $whatsappOrders + $webOrders + $onlineOrders;
        $channelPercentages = [
            'whatsapp' => $channelTotal > 0 ? round(($whatsappOrders / $channelTotal) * 100) : 0,
            'web' => $channelTotal > 0 ? round(($webOrders / $channelTotal) * 100) : 0,
            'online' => $channelTotal > 0 ? round(($onlineOrders / $channelTotal) * 100) : 0,
        ];

        $completedDurations = Order::where('status', 'SELESAI')
            ->whereDate('created_at', $today)
            ->get(['created_at', 'updated_at'])
            ->map(fn ($order) => max(0, $order->created_at->diffInMinutes($order->updated_at)));
        $avgCookingTime = $completedDurations->count() > 0
            ? round($completedDurations->average(), 1)
            : null;
        $customerSatisfaction = null;

        $lowStockProducts = Product::where('stock', '>', 0)
            ->where('stock', '<=', 20)
            ->where('is_active', true)
            ->orderBy('stock')
            ->limit(5)
            ->get();
        $recentTransactions = Order::with(['orderItems.product'])->orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.owner.dashboard', compact(
            'todayRevenue', 'yesterdayRevenue', 'revenueGrowth', 'todayOrders', 'todayOrdersSuccess',
            'todayOrdersPending', 'todayOrdersCancelled', 'orderEfficiency', 'activeProducts',
            'availableProducts', 'limitedProducts', 'soldOutProducts',
            'hourlySales', 'maxHourlyVolume', 'whatsappOrders', 'webOrders', 'onlineOrders',
            'channelPercentages', 'avgCookingTime', 'customerSatisfaction',
            'lowStockProducts', 'recentTransactions'
        ));
    }

    public function ownerReports()
    {
        $topVolume = \App\Models\OrderItem::whereHas('order', function ($query) {
            $query->where('status', 'SELESAI');
        })->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')->orderByDesc('total_qty')->limit(1)->with('product')->first();

        $products = Product::withSum(['orderItems as total_sold' => function ($query) {
            $query->whereHas('order', function ($orderQuery) {
                $orderQuery->where('status', 'SELESAI');
            });
        }], 'quantity')->where('is_active', true)->orderByDesc('total_sold')->limit(5)->get();

        $totalRevenue = Order::where('status', 'SELESAI')->sum('total_amount');

        $totalOrders = Order::count();
        $successOrders = Order::where('status', 'SELESAI')->count();
        $cancelledOrders = Order::where('status', 'DIBATALKAN')->count();
        $fulfillmentRate = $totalOrders > 0 ? round(($successOrders / $totalOrders) * 100, 1) : 0;

        $totalDiscount = Order::where('status', 'SELESAI')->sum('discount_amount');
        return view('admin.owner.reports', compact(
            'topVolume', 'products', 'totalRevenue', 'totalOrders', 'successOrders',
            'cancelledOrders', 'fulfillmentRate', 'totalDiscount'
        ));
    }

    // ==================== EXPORT LAPORAN (OWNER) ====================
    public function exportLaporan(Request $request)
    {
        // Ambil data dari tanggal yang dipilih (default: bulan ini)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Ambil data order yang SELESAI dalam rentang tanggal tersebut
        $orders = Order::where('status', 'SELESAI')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->with('orderItems')
            ->orderBy('created_at', 'desc')
            ->get();

        // Nama file
        $filename = 'Laporan_Penjualan_' . $startDate . '_sd_' . $endDate . '.csv';

        // Header untuk download file CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Buka output stream
        $output = fopen('php://output', 'w');

        // Tulis Header Kolom
        fputcsv($output, ['No Order', 'Tanggal', 'Pelanggan', 'Metode Bayar', 'Total Item', 'Total Pendapatan (Rp)']);

        // Tulis Data
        foreach ($orders as $order) {
            $totalItem = $order->orderItems->sum('quantity');
            fputcsv($output, [
                $order->order_ref,
                $order->created_at->format('d/m/Y H:i'),
                $order->customer_name,
                str_replace('_', ' ', $order->payment_method),
                $totalItem,
                number_format($order->total_amount, 0, ',', '.')
            ]);
        }

        fclose($output);
        exit;
    }
}
