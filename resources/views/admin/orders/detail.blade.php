@extends('layouts.admin')

@section('title', 'Detail Order')

@section('content')
@if(!$order)
<div class="admin-card p-5 text-center">
    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-muted);"></i>
    <h4 class="mt-3 text-muted">Belum Ada Order</h4>
    <p class="text-muted">Belum ada pesanan yang masuk ke sistem.</p>
    <a href="{{ route('admin.orders') }}" class="btn-admin mt-3">
        <i class="bi bi-list-ul"></i> Lihat Daftar Order
    </a>
</div>
@else

<style>
    .detail-header {
        background: linear-gradient(135deg, #3d2817 0%, #5c3d2e 100%);
        color: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
    }
    .stepper-line {
        position: absolute;
        top: 20px;
        left: 40px;
        right: 40px;
        height: 3px;
        background: var(--border-color);
        z-index: 0;
    }
    .stepper-line-active {
        position: absolute;
        top: 20px;
        left: 40px;
        width: {{ (($currentStage - 1) / 4) * 100 }}%;
        max-width: calc(100% - 80px);
        height: 3px;
        background: var(--brown-dark);
        z-index: 1;
    }
    .stepper-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin: 0 auto 8px;
        position: relative;
        z-index: 2;
    }
    .quick-actions-bar {
        background: linear-gradient(135deg, #3d2817 0%, #5c3d2e 100%);
        color: white;
        border-radius: 12px;
        padding: 20px 24px;
        margin-top: 24px;
    }
    .timeline-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 6px;
    }
    .timeline-line {
        position: absolute;
        left: 4px;
        top: 16px;
        bottom: 0;
        width: 2px;
        background: var(--border-color);
    }
    .stepper-container {
        display: flex !important;
        justify-content: space-between !important;
        width: 100% !important;
    }
    .stepper-container > div {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        padding: 0 4px !important;
    }
</style>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb font-mono" style="font-size: 0.8rem;">
        <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}" class="text-decoration-none">← KEMBALI KE DAFTAR ORDER</a></li>
        <li class="breadcrumb-item active">DETAIL PESANAN #{{ $order->order_ref }}</li>
    </ol>
</nav>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex gap-2">
        <button class="btn-admin-outline btn-sm"><i class="bi bi-printer"></i> Cetak Nota Dapur / Struk</button>
        <button class="btn-admin-outline btn-sm"><i class="bi bi-tag"></i> Cetak Slip Dapur & Label</button>
    </div>
</div>

<!-- Order Header Card -->
<div class="detail-header">
    <div class="row">
        <div class="col-md-8">
            <h2 class="fw-bold mb-2" style="font-size: 1.5rem;">Pesanan #{{ $order->order_ref }}</h2>
            <div class="d-flex gap-2 mb-2 flex-wrap">
                <span class="badge" style="background: var(--orange-fav); color: white;">PRIORITAS MAKAN SIANG</span>
                <span class="badge" style="background: rgba(255,255,255,0.2);">Batch Pengukuhan #04</span>
            </div>
            <div class="font-mono" style="font-size: 0.8rem; opacity: 0.9;">
                TIPE: INSTANT COURIER • SUMBER: WEB TOKO PUBLIK<br>
                DIBUAT: {{ $order->created_at->format('d/m/Y H:i') }} WIB
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div class="p-3 rounded" style="background: rgba(255,255,255,0.1);">
                <div class="font-mono" style="font-size: 0.7rem; opacity: 0.8; margin-bottom: 4px;">METODE PEMBAYARAN</div>
                <div class="fw-bold mb-2">{{ str_replace('_', ' ', $order->payment_method) }}</div>
                <span class="badge" style="background: {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : '#e76f51' }}; color: white; font-size: 0.7rem;">
                    {{ $order->payment?->payment_status ?? 'BELUM LUNAS' }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Progress Stepper -->
<div class="admin-card mb-4" style="position: relative; z-index: 10;">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h5 class="fw-bold mb-1">PROSES PESANAN AKTIF: TAHAP {{ $currentStage }} DARI 5</h5>
            <small class="text-muted font-mono">KLOTER PENGUKUSAN #4 • DAPUR PUSAT SUKAJADI</small>
        </div>

        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" style="z-index: 999; position: relative;">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger p-2 mb-2" style="font-size: 0.8rem; border: 1px solid red;">
                    @foreach ($errors->all() as $error)
                        <div>⚠️ {{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success p-2 mb-2" style="font-size: 0.8rem;">
                    ✅ {{ session('success') }}
                </div>
            @endif
            <div class="d-flex gap-2 align-items-center">
                <select name="status" class="form-select form-select-sm" style="width: 220px; border: 2px solid var(--brown-dark);">
                    <option value="MENUNGGU_KONFIRMASI" {{ $order->status == 'MENUNGGU_KONFIRMASI' ? 'selected' : '' }}>1. Menunggu Konfirmasi</option>
                    <option value="DIPROSES" {{ $order->status == 'DIPROSES' ? 'selected' : '' }}>2. Sedang Diproses / Dikukus</option>
                    <option value="DIKIRIM" {{ $order->status == 'DIKIRIM' ? 'selected' : '' }}>3. Dikirim / Pengantaran</option>
                    <option value="SELESAI" {{ $order->status == 'SELESAI' ? 'selected' : '' }}>4. Selesai</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm" style="font-weight: bold; padding: 6px 16px;">
                     SIMPAN
                </button>
            </div>
        </form>
    </div>

    <div class="stepper-container position-relative px-4">
        <div class="stepper-line"></div>
        <div class="stepper-line-active"></div>

        @php
            $stages = [
                ['num' => 1, 'title' => 'KONFIRMASI', 'sub' => 'Terkonfirmasi', 'time' => $order->created_at->format('H:i') . ' WIB'],
                ['num' => 2, 'title' => 'SEDANG DIKUKUS', 'sub' => 'Aktif Sekarang', 'time' => 'Kloter #4 • ' . $order->updated_at->format('H:i') . ' WIB'],
                ['num' => 3, 'title' => 'SIAP DIKIRIM', 'sub' => 'Pengepakan Mika & Saus', 'time' => 'Est. 12:05 WIB'],
                ['num' => 4, 'title' => 'PENGANTARAN', 'sub' => 'Kurir Rian Kusuma', 'time' => 'Est. 12:12 WIB'],
                ['num' => 5, 'title' => 'SELESAI', 'sub' => 'Verifikasi COD', 'time' => 'Est. 12:20 WIB'],
            ];
        @endphp

        @foreach($stages as $stage)
        <div class="text-center" style="position: relative; z-index: 2;">
            <div class="stepper-dot" style="
                background: {{ $stage['num'] == $currentStage ? 'var(--orange-fav)' : ($stage['num'] < $currentStage ? 'var(--brown-dark)' : 'var(--cream-dark)') }};
                color: {{ ($stage['num'] <= $currentStage) ? 'white' : 'var(--text-muted)' }};
            ">
                @if($stage['num'] < $currentStage)
                    <i class="bi bi-check-lg"></i>
                @elseif($stage['num'] == $currentStage)
                    <i class="bi bi-cup-hot-fill"></i>
                @else
                    <i class="bi bi-{{ $stage['num'] == 3 ? 'box' : ($stage['num'] == 4 ? 'truck' : 'check-circle') }}" style="font-size: 0.9rem;"></i>
                @endif
            </div>
            <div class="fw-bold" style="color: var(--brown-dark); font-size: 0.75rem;">{{ $stage['num'] }}. {{ $stage['title'] }}</div>
            <div class="font-mono" style="color: var(--text-muted); font-size: 0.65rem;">{{ $stage['time'] }}</div>
            @if($stage['num'] == $currentStage)
                <div style="color: var(--orange-fav); font-weight: 600; font-size: 0.7rem;">{{ $stage['sub'] }}</div>
            @else
                <div style="color: var(--text-muted); font-size: 0.65rem;">{{ $stage['sub'] }}</div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<!-- Sisa konten -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-person-fill"></i> Informasi Pemesan</div>
                <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">CUST-0491</span>
            </div>
            <h4 class="fw-bold mb-1" style="font-size: 1.3rem;">{{ $order->customer_name }}</h4>
            <div class="mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-telephone-fill text-muted"></i>
                <span class="fw-bold">{{ $order->customer_phone }}</span>
            </div>
            <button class="btn-admin w-100 mt-3"><i class="bi bi-chat-square-text-fill"></i> Chat WhatsApp Pelanggan</button>
        </div>

        <div class="admin-card mt-4">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-geo-alt-fill"></i> Alamat & Pengantaran</div>
            </div>
            <div class="mb-3">
                <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">TUJUAN PENGIRIMAN</div>
                <div class="fw-bold" style="font-size: 0.9rem;">{{ $order->shipping_address }}</div>
            </div>
            <div class="d-flex align-items-center gap-3 p-3 border rounded" style="background: var(--cream-light);">
                <div style="width: 40px; height: 40px; background: var(--brown-dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">RK</div>
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size: 0.9rem;">Rian Kusuma</div>
                    <small class="text-muted font-mono" style="font-size: 0.7rem;">Kurir Dapur #02</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="admin-card">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-cup-hot-fill"></i> Rincian Menu</div>
            </div>
            @foreach($order->orderItems as $item)
            <div class="d-flex gap-3 mb-3 pb-3 border-bottom">
                <div class="flex-grow-1">
                    <div class="fw-bold" style="font-size: 1rem;">{{ $item->product->name }}</div>
                    <div class="text-muted" style="font-size: 0.8rem;">Kuantitas: <strong>{{ $item->quantity }} Porsi</strong></div>
                </div>
                <div class="text-end">
                    <div class="fw-bold" style="font-size: 1.1rem; color: var(--brown-dark);">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Rincian Pembayaran -->
        <div class="admin-card mt-4">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-receipt"></i> Rincian Pembayaran</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700;">TOTAL YANG HARUS DIBAYAR</div>
                    <div class="fw-bold" style="font-size: 2rem; color: var(--brown-dark);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <!-- ✅ VERIFIKASI PEMBAYARAN - SELALU MUNCUL UNTUK NON-COD -->
        @if($order->payment_method !== 'COD')
        <div class="admin-card mt-4" style="border-left: 4px solid {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : ($order->payment?->payment_status === 'REJECTED' ? '#c53030' : 'var(--orange-fav)') }};">
            <div class="card-header-custom">
                <div class="card-title-custom">
                    <i class="bi bi-image"></i> STATUS PEMBAYARAN ({{ $order->payment_method }})
                </div>
                <span class="badge-admin" style="background: {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-bg)' : ($order->payment?->payment_status === 'REJECTED' ? '#fde8e8' : 'var(--warning-bg)') }}; color: {{ $order->payment?->payment_status === 'VERIFIED' ? 'var(--green-stock)' : ($order->payment?->payment_status === 'REJECTED' ? '#c53030' : '#b45309') }};">
                    {{ $order->payment?->payment_status === 'VERIFIED' ? '✅ TERVERIFIKASI' : ($order->payment?->payment_status === 'REJECTED' ? '❌ DITOLAK' : ' MENUNGGU PEMBAYARAN') }}
                </span>
            </div>

            @if($order->payment && $order->payment->receipt_image_path)
                <!-- Jika ADA bukti pembayaran -->
                <div class="text-center mb-3 p-3" style="background: var(--cream-light); border-radius: 8px;">
                    <img src="{{ asset('storage/' . $order->payment->receipt_image_path) }}" alt="Bukti Transfer" class="img-fluid rounded shadow-sm" style="max-height: 400px; max-width: 100%;">
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $order->payment->receipt_image_path) }}" target="_blank" class="btn-admin-outline btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> Buka Gambar Ukuran Penuh
                        </a>
                    </div>
                    <div class="mt-2 font-mono" style="font-size: 0.75rem; color: var(--text-muted);">
                        Jumlah Dibayar: Rp {{ number_format($order->payment->amount_paid ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                @if($order->payment->payment_status === 'PENDING_REVIEW')
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 700;">CATATAN PENOLAKAN (Opsional)</label>
                        <input type="text" id="rejectionNote" class="form-control" placeholder="Contoh: Nominal tidak sesuai">
                    </div>
                    <div class="col-6">
                        <form method="POST" action="{{ route('admin.payments.verify', $order->payment) }}" class="d-inline w-100">
                            @csrf
                            <input type="hidden" name="status" value="REJECTED">
                            <input type="hidden" name="rejection_note" id="rejectionNoteInput">
                            <button type="submit" class="btn w-100" onclick="document.getElementById('rejectionNoteInput').value = document.getElementById('rejectionNote').value;" style="background: #c53030; color: white; border: none;">
                                <i class="bi bi-x-circle"></i> TOLAK PEMBAYARAN
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <form method="POST" action="{{ route('admin.payments.verify', $order->payment) }}" class="d-inline w-100">
                            @csrf
                            <input type="hidden" name="status" value="VERIFIED">
                            <button type="submit" class="btn w-100" style="background: var(--green-stock); color: white; border: none;">
                                <i class="bi bi-check-circle"></i> TERIMA & PROSES ORDER
                            </button>
                        </form>
                    </div>
                </div>
                @endif

            @else
                <!-- Jika BELUM ADA bukti pembayaran -->
                <div class="text-center py-4" style="background: var(--cream-light); border-radius: 8px; border: 2px dashed var(--border-color);">
                    <i class="bi bi-hourglass-split text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">Pelanggan Belum Upload Bukti Pembayaran</h5>
                    <p class="text-muted" style="font-size: 0.85rem;">
                        Order ini menggunakan metode <strong>{{ $order->payment_method }}</strong>, tetapi pelanggan belum mengunggah bukti transfer.
                    </p>
                    <div class="mt-3">
                        <span class="badge-admin" style="background: var(--warning-bg); color: #b45309; font-size: 0.8rem;">
                            ⏳ Menunggu pelanggan upload bukti via halaman Checkout
                        </span>
                    </div>
                    <div class="mt-3 p-3 border rounded" style="background: white; font-size: 0.8rem; color: var(--text-muted);">
                        <strong>💡 Tips Admin:</strong> Jika pelanggan sudah transfer tapi belum upload bukti, Anda bisa:
                        <ul class="text-start mt-2 mb-0">
                            <li>Hubungi pelanggan via WhatsApp untuk konfirmasi</li>
                            <li>Tunggu pelanggan upload bukti di halaman Status Order mereka</li>
                            <li>Atau ubah status order manual menggunakan dropdown di atas</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        @else
        <!-- Jika COD -->
        <div class="admin-card mt-4" style="border-left: 4px solid var(--brown-dark);">
            <div class="card-header-custom">
                <div class="card-title-custom">
                    <i class="bi bi-cash-stack"></i> METODE PEMBAYARAN: COD (CASH ON DELIVERY)
                </div>
                <span class="badge-admin badge-completed">💵 TAGIH DI TEMPAT</span>
            </div>
            <div class="p-3" style="background: var(--cream-light); border-radius: 8px;">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-info-circle-fill" style="font-size: 1.5rem; color: var(--brown-dark);"></i>
                    <div>
                        <div class="fw-bold">Pembayaran akan diterima kurir saat pengantaran</div>
                        <small class="text-muted">Pastikan kurir membawa kembalian yang cukup. Total tagihan: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="text-center mt-4 font-mono" style="font-size: 0.7rem; color: var(--text-muted);">
    BLUEPRINT: ADMIN_SPEC_v1.0 // WIREFRAME SCHEMATIC // DAPUR TH SANT
</div>

@endif
@endsection
