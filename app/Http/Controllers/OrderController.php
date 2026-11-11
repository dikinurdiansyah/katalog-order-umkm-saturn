<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ==========================================
    // ADMIN: Daftar Semua Order
    // ==========================================
    public function index()
    {
        $orders = Order::with(['promo', 'orderItems.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.index', compact('orders'));
    }

    // ==========================================
    // CUSTOMER: Tampilkan Form Checkout
    // ==========================================
    public function create()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('catalog.index')
                ->with('error', 'Keranjang kosong. Tambahkan produk terlebih dahulu.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get();
        $promos = Promo::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->get();

        $subtotal = 0;
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $product = $products->firstWhere('id', $productId);
            if ($product) {
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];
            }
        }

        return view('orders.checkout', compact('cartItems', 'subtotal', 'promos'));
    }

    // ==========================================
    // CUSTOMER: Cek Kode Promo (AJAX)
    // ==========================================
    public function checkPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $promo = Promo::where('code', strtoupper(trim($request->promo_code)))
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', now()->toDateString());
            })
            ->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Kode promo tidak valid atau sudah kadaluarsa.',
            ]);
        }

        if ($promo->min_order_amount && $request->subtotal < $promo->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum pembelian Rp ' . number_format($promo->min_order_amount, 0, ',', '.') . ' untuk menggunakan promo ini.',
            ]);
        }

        $discount = 0;
        if ($promo->discount_type === 'PERCENT') {
            $discount = ($request->subtotal * $promo->discount_amount) / 100;
        } else {
            $discount = $promo->discount_amount;
        }

        $discount = min($discount, $request->subtotal);

        return response()->json([
            'success' => true,
            'message' => 'Promo berhasil diterapkan! Hemat Rp ' . number_format($discount, 0, ',', '.'),
            'discount' => $discount,
            'promo_name' => $promo->code,
        ]);
    }

    // ==========================================
    // CUSTOMER: Proses Checkout (Simpan Order)
    // ==========================================
    public function store(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang kosong.');
        }

        // ✅ REVISI: HANYA QRIS, COD SUDAH DIHAPUS TOTAL
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'fulfillment_method' => 'required|in:DELIVERY,PICKUP',
            'kecamatan' => 'required_if:fulfillment_method,DELIVERY|string',
            'desa' => 'required_if:fulfillment_method,DELIVERY|string',
            'delivery_address' => 'required_if:fulfillment_method,DELIVERY|string',
            'pickup_note' => 'required_if:fulfillment_method,PICKUP|string',
            'payment_method' => 'required|in:QRIS', // ✅ Hanya QRIS
            'promo_code' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // ✅ Gabungkan Kecamatan dan Desa ke Alamat agar rapi
            $finalAddress = ($validated['fulfillment_method'] === 'DELIVERY')
                ? 'Kec. ' . $validated['kecamatan'] . ' - Desa ' . $validated['desa'] . '. ' . $validated['delivery_address']
                : $validated['pickup_note'];

            $promo = null;
            if (!empty($validated['promo_code'])) {
                $promo = Promo::where('code', strtoupper(trim($validated['promo_code'])))->first();

                $isPromoValid = true;
                if (method_exists($promo, 'isValid')) {
                    $isPromoValid = $promo->isValid();
                } else {
                    $isPromoValid = $promo->is_active && (!$promo->valid_until || $promo->valid_until >= now()->toDateString());
                }

                if (!$promo || !$isPromoValid) {
                    throw new \Exception('Kode promo tidak valid atau sudah kedaluwarsa.');
                }

                $subtotalTemp = 0;
                foreach ($cart as $productId => $item) {
                    $product = Product::find($productId);
                    if ($product) {
                        $subtotalTemp += $product->price * $item['quantity'];
                    }
                }
                if ($promo->min_order_amount && $subtotalTemp < $promo->min_order_amount) {
                    throw new \Exception("Minimum order untuk promo ini adalah Rp " . number_format($promo->min_order_amount, 0, ',', '.'));
                }
            }

            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $finalAddress,
                'fulfillment_method' => $validated['fulfillment_method'],
                'payment_method' => 'QRIS', // ✅ Force QRIS
                'payment_provider' => 'QRIS', // ✅ Force QRIS
                'promo_id' => $promo?->id,
                'subtotal_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'MENUNGGU_KONFIRMASI',
            ]);

            $subtotal = 0;
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
                }

                if (!$product->is_active) {
                    throw new \Exception("Produk {$product->name} tidak aktif.");
                }

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'price_at_purchase' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            // ✅ Hitung ongkir dinamis berdasarkan desa yang dipilih (Area Ciwidey)
            $shippingCost = 0;
            if ($validated['fulfillment_method'] === 'DELIVERY' && !empty($validated['desa'])) {
                $desaData = [
                    'Ciwidey' => ['Ciwidey' => 5000, 'Panyocokan' => 5000, 'Rawabogo' => 6000, 'Patengan' => 7000, 'Panundaan' => 6000, 'Mekarsari' => 8000, 'Nengkelan' => 8000, 'Sukawening' => 9000],
                    'Rancabali' => ['Rancabali' => 10000, 'Alam Endah' => 12000, 'Lebakmuncang' => 12000, 'Cipelah' => 18000],
                    'Pasir Jambu' => ['Pasir Jambu' => 12000, 'Cangkuang' => 14000, 'Margamukti' => 15000]
                ];

                $kecamatan = $validated['kecamatan'];
                $desa = $validated['desa'];

                if (isset($desaData[$kecamatan][$desa])) {
                    $shippingCost = $desaData[$kecamatan][$desa];
                } else {
                    $shippingCost = 10000; // Default fallback
                }
            }

            $packagingCost = 2000;

            $discount = 0;
            if ($promo) {
                if (method_exists($promo, 'calculateDiscount')) {
                    $discount = $promo->calculateDiscount($subtotal);
                } else {
                    $discount = ($promo->discount_type === 'PERCENT')
                        ? ($subtotal * $promo->discount_amount) / 100
                        : $promo->discount_amount;
                    $discount = min($discount, $subtotal);
                }
            }

            $finalTotal = $subtotal + $packagingCost + $shippingCost - $discount;

            $order->update([
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'total_amount' => $finalTotal,
            ]);

            session()->forget('cart');
            session(['last_order_id' => $order->id]);

            DB::commit();

            return redirect()->route('orders.success', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    // ==========================================
    // CUSTOMER & ADMIN: Lihat Detail Order (SMART ROUTING)
    // ==========================================
    public function show(Order $order)
    {
        $order->load(['promo', 'orderItems.product', 'payment']);

        // ✅ PERBAIKAN PENTING: 'DIKIRIM' di-map ke 4 agar Stage "Pengantaran" aktif
        $stageMap = [
            'MENUNGGU_KONFIRMASI' => 1,
            'DIPROSES' => 2,       // Stage 2 (Dikukus) & 3 (Siap Kirim)
            'DIKIRIM' => 4,        // ✅ Stage 4 (Dalam Pengantaran)
            'SELESAI' => 5,        // Stage 5 (Selesai)
            'DIBATALKAN' => 0
        ];

        $currentStage = $stageMap[$order->status] ?? 1;

        if (request()->routeIs('admin.orders.show')) {
            return view('admin.orders.show', compact('order', 'currentStage'));
        }

        return view('orders.status', compact('order'));
    }

    // ==========================================
    // ADMIN: Update Status Order
    // ==========================================
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:MENUNGGU_KONFIRMASI,DIPROSES,DIKIRIM,SELESAI,DIBATALKAN',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($validated['status'] === 'DIBATALKAN' && empty($validated['rejection_reason'])) {
            return back()->with('error', 'Alasan pembatalan harus diisi.');
        }

        // Jika status berubah ke DIBATALKAN, kembalikan stok
        if ($validated['status'] === 'DIBATALKAN' && $order->status !== 'DIBATALKAN') {
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
        }

        $order->update($validated);

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    // ==========================================
    // ADMIN: Hapus Order
    // ==========================================
    public function destroy(Order $order)
    {
        foreach ($order->orderItems as $item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        $order->delete();

        return redirect()->route('admin.orders')
            ->with('success', 'Order berhasil dihapus dan stok dikembalikan.');
    }

    // ==========================================
    // ✅ CUSTOMER: Upload Bukti Pembayaran (Di Halaman Success)
    // ==========================================
    public function uploadBukti(Request $request, Order $order)
    {
        $validated = $request->validate([
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $path = $request->file('receipt_image')->store('bukti_pembayaran', 'public');

            $payment = $order->payment;

            if ($payment) {
                $payment->update([
                    'receipt_image_path' => $path,
                    'payment_status' => 'PENDING_REVIEW',
                    'amount_paid' => $order->total_amount,
                    'admin_notes' => 'Menunggu verifikasi admin',
                ]);
            } else {
                Payment::create([
                    'order_id' => $order->id,
                    'receipt_image_path' => $path,
                    'payment_status' => 'PENDING_REVIEW',
                    'amount_paid' => $order->total_amount,
                    'admin_notes' => 'Menunggu verifikasi admin',
                ]);
            }

            // ✅ PERBAIKAN: Redirect langsung ke halaman status order agar flow lebih mulus
            return redirect()->route('orders.status.show', $order->id)
                ->with('success', 'Bukti pembayaran berhasil diupload! Pesanan Anda sedang diverifikasi.');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupload bukti: ' . $e->getMessage());
        }
    }
}
