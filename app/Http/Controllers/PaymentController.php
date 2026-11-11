<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function show(Payment $payment)
    {
        $payment->load('order');
        return view('admin.payments.show', compact('payment'));
    }

    public function create(Order $order)
    {
        if ($order->payment) {
            return redirect()->route('admin.payments.show', $order->payment)
                ->with('info', 'Order ini sudah memiliki pembayaran.');
        }

        return view('admin.payments.create', compact('order'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:payments,order_id',
            'amount_paid' => 'required|numeric|min:0',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ✅ SIMPAN LANGSUNG KE public/receipts (TANPA SYMLINK)
        $file = $request->file('receipt_image');
        $fileName = time() . '_receipt_' . $file->getClientOriginalName();

        // Pastikan folder receipts ada di dalam public/
        if (!file_exists(public_path('receipts'))) {
            mkdir(public_path('receipts'), 0755, true);
        }

        $file->move(public_path('receipts'), $fileName);
        $path = 'receipts/' . $fileName;

        Payment::create([
            'order_id' => $validated['order_id'],
            'amount_paid' => $validated['amount_paid'],
            'receipt_image_path' => $path,
            'payment_status' => 'PENDING_REVIEW',
        ]);

        return redirect()->route('admin.payments')
            ->with('success', 'Bukti pembayaran berhasil diupload.');
    }

    public function edit(Payment $payment)
    {
        $payment->load('order');
        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'receipt_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'payment_status' => 'required|in:PENDING_REVIEW,VERIFIED,REJECTED',
            'admin_notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_image')) {
            // Hapus file lama jika ada
            if ($payment->receipt_image_path && file_exists(public_path($payment->receipt_image_path))) {
                unlink(public_path($payment->receipt_image_path));
            }

            // Upload file baru ke public/receipts
            $file = $request->file('receipt_image');
            $fileName = time() . '_receipt_' . $file->getClientOriginalName();
            $file->move(public_path('receipts'), $fileName);
            $validated['receipt_image_path'] = 'receipts/' . $fileName;
        }

        // Logic VERIFIED
        if ($validated['payment_status'] === 'VERIFIED' && $payment->payment_status !== 'VERIFIED') {
            $validated['verified_at'] = now();
            $totalBill = $payment->order->total_amount;
            $amountPaid = $validated['amount_paid'];
            $changeAmount = $amountPaid - $totalBill;
            $validated['change_amount'] = $changeAmount > 0 ? $changeAmount : 0;

            if ($payment->order) {
                $payment->order->update(['status' => 'DIPROSES']);
            }
        }

        // Logic REJECTED
        if ($validated['payment_status'] === 'REJECTED' && $payment->payment_status !== 'REJECTED') {
            $validated['verified_at'] = null;
            $validated['change_amount'] = 0;

            if ($payment->order) {
                foreach ($payment->order->orderItems as $item) {
                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }

                $payment->order->update([
                    'status' => 'DIBATALKAN',
                    'rejection_reason' => $validated['admin_notes'] ?? 'Pembayaran ditolak',
                ]);
            }
        }

        $payment->update($validated);

        return redirect()->route('admin.payments')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->receipt_image_path && file_exists(public_path($payment->receipt_image_path))) {
            unlink(public_path($payment->receipt_image_path));
        }

        $payment->delete();

        return redirect()->route('admin.payments')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    // ==========================================
    // ✅ CUSTOMER: Upload / Upload Ulang Bukti Bayar (TANPA LOGIN)
    // ==========================================
    public function storeGuest(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount_paid' => 'required|numeric|min:0',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->payment && $order->payment->payment_status === 'VERIFIED') {
            return back()->with('error', 'Order ini sudah lunas. Tidak perlu upload lagi.');
        }

        // ✅ SIMPAN LANGSUNG KE public/receipts
        $file = $request->file('receipt_image');
        $fileName = time() . '_receipt_' . $file->getClientOriginalName();

        if (!file_exists(public_path('receipts'))) {
            mkdir(public_path('receipts'), 0755, true);
        }

        $file->move(public_path('receipts'), $fileName);
        $path = 'receipts/' . $fileName;

        if ($order->payment) {
            // Hapus gambar lama yang ditolak
            if ($order->payment->receipt_image_path && file_exists(public_path($order->payment->receipt_image_path))) {
                unlink(public_path($order->payment->receipt_image_path));
            }

            $order->payment->update([
                'amount_paid' => $validated['amount_paid'],
                'receipt_image_path' => $path,
                'payment_status' => 'PENDING_REVIEW',
                'admin_notes' => null,
                'verified_at' => null,
                'change_amount' => 0,
            ]);
        } else {
            Payment::create([
                'order_id' => $validated['order_id'],
                'amount_paid' => $validated['amount_paid'],
                'receipt_image_path' => $path,
                'payment_status' => 'PENDING_REVIEW',
            ]);
        }

        $order->update(['status' => 'MENUNGGU_KONFIRMASI']);

        return redirect()->route('orders.status.show', $order)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    // ==========================================
    // ✅ ADMIN: Verifikasi Cepat
    // ==========================================
    public function verify(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:VERIFIED,REJECTED',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        if ($validated['payment_status'] === 'VERIFIED') {
            $totalBill = $payment->order->total_amount;
            $amountPaid = $payment->amount_paid;
            $changeAmount = $amountPaid - $totalBill;

            $payment->update([
                'payment_status' => 'VERIFIED',
                'change_amount' => $changeAmount > 0 ? $changeAmount : 0,
                'admin_notes' => $validated['admin_notes'] ?? 'Pembayaran diverifikasi',
                'verified_at' => now(),
            ]);

            $payment->order->update(['status' => 'DIPROSES']);

            return back()->with('success', '✅ Pembayaran diverifikasi! Kembalian: Rp ' . number_format($changeAmount > 0 ? $changeAmount : 0, 0, ',', '.'));
        }

        if ($validated['payment_status'] === 'REJECTED') {
            $payment->update([
                'payment_status' => 'REJECTED',
                'admin_notes' => $validated['admin_notes'] ?? 'Pembayaran ditolak',
                'verified_at' => null,
                'change_amount' => 0,
            ]);

            $payment->order->update([
                'status' => 'MENUNGGU_KONFIRMASI',
            ]);

            return back()->with('error', '❌ Pembayaran ditolak. Customer dapat upload ulang bukti transfer.');
        }

        return back()->with('error', 'Status tidak valid.');
    }
}
