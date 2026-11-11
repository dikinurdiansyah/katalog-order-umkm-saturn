@extends('layouts.app')

@section('title', 'Konfirmasi Order - Berhasil')

@section('navbar')
<div class="top-navbar">
    <a href="{{ route('catalog.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="page-title">KONFIRMASI ORDER</span>
    <div class="user-icon">
        <i class="bi bi-shield-check text-success"></i>
    </div>
</div>
@endsection

@section('content')
@php
    // ✅ Mengambil nomor WhatsApp dinamis dari pengaturan Admin
    $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
    $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
    $adminWa = preg_replace('/[^0-9]/', '', $data['whatsapp'] ?? '081234567890');
    if (str_starts_with($adminWa, '0')) {
        $adminWa = '62' . substr($adminWa, 1);
    }
@endphp

<!-- ✅ WRAPPER RESPONSIF -->
<div class="container py-4 py-md-5 px-3">
    <div class="mx-auto" style="max-width: 650px;">

        <!-- ✅ NOTIFIKASI SUKSES / ERROR -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm">
                <i class="bi bi-exclamation-circle-fill me-2"></i> Gagal mengupload:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Success Icon -->
        <div class="text-center mb-4">
            <div style="width: 90px; height: 90px; background: var(--green-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 4px 12px rgba(45, 106, 79, 0.15);">
                <i class="bi bi-check-lg" style="font-size: 3rem; color: var(--green-stock);"></i>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-4">
            <span class="badge-status d-inline-flex align-items-center gap-2 mb-3" style="padding: 6px 14px; border-radius: 50px;">
                <span style="width: 8px; height: 8px; background: var(--green-stock); border-radius: 50%;"></span>
                STATUS: MASUK SISTEM
            </span>
            <h2 class="fw-bold text-brown mb-2" style="font-size: 1.5rem;">PESANAN BERHASIL DITERIMA!</h2>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                Terima kasih, <strong>{{ $order->customer_name }}</strong>! Dapur Th Sant sedang menyiapkan pesanan Anda.
            </p>
        </div>

        <!-- Order Info Card -->
        <div class="card-warm p-4 mb-4" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="font-mono fw-bold" style="font-size: 0.75rem; color: var(--text-muted); letter-spacing: 1px;">KODE PESANAN</span>
                <span class="badge-status">MENUNGGU KONFIRMASI</span>
            </div>

            <div class="card-warm p-3 mb-3 d-flex justify-content-between align-items-center" style="background: var(--cream-light); border-radius: 12px;">
                <div>
                    <div class="font-mono fw-bold text-brown" style="font-size: 1.2rem; letter-spacing: 0.5px;">{{ $order->order_ref }}</div>
                    <small class="text-muted" style="font-size: 0.8rem;">
                        {{ $order->created_at->format('d F Y, H:i') }} WIB
                    </small>
                </div>
                <button type="button" class="btn-brown-outline px-3 py-2 d-flex align-items-center gap-2" style="font-size: 0.8rem; border-radius: 8px;" onclick="copyOrderCode()">
                    <i class="bi bi-clipboard"></i> SALIN
                </button>
            </div>

            <div class="card-warm p-3 d-flex align-items-center gap-3" style="border-radius: 12px;">
                <i class="bi bi-clock text-brown" style="font-size: 1.3rem;"></i>
                <div>
                    <div class="section-label mb-0">WAKTU PENYAJIAN</div>
                    <div class="fw-bold text-brown">Estimasi Dikukus Hangat: 15 - 25 Menit</div>
                </div>
            </div>
        </div>

        <!-- Menu Details -->
        <div class="card-warm p-4 mb-4" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-brown d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-receipt"></i> Rincian Menu
                </h5>
                <span class="badge-status">{{ $order->orderItems->count() }} Varian</span>
            </div>

            @foreach($order->orderItems as $item)
                <div class="d-flex gap-3 mb-3 pb-3" style="border-bottom: 1px dashed var(--border-color);">
                    <div style="width: 60px; height: 60px; flex-shrink: 0; background: var(--cream-dark); border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                        @php
                            $img = $item->product->image_url ?? null;
                        @endphp
                        @if($img)
                            <img src="{{ asset($img) }}" class="w-100 h-100" style="object-fit: cover;">
                        @else
                            <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">1:1</span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-brown" style="font-size: 0.95rem;">{{ $item->product->name }}</div>
                        <small class="text-muted d-block mb-1">{{ $item->product->description ? Str::limit($item->product->description, 30) : 'Isi 4 pcs' }}</small>
                        <span class="badge-stock" style="background: var(--cream-dark); color: var(--brown-dark); font-size: 0.7rem; padding: 4px 8px; border-radius: 6px;">
                            {{ $item->quantity }}x Porsi
                        </span>
                    </div>
                    <div class="fw-bold text-brown font-mono text-end" style="font-size: 0.95rem;">
                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach

            <!-- Totals -->
            <div class="mt-3 pt-2">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal Pesanan</span>
                    <span class="font-mono text-brown">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                </div>

                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-success">Diskon Promo</span>
                        <span class="font-mono text-success">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Biaya Kemasan Higienis</span>
                    <span class="font-mono text-brown">Rp 2.000</span>
                </div>

                @if($order->fulfillment_method === 'PICKUP')
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Biaya Pengambilan (Self-Pickup)</span>
                        <span class="font-mono text-success">Rp 0</span>
                    </div>
                @else
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span class="font-mono text-brown">Rp {{ number_format($order->total_amount - $order->subtotal_amount - $order->discount_amount - 2000, 0, ',', '.') }}</span>
                    </div>
                @endif

                <hr class="divider" style="border-top: 2px dashed var(--border-color);">

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-brown" style="font-size: 0.9rem; letter-spacing: 1px;">TOTAL PEMBAYARAN</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Metode: {{ str_replace('_', ' ', $order->payment_method) }}</small>
                    </div>
                    <div class="fw-bold text-brown" style="font-size: 1.4rem;">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping / Pickup Info -->
        <div class="card-warm p-4 mb-4" style="border-radius: 16px;">
            @if($order->fulfillment_method === 'PICKUP')
                <h5 class="fw-bold mb-3 text-brown d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-shop"></i> Lokasi Pengambilan
                </h5>
                <div class="mb-3">
                    <div class="fw-bold text-brown">Dapur Th Sant - Outlet</div>
                    <div class="text-muted" style="font-size: 0.9rem;">Jl. Raya Ciwidey, Bandung</div>
                </div>
                <div>
                    <div class="section-label">CATATAN PENGAMBILAN</div>
                    <div class="card-warm p-3" style="background: var(--cream-light); border-radius: 8px;">
                        <div class="fst-italic" style="font-size: 0.9rem; color: var(--text-dark);">
                            "{{ $order->shipping_address }}"
                        </div>
                    </div>
                </div>
            @else
                <h5 class="fw-bold mb-3 text-brown d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-geo-alt-fill"></i> Tujuan Pengiriman
                </h5>
                <div>
                    <div class="fw-bold text-brown mb-1">
                        {{ $order->customer_name }}
                        <span class="font-mono text-muted" style="font-size: 0.85rem; font-weight: 400;">({{ $order->customer_phone }})</span>
                    </div>
                    <div class="text-muted" style="font-size: 0.9rem; line-height: 1.5;">{{ $order->shipping_address }}</div>
                </div>
            @endif
        </div>

        <!-- ✅ PAYMENT & UPLOAD SECTION (KHUSUS QRIS) -->
        @if($order->payment_method === 'QRIS')
            <div class="card-warm p-4 mb-4" style="background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 16px;">
                <h5 class="fw-bold mb-3 text-brown d-flex align-items-center gap-2" style="font-size: 1rem;">
                    <i class="bi bi-bank"></i> Instruksi & Upload Pembayaran
                </h5>

                {{-- 1. ALERT JIKA PAYMENT DITOLAK (UPLOAD ULANG) --}}
                @if(isset($order->payment) && $order->payment->payment_status === 'REJECTED')
                    <div class="alert alert-danger mb-3" style="border-radius: 12px;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-x-circle-fill" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Pembayaran Sebelumnya Ditolak</strong><br>
                                <small class="text-muted">Catatan Admin: <em>"{{ $order->payment->admin_notes ?? 'Tidak ada catatan' }}"</em></small><br>
                                <small class="mt-2 d-block fw-bold">Silakan upload ulang bukti transfer yang benar di bawah ini.</small>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 2. ALERT JIKA PAYMENT SUDAH VERIFIED (LUNAS) --}}
                @if(isset($order->payment) && $order->payment->payment_status === 'VERIFIED')
                    <div class="alert alert-success mb-3" style="border-radius: 12px;">
                        <div class="d-flex gap-2">
                            <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i>
                            <div>
                                <strong>Pembayaran Terverifikasi</strong><br>
                                <small class="text-muted">Pesanan Anda sedang diproses oleh dapur kami. Terima kasih!</small>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- ✅ 3. TAMPILKAN GAMBAR QRIS (DENGAN FALLBACK AGAR TIDAK RUSAK) --}}
                    <div class="card-warm p-3 mb-3 text-center" style="background: white; border-radius: 12px; border: 2px dashed var(--brown-dark);">
                        <p class="fw-bold mb-2" style="color: var(--brown-dark); font-size: 0.95rem;">
                            <i class="bi bi-qr-code-scan"></i> Arahkan Kamera HP untuk Scan QRIS
                        </p>

                        @php
                            $qrisPath = public_path('images/qris.png');
                            $qrisExists = file_exists($qrisPath);
                        @endphp

                        @if($qrisExists)
                            <img src="{{ asset('images/qris.png') }}" alt="QRIS Dapur Th Sant"
                                 class="img-fluid mb-3"
                                 style="max-width: 250px; border: 1px solid #ddd; border-radius: 8px; background: white; padding: 10px;">
                        @else
                            <div class="mb-3 p-4 mx-auto" style="max-width: 250px; background: white; border-radius: 12px; border: 2px dashed var(--brown-dark);">
                                <i class="bi bi-qr-code" style="font-size: 5rem; color: var(--brown-dark);"></i>
                                <p class="text-muted small mb-0 mt-2">QRIS Code<br><small>(Belum diupload admin)</small></p>
                            </div>
                        @endif

                        <hr style="border-top: 1px dashed #ccc;">

                        <div class="d-flex justify-content-between align-items-center px-2">
                            <span class="text-muted fw-bold">Total Transfer</span>
                            <span class="font-mono fw-bold text-brown" style="font-size: 1.2rem;">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.8rem;">
                            a.n. <strong>Dapur Th Sant</strong>
                        </small>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.75rem; color: var(--green-stock); font-weight: bold;">
                            <i class="bi bi-camera"></i> Scan langsung membuka aplikasi e-wallet / m-banking
                        </small>
                    </div>

                    {{-- FORM UPLOAD BUKTI --}}
                    <form method="POST" action="{{ route('orders.uploadBukti', $order->id) }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="section-label">
                                {{ isset($order->payment) && $order->payment->payment_status === 'REJECTED' ? 'UPLOAD ULANG BUKTI TRANSFER' : 'UPLOAD BUKTI TRANSFER' }}
                                <span class="text-danger">*</span>
                            </label>
                            <!-- ✅ TAMBAHAN ID: receipt_image -->
                            <input type="file" name="receipt_image" id="receipt_image" class="form-control-warm w-100" accept="image/jpeg,image/png,image/jpg" required style="padding: 10px;">
                            <small class="text-muted" style="font-size: 0.75rem;">Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>

                        <!-- ✅ TAMBAHAN ID: btnKirimBukti -->
                        <button type="submit" id="btnKirimBukti" class="btn-brown w-100 d-flex justify-content-center align-items-center gap-2 py-3" style="border-radius: 12px; font-weight: 700;">
                            <i class="bi bi-upload"></i>
                            {{ isset($order->payment) && $order->payment->payment_status === 'REJECTED' ? 'KIRIM ULANG BUKTI PEMBAYARAN' : 'KIRIM BUKTI PEMBAYARAN' }}
                        </button>
                    </form>

                    <small class="text-muted d-block text-center mt-3" style="font-size: 0.8rem;">
                        <i class="bi bi-info-circle"></i> Setelah diupload, status akan menunggu verifikasi admin.
                    </small>
                @endif
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="d-grid gap-3 mb-4">
            <!-- ✅ DIPERBAIKI: Route diubah dari 'orders.show' menjadi 'orders.status.show' -->
            <a href="{{ route('orders.status.show', $order->id) }}" class="btn-brown w-100 d-flex justify-content-center align-items-center gap-2 py-3" style="border-radius: 12px; font-size: 1rem; font-weight: 700; text-decoration: none;">
                <i class="bi bi-truck"></i> Lacak Status Pesanan
            </a>

            <!-- ✅ TOMBOL WHATSAPP DINAMIS -->
            <a href="https://wa.me/{{ $adminWa }}?text=Halo%20{{ urlencode($data['business_name'] ?? 'Dapur Th Sant') }},%20saya%20mau%20konfirmasi%20order%20{{ $order->order_ref }}"
               class="btn w-100 d-flex justify-content-center align-items-center gap-2 py-3" style="background: white; color: var(--brown-dark); border: 1px solid var(--border-color); border-radius: 12px; font-size: 0.95rem; font-weight: 600; text-decoration: none;">
                <i class="bi bi-whatsapp" style="color: #25D366; font-size: 1.2rem;"></i>
                <span>Kirim Konfirmasi via WhatsApp</span>
            </a>
        </div>

        <div class="text-center pb-4">
            <a href="{{ route('catalog.index') }}" class="text-brown fw-bold" style="text-decoration: none; font-size: 0.9rem; letter-spacing: 1px; border-bottom: 2px solid var(--brown-dark); padding-bottom: 2px;">
                KEMBALI KE KATALOG
            </a>
        </div>

    </div>
</div>

@push('scripts')
<script>
    // 1. Fungsi Copy Kode Order
    function copyOrderCode() {
        const code = '{{ $order->order_ref }}';
        navigator.clipboard.writeText(code).then(() => {
            const btn = event.currentTarget;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i> TERSALIN!';
            btn.style.background = 'var(--green-stock)';
            btn.style.color = 'white';
            btn.style.borderColor = 'var(--green-stock)';

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = 'white';
                btn.style.color = 'var(--brown-dark)';
                btn.style.borderColor = 'var(--brown-dark)';
            }, 2000);
        }).catch(() => {
            const textArea = document.createElement("textarea");
            textArea.value = code;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand("copy");
            document.body.removeChild(textArea);
            alert('Kode order disalin: ' + code);
        });
    }

    // ✅ 2. FUNGSI ANTI-KLIK: Tombol kirim disabled kalau belum pilih file
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('receipt_image');
        const submitBtn = document.getElementById('btnKirimBukti');

        if (fileInput && submitBtn) {
            // Set tombol disabled di awal (abu-abu & tidak bisa diklik)
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.5';
            submitBtn.style.cursor = 'not-allowed';

            // Aktifkan tombol HANYA kalau ada file yang dipilih
            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    submitBtn.disabled = false;
                    submitBtn.style.opacity = '1';
                    submitBtn.style.cursor = 'pointer';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.5';
                    submitBtn.style.cursor = 'not-allowed';
                }
            });
        }
    });
</script>
@endpush
@endsection
