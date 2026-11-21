<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $order = Order::find($request->input('order_id'));
        if ($order && ($order->payment_method === 'COD' || $order->metode_pembayaran === 'Tunai')) {
            return back()->with('error', 'Pembayaran tunai tidak memerlukan upload bukti transfer.');
        }

        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id|unique:payments,order_id',
            'amount_paid' => 'required|numeric|min:0',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $file = $request->file('receipt_image');
        $path = $this->storeReceipt($file);
        if ($path === null) {
            return back()->with('error', 'Bukti pembayaran gagal disimpan. Silakan coba lagi.');
        }

        try {
            Payment::create([
                'order_id' => $validated['order_id'],
                'amount_paid' => $validated['amount_paid'],
                'receipt_image_path' => $path,
                'payment_status' => 'PENDING_REVIEW',
            ]);
        } catch (QueryException $e) {
            $this->deleteReceipt($path);
            report($e);

            return back()->with('error', 'Bukti pembayaran gagal disimpan. Silakan coba lagi.');
        }

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

        if (
            $validated['payment_status'] === 'VERIFIED'
            && (float) $validated['amount_paid'] < (float) $payment->order->total_amount
        ) {
            return back()->withErrors([
                'amount_paid' => 'Jumlah pembayaran terverifikasi tidak boleh kurang dari total tagihan.',
            ])->withInput();
        }

        if (
            $payment->payment_status !== 'PENDING_REVIEW'
            && $validated['payment_status'] !== $payment->payment_status
        ) {
            return back()->with('error', 'Status pembayaran yang sudah diputuskan tidak dapat diubah.');
        }

        $oldReceiptPath = null;
        $newReceiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $newReceiptPath = $this->storeReceipt($request->file('receipt_image'));
            if ($newReceiptPath === null) {
                return back()->with('error', 'Bukti pembayaran gagal disimpan. Silakan coba lagi.');
            }
            $validated['receipt_image_path'] = $newReceiptPath;
        }

        try {
            $updated = DB::transaction(function () use ($payment, $validated, &$oldReceiptPath) {
                $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                if (
                    $lockedPayment->payment_status !== 'PENDING_REVIEW'
                    && $validated['payment_status'] !== $lockedPayment->payment_status
                ) {
                    return false;
                }

                $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->firstOrFail();
                $oldReceiptPath = $lockedPayment->receipt_image_path;
                if (
                    $validated['payment_status'] === 'VERIFIED'
                    && (float) $validated['amount_paid'] < (float) $order->total_amount
                ) {
                    return false;
                }

                if ($validated['payment_status'] === 'VERIFIED' && $lockedPayment->payment_status !== 'VERIFIED') {
                    $validated['verified_at'] = now();
                    $validated['change_amount'] = max(
                        0,
                        (float) $validated['amount_paid'] - (float) $order->total_amount
                    );
                    $order->update(['status' => 'DIPROSES']);
                } elseif ($validated['payment_status'] === 'REJECTED') {
                    $validated['verified_at'] = null;
                    $validated['change_amount'] = 0;

                    if ($lockedPayment->payment_status !== 'REJECTED') {
                        foreach ($order->orderItems as $item) {
                            Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                        }

                        $order->update([
                            'status' => 'DIBATALKAN',
                            'rejection_reason' => $validated['admin_notes'] ?? 'Pembayaran ditolak',
                        ]);
                    }
                }

                return $lockedPayment->update($validated);
            });
        } catch (QueryException $e) {
            if ($newReceiptPath) {
                $this->deleteReceipt($newReceiptPath);
            }
            report($e);

            return back()->with('error', 'Pembayaran gagal diperbarui. Silakan coba lagi.');
        }

        if (!$updated) {
            if ($newReceiptPath) {
                $this->deleteReceipt($newReceiptPath);
            }

            return back()->with('error', 'Status pembayaran berubah. Muat ulang halaman sebelum mencoba lagi.');
        }

        if ($newReceiptPath) {
            $this->deleteReceipt($oldReceiptPath);
        }

        return redirect()->route('admin.payments')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        $receiptPath = $payment->receipt_image_path;
        $payment->delete();
        $this->deleteReceipt($receiptPath);

        return redirect()->route('admin.payments')
            ->with('success', 'Pembayaran berhasil dihapus.');
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

        try {
            $result = DB::transaction(function () use ($payment, $validated) {
                $lockedPayment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                if ($lockedPayment->payment_status !== 'PENDING_REVIEW') {
                    return ['error' => 'Status pembayaran sudah berubah. Muat ulang halaman sebelum mencoba lagi.'];
                }

                $order = Order::whereKey($lockedPayment->order_id)->lockForUpdate()->firstOrFail();

                if ($validated['payment_status'] === 'VERIFIED') {
                    $amountPaid = (float) $lockedPayment->amount_paid;
                    $totalBill = (float) $order->total_amount;
                    if ($amountPaid < $totalBill) {
                        return ['error' => 'Pembayaran tidak dapat diverifikasi karena jumlahnya kurang dari total tagihan.'];
                    }

                    $changeAmount = $amountPaid - $totalBill;
                    $lockedPayment->update([
                        'payment_status' => 'VERIFIED',
                        'change_amount' => $changeAmount,
                        'admin_notes' => $validated['admin_notes'] ?? 'Pembayaran diverifikasi',
                        'verified_at' => now(),
                    ]);
                    $order->update(['status' => 'DIPROSES']);

                    return ['change_amount' => $changeAmount];
                }

                $lockedPayment->update([
                    'payment_status' => 'REJECTED',
                    'admin_notes' => $validated['admin_notes'] ?? 'Pembayaran ditolak',
                    'verified_at' => null,
                    'change_amount' => 0,
                ]);
                $order->update(['status' => 'MENUNGGU_KONFIRMASI']);

                return ['rejected' => true];
            });
        } catch (QueryException $e) {
            report($e);

            return back()->with('error', 'Pembayaran gagal diperbarui. Silakan coba lagi.');
        }

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        if (array_key_exists('change_amount', $result)) {
            return back()->with('success', 'Pembayaran diverifikasi! Kembalian: Rp ' . number_format($result['change_amount'], 0, ',', '.'));
        }

        if (!empty($result['rejected'])) {
            return back()->with('error', 'Pembayaran ditolak. Customer dapat upload ulang bukti transfer.');
        }

        return back()->with('error', 'Status tidak valid.');
    }

    private function storeReceipt(UploadedFile $file): ?string
    {
        $filename = Str::uuid()->toString() . '.' . $file->extension();
        $path = $file->storeAs('receipts', $filename, 'local');

        return $path === false ? null : $path;
    }

    private function deleteReceipt(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);
        if (str_starts_with($path, 'receipts/')) {
            foreach (['local', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($path) && !Storage::disk($disk)->delete($path)) {
                    logger()->warning('Could not remove receipt from storage disk.', [
                        'disk' => $disk,
                        'path' => $path,
                    ]);
                }
            }

            $legacyPath = public_path('receipts/' . basename($path));
            if (is_file($legacyPath) && !unlink($legacyPath)) {
                logger()->warning('Could not remove legacy public receipt.', ['path' => $path]);
            }
        } elseif (str_starts_with($path, 'bukti_pembayaran/')) {
            foreach (['local', 'public'] as $disk) {
                if (Storage::disk($disk)->exists($path) && !Storage::disk($disk)->delete($path)) {
                    logger()->warning('Could not remove receipt from storage disk.', [
                        'disk' => $disk,
                        'path' => $path,
                    ]);
                }
            }
        }
    }
}
