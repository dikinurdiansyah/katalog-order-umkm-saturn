@extends('layouts.app')

@section('title', 'Keranjang - Dapur Th Sant')

@section('navbar')
<div class="top-navbar">
    <a href="{{ route('catalog.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="page-title">KERANJANG BELANJA</span>
    <div class="user-icon">
        <a href="{{ route('catalog.index') }}" class="text-decoration-none text-dark">
            <i class="bi bi-grid-fill" style="font-size: 1.2rem;"></i>
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    // ✅ PENGAMAN ANTI-ERROR: Pastikan variabel selalu terdefinisi
    $cartItems = $cartItems ?? [];
    $subtotal = 0;
    $totalQty = 0;

    foreach($cartItems as $item) {
        $subtotal += $item['subtotal'] ?? 0;
        $totalQty += $item['quantity'] ?? 0;
    }

    $freeShippingTarget = 80000;
    $remaining = max(0, $freeShippingTarget - $subtotal);
    $progress = min(100, ($subtotal / $freeShippingTarget) * 100);
    $packaging = 2000;
    $totalPayment = $subtotal + $packaging;
@endphp

<div class="container py-4 px-3 px-lg-4">

    @if(count($cartItems) > 0)
        <div class="row g-4">

            <!-- ========================================== -->
            <!-- KOLOM KIRI: Daftar Item & Voucher -->
            <!-- ========================================== -->
            <div class="col-lg-8">

                <!-- Free Shipping Progress -->
                <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-truck text-brown" style="font-size: 1.2rem;"></i>
                            <strong class="text-brown" style="font-size: 0.9rem;">
                                @if($remaining > 0)
                                    GRATIS ONGKIR: TAMBAH RP {{ number_format($remaining, 0, ',', '.') }} LAGI
                                @else
                                    🎉 SELAMAT! GRATIS ONGKIR TERCAPAI
                                @endif
                            </strong>
                        </div>
                        <span class="font-mono text-muted" style="font-size: 0.85rem;">{{ round($progress) }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; background: var(--cream-dark); border-radius: 5px; overflow: hidden;">
                        <div class="progress-bar" style="width: {{ $progress }}%; background: var(--brown-dark); border-radius: 5px; transition: width 0.5s ease;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">Rp {{ number_format($subtotal, 0, ',', '.') }}</small>
                        <small class="text-muted font-mono" style="font-size: 0.75rem;">Target: Rp {{ number_format($freeShippingTarget, 0, ',', '.') }}</small>
                    </div>
                </div>

                <!-- Select All & Clear Cart -->
                <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                    <div class="d-flex align-items-center gap-2">
                        <input class="form-check-input" type="checkbox" checked style="width: 20px; height: 20px; accent-color: var(--brown-dark);">
                        <label class="fw-bold text-brown" style="font-size: 0.95rem;">
                            Pilih Semua ({{ count($cartItems) }} Item)
                        </label>
                    </div>
                    <form method="POST" action="{{ route('cart.clear') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-muted p-0" style="font-size: 0.85rem; text-decoration: none; font-weight: 600;"
                                onclick="return confirm('Yakin ingin mengosongkan keranjang?')">
                            <i class="bi bi-trash me-1"></i> HAPUS SEMUA
                        </button>
                    </form>
                </div>

                <!-- Cart Items Loop -->
                @foreach($cartItems as $item)
                    <div class="card-warm p-3 p-md-4 mb-3" style="border-radius: 16px; transition: transform 0.2s;">
                        <div class="d-flex gap-3">
                            <!-- Checkbox -->
                            <div class="d-flex align-items-start pt-1">
                                <input class="form-check-input" type="checkbox" checked style="width: 20px; height: 20px; accent-color: var(--brown-dark);">
                            </div>

                            <!-- ✅ IMAGE -->
                            <div style="width: 80px; height: 80px; flex-shrink: 0;">
                                @php
                                    $imageUrl = $item['image'] ?? ($item['product']->image_url ?? null);
                                @endphp
                                @if($imageUrl)
                                    <img src="{{ asset($imageUrl) }}" class="w-100 h-100" style="object-fit: cover; border-radius: 12px;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: var(--cream-dark); border-radius: 12px; border: 1px dashed var(--border-color);">
                                        <i class="bi bi-image text-muted" style="font-size: 1.5rem;"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="fw-bold mb-1 text-brown" style="font-size: 1rem; line-height: 1.3;">
                                        {{ $item['product']->name ?? 'Produk' }}
                                    </h6>
                                    <form method="POST" action="{{ route('cart.remove', $item['product']->id ?? 0) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link p-0 text-muted" title="Hapus Item">
                                            <i class="bi bi-trash" style="font-size: 1.1rem;"></i>
                                        </button>
                                    </form>
                                </div>

                                {{-- ✅ DIPERBAIKI: Tampilkan catatan HANYA jika ada isinya --}}
                                @if(!empty(trim($item['notes'] ?? '')))
                                    <span class="badge-stock mb-2 d-inline-block" style="background: var(--cream-dark); color: var(--brown-dark); font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;">
                                        <i class="bi bi-chat-left-text me-1"></i> {{ trim($item['notes']) }}
                                    </span>
                                @endif

                                <div class="text-muted" style="font-size: 0.85rem;">
                                    @ Rp {{ number_format($item['product']->price ?? 0, 0, ',', '.') }} / porsi
                                </div>
                            </div>
                        </div>

                        <!-- Subtotal & Qty Control -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                            <div>
                                <div class="section-label mb-0" style="font-size: 0.7rem; color: var(--text-muted);">SUBTOTAL</div>
                                <div class="fw-bold text-brown" style="font-size: 1.15rem;">
                                    Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('cart.update', $item['product']->id ?? 0) }}">
                                @csrf
                                @method('PUT')
                                <div class="qty-control d-flex align-items-center border rounded-3 overflow-hidden" style="background: white; border-color: var(--border-color) !important;">
                                    <button type="button" onclick="updateQty(this, -1, 1)" class="btn btn-sm px-3 py-2" style="color: var(--brown-dark); font-weight: 700;">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" name="quantity" value="{{ $item['quantity'] ?? 1 }}" min="1" max="{{ $item['product']->stock ?? 99 }}"
                                           onchange="this.form.submit()" readonly
                                           style="width: 45px; text-align: center; border: none; font-weight: 600; font-size: 0.95rem; background: transparent;">
                                    <button type="button" onclick="updateQty(this, 1, {{ $item['product']->stock ?? 99 }})" class="btn btn-sm px-3 py-2" style="color: var(--brown-dark); font-weight: 700;">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach

                <!-- Voucher Section -->
                <div class="card-warm p-3 p-md-4 mb-3 d-flex align-items-center gap-3" style="border-radius: 16px; border: 1px dashed var(--brown-dark);">
                    <div style="width: 48px; height: 48px; background: var(--cream-dark); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="bi bi-tag-fill text-brown" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-brown" style="font-size: 0.95rem;">Voucher Diskon UMKM</div>
                        <small class="text-muted" style="font-size: 0.8rem;">Hemat hingga Rp 10.000 untuk pesanan ini</small>
                    </div>
                    <a href="#" class="btn-brown-outline px-3 py-2 d-flex align-items-center gap-2" style="font-size: 0.85rem; border-radius: 10px; white-space: nowrap;">
                        PAKAI <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

            </div> <!-- End Col Left -->

            <!-- ========================================== -->
            <!-- KOLOM KANAN: Ringkasan & Checkout -->
            <!-- ========================================== -->
            <div class="col-lg-4">
                <div class="sticky-summary" style="position: sticky; top: 20px;">
                    <div class="card-warm p-4" style="border-radius: 16px; border: 1px solid var(--border-color);">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0 text-brown" style="font-size: 1.1rem;">Ringkasan Pesanan</h5>
                            <small class="font-mono text-muted" style="font-size: 0.75rem;">DRAFT #{{ strtoupper(substr(md5(time()), 0, 6)) }}</small>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal Produk ({{ $totalQty }} Porsi)</span>
                            <span class="fw-bold text-brown">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Biaya Kemasan Higienis</span>
                            <span class="fw-bold text-brown">Rp {{ number_format($packaging, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Diskon Promo</span>
                            <span class="fw-bold text-success">-Rp 0</span>
                        </div>

                        <hr style="border-top: 2px dashed var(--border-color);">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="fw-bold text-brown" style="font-size: 0.9rem; letter-spacing: 1px;">TOTAL PEMBAYARAN</div>
                                <small class="text-muted" style="font-size: 0.75rem;">Belum termasuk ongkir</small>
                            </div>
                            <div class="fw-bold text-brown" style="font-size: 1.6rem;">
                                Rp {{ number_format($totalPayment, 0, ',', '.') }}
                            </div>
                        </div>

                        <a href="{{ route('orders.create') }}" class="btn w-100 d-flex justify-content-between align-items-center shadow-sm"
                           style="background: var(--brown-dark); color: white; border: none; border-radius: 14px; padding: 16px 20px; font-weight: 700; font-size: 1.05rem; text-decoration: none; transition: transform 0.2s;"
                           onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <span>Lanjut ke Checkout</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="{{ route('catalog.index') }}" class="btn w-100 d-flex justify-content-center align-items-center gap-2 mt-3"
                           style="background: transparent; color: var(--brown-dark); border: 1px solid var(--border-color); border-radius: 14px; padding: 12px; font-weight: 600; font-size: 0.9rem; text-decoration: none;">
                            <i class="bi bi-arrow-left"></i>
                            <span>Lanjut Belanja</span>
                        </a>
                    </div>
                </div>
            </div> <!-- End Col Right -->

        </div> <!-- End Row -->

    @else
        <!-- Empty Cart State -->
        <div class="text-center py-5 my-5">
            <div style="width: 120px; height: 120px; background: var(--cream-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i class="bi bi-bag-x" style="font-size: 3.5rem; color: var(--text-muted);"></i>
            </div>
            <h3 class="text-brown fw-bold mb-2">Keranjang Belanja Kosong</h3>
            <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">Yuk, mulai belanja dimsum kukus dan goreng favoritmu yang fresh dari dapur kami!</p>
            <a href="{{ route('catalog.index') }}" class="btn-brown px-4 py-3" style="border-radius: 12px; font-weight: 600; font-size: 1rem;">
                <i class="bi bi-grid me-2"></i> Lihat Katalog Menu
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function updateQty(btn, delta, max) {
        const form = btn.closest('form');
        const input = form.querySelector('input[name="quantity"]');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
        form.submit(); // Auto submit saat qty berubah
    }
</script>
@endpush
@endsection
