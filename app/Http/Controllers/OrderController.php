<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Promo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class OrderController extends Controller
{
    // ==========================================
    // ADMIN: Daftar Semua Order
    // ==========================================
    public function index(Request $request)
    {
        $validated = $request->validate([
            'metode_pembayaran' => 'nullable|in:Tunai,QRIS,Transfer',
            'status' => 'nullable|in:MENUNGGU_KONFIRMASI,DIPROSES,DIKIRIM,SELESAI,DIBATALKAN',
        ]);

        $query = Order::with(['promo', 'orderItems.product', 'payment'])
            ->when($validated['metode_pembayaran'] ?? null, function ($query, $metodePembayaran) {
                $query->where('metode_pembayaran', $metodePembayaran);
            })
            ->when($validated['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            });

        $totalTerfilter = (clone $query)->sum('total_amount');
        $orders = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.index', compact('orders', 'totalTerfilter'));
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
        if ($products->count() !== count($cart)) {
            return redirect()->route('cart.index')
                ->with('error', 'Ada produk di keranjang yang sudah tidak tersedia. Periksa kembali keranjang Anda.');
        }

        $unavailableProduct = $products->first(function ($product) use ($cart) {
            return !$product->is_available
                || !$product->is_active
                || $product->stock < ($cart[$product->id]['quantity'] ?? 0);
        });
        if ($unavailableProduct) {
            return redirect()->route('cart.index')
                ->with('error', "Produk {$unavailableProduct->name} tidak tersedia atau stoknya tidak mencukupi.");
        }

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

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'fulfillment_method' => 'required|in:DELIVERY,PICKUP',
            'kecamatan' => 'required_if:fulfillment_method,DELIVERY|string',
            'desa' => 'required_if:fulfillment_method,DELIVERY|string',
            'delivery_address' => 'required_if:fulfillment_method,DELIVERY|string',
            'pickup_note' => 'required_if:fulfillment_method,PICKUP|string',
            'metode_pembayaran' => 'required|in:Tunai,QRIS,Transfer',
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
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'payment_method' => $validated['metode_pembayaran'] === 'Tunai' ? 'COD' : 'TRANSFER_BANK',
                'payment_provider' => $validated['metode_pembayaran'],
                'promo_id' => $promo?->id,
                'subtotal_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'MENUNGGU_KONFIRMASI',
            ]);

            $subtotal = 0;
            foreach ($cart as $productId => $item) {
                $product = Product::findOrFail($productId);

                if (!$product->is_active || !$product->is_available) {
                    throw new \Exception("Produk {$product->name} sedang habis dan tidak dapat dipesan.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
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

        $stageMap = $order->fulfillment_method === 'PICKUP'
            ? [
                'MENUNGGU_KONFIRMASI' => 1,
                'DIPROSES' => 2,
                'DIKIRIM' => 3,
                'SELESAI' => 4,
                'DIBATALKAN' => 0
            ]
            : [
                'MENUNGGU_KONFIRMASI' => 1,
                'DIPROSES' => 2,
                'DIKIRIM' => 4,
                'SELESAI' => 5,
                'DIBATALKAN' => 0
            ];

        $currentStage = $stageMap[$order->status] ?? 1;

        if (request()->routeIs('admin.orders.show')) {
            return view('admin.orders.show', compact('order', 'currentStage'));
        }

        return view('orders.status', compact('order'));
    }

    public function receipt(Order $order)
    {
        $receiptPath = $order->payment?->receipt_image_path;
        if (!$receiptPath) {
            abort(404);
        }

        $relativePath = ltrim(str_replace('\\', '/', $receiptPath), '/');
        if (str_starts_with($relativePath, 'storage/')) {
            $relativePath = substr($relativePath, strlen('storage/'));
        }

        $filename = basename($relativePath);
        $candidates = match (true) {
            str_starts_with($relativePath, 'bukti_pembayaran/') => [
                storage_path('app/public/bukti_pembayaran/' . $filename),
            ],
            str_starts_with($relativePath, 'receipts/') => [
                public_path('receipts/' . $filename),
                storage_path('app/public/receipts/' . $filename),
            ],
            default => [],
        };

        $filePath = null;
        foreach ($candidates as $candidate) {
            if (is_file($candidate) && is_readable($candidate)) {
                $filePath = $candidate;
                break;
            }
        }

        if ($filePath === null) {
            abort(404);
        }

        $contentType = match (strtolower(pathinfo($filePath, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => abort(404),
        };

        return response()->file($filePath, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
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

    public function markCashPaid(Request $request, Order $order)
    {
        if ($order->payment_method !== 'COD' && $order->metode_pembayaran !== 'Tunai') {
            return back()->with('error', 'Aksi ini hanya berlaku untuk pembayaran tunai.');
        }

        if ($order->payment?->payment_status === 'VERIFIED') {
            return back()->with('error', 'Pembayaran pesanan ini sudah ditandai lunas.');
        }

        $validated = $request->validate([
            'amount_paid' => ['required', 'numeric', 'min:' . $order->total_amount],
        ], [
            'amount_paid.min' => 'Jumlah uang diterima tidak boleh kurang dari total tagihan.',
        ]);

        $amountPaid = (float) $validated['amount_paid'];
        $changeAmount = max(0, $amountPaid - (float) $order->total_amount);

        DB::transaction(function () use ($order, $amountPaid, $changeAmount) {
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'receipt_image_path' => null,
                    'payment_status' => 'VERIFIED',
                    'admin_notes' => 'Pembayaran tunai diterima oleh ' . (auth()->user()?->name ?? 'admin'),
                    'verified_at' => now(),
                ]
            );
        });

        return back()->with(
            'success',
            'Pembayaran tunai ditandai lunas. Kembalian: Rp ' . number_format($changeAmount, 0, ',', '.')
        );
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
        if ($order->payment_method === 'COD' || $order->metode_pembayaran === 'Tunai') {
            return back()->with('error', 'Pembayaran tunai tidak memerlukan upload bukti transfer.');
        }

        $request->validate([
            'receipt_image' => [
                'required',
                'bail',
                'file',
                'max:2048',
                function ($attribute, $value, $fail) {
                    $imageInfo = @getimagesize($value->getRealPath());

                    if ($imageInfo === false || !in_array($imageInfo[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                        $fail('Bukti pembayaran harus berupa gambar JPG atau PNG.');
                    }
                },
            ],
        ]);

        $file = $request->file('receipt_image');
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            return back()->withErrors(['receipt_image' => 'File bukti pembayaran tidak dapat dibaca.']);
        }

        $extension = match ($imageInfo[2]) {
            IMAGETYPE_JPEG => 'jpg',
            IMAGETYPE_PNG => 'png',
            default => null,
        };
        if ($extension === null) {
            return back()->withErrors(['receipt_image' => 'Bukti pembayaran harus berupa gambar JPG atau PNG.']);
        }

        $filename = Str::uuid()->toString() . '.' . $extension;
        $directory = storage_path('app/public/bukti_pembayaran');
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;
        try {
            $file->move($directory, $filename);
        } catch (FileException $e) {
            report($e);

            return back()->with('error', 'Bukti pembayaran gagal disimpan. Silakan coba lagi.');
        }
        $path = 'bukti_pembayaran/' . $filename;

        try {
            DB::transaction(function () use ($order, $path) {
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
            });

        } catch (QueryException $e) {
            if (is_file($destination) && !unlink($destination)) {
                logger()->warning('Could not remove uploaded receipt after payment save failed.', [
                    'path' => $path,
                ]);
            }
            report($e);

            return back()->with('error', 'Bukti pembayaran gagal disimpan. Silakan coba lagi atau hubungi admin.');
        }

        return redirect()->route('orders.status.show', $order->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Pesanan Anda sedang diverifikasi.');
    }
}
