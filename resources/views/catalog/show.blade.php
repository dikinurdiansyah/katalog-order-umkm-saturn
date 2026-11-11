@extends('layouts.app')

@section('title', $product->name . ' - Detail Produk')

@section('navbar')
<div class="top-navbar">
    <a href="{{ route('catalog.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="page-title">DETAIL PRODUK</span>
    <div class="user-icon">
        <a href="{{ route('cart.index') }}" class="text-decoration-none text-dark position-relative">
            <i class="bi bi-bag-fill" style="font-size: 1.2rem;"></i>
            @php
                $cartCount = 0;
                $cart = session()->get('cart', []);
                foreach($cart as $item) { $cartCount += intval($item['quantity'] ?? 0); }
            @endphp
            @if($cartCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25em 0.4em;">
                {{ $cartCount }}
            </span>
            @endif
        </a>
    </div>
</div>
@endsection

@section('content')
@php
    // ✅ AMAN: Pastikan stok selalu berupa angka, jika null dianggap 0
    $availableStock = intval($product->stock ?? 0);

    // ✅ Ambil semua gambar untuk gallery (menggunakan helper dari Model)
    $allImages = $product->getAllImages();

    // ✅ LOGIC BARU: Cek apakah produk ini adalah Minuman
    $isMinuman = false;
    if (isset($product->category) && $product->category) {
        $catName = strtolower($product->category->name);
        if (str_contains($catName, 'minuman') || str_contains($catName, 'drink') || str_contains($catName, 'es') || str_contains($catName, 'juice')) {
            $isMinuman = true;
        }
    }
@endphp

<!-- ✅ CONTAINER RESPONSIF -->
<div class="container-fluid px-3 px-lg-5 pt-4 pb-5">

    <!-- Doc ID Bar (Hidden on mobile) -->
    <div class="d-none d-md-flex justify-content-between align-items-center mb-3 pb-2" style="border-bottom: 1px solid var(--border-color);">
        <small class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">DOC_ID: P2_PRD_DETAIL</small>
        <small class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">SPEC V1.0.4 [LODI]</small>
    </div>

    <div class="row g-4">
        <!-- KOLOM KIRI: Gambar Produk (Gallery) -->
        <div class="col-lg-6">
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <span class="badge-stock" style="background: var(--brown-dark); color: white; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;">BEST SELLER</span>
                <span class="badge-stock" style="background: white; color: var(--brown-dark); border: 1px solid var(--border-color); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;">100% HALAL</span>
                @if($availableStock > 10)
                    <span class="badge-stock" style="background: var(--green-bg); color: var(--green-stock); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;">READY STOCK</span>
                @elseif($availableStock > 0)
                    <span class="badge-stock" style="background: var(--warning-bg); color: var(--orange-fav); padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;">SISA {{ $availableStock }}</span>
                @else
                    <span class="badge-stock" style="background: #fde8e8; color: #c53030; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;">HABIS</span>
                @endif
            </div>

            <!-- Main Image -->
            <div class="card-warm" style="aspect-ratio: 4/3; background: var(--cream); display: flex; align-items: center; justify-content: center; position: relative; border-radius: 16px; overflow: hidden;">
                <img id="mainProductImage" src="{{ asset($product->image_url ?? 'products/default.jpg') }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            </div>

            <!-- Thumbnail Strip (Muncul jika ada lebih dari 1 foto) -->
            @if(count($allImages) > 1)
            <div class="d-flex gap-2 mt-3 flex-nowrap overflow-auto pb-2" style="scrollbar-width: thin;">
                @foreach($allImages as $index => $image)
                    <div class="flex-shrink-0" style="width: 80px; height: 80px; border-radius: 12px; overflow: hidden; cursor: pointer; border: 3px solid {{ $index === 0 ? 'var(--brown-dark)' : 'transparent' }};"
                         onclick="changeMainImage('{{ asset($image) }}', this)">
                        <img src="{{ asset($image) }}" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- KOLOM KANAN: Info Produk -->
        <div class="col-lg-6">
            <h2 class="fw-bold text-brown mb-3" style="font-size: clamp(1.3rem, 2.5vw, 2rem); line-height: 1.3;">
                {{ $product->name }}
            </h2>

            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                <span class="text-brown">
                    <i class="bi bi-star-fill" style="color: #f59e0b;"></i>
                    <strong>4.9</strong>
                </span>
                <span class="text-muted" style="font-size: 0.9rem;">(128 ulasan terverifikasi)</span>
                <span class="text-muted">•</span>
                <span class="text-muted" style="font-size: 0.9rem;">Terjual 1.4k+</span>
            </div>

            <div class="card-warm p-3 p-md-4 d-flex align-items-baseline gap-2 flex-wrap mb-4" style="border-radius: 12px;">
                <span class="fw-bold text-brown" style="font-size: clamp(1.8rem, 4vw, 2.5rem);">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                <span class="text-muted" style="font-size: 0.95rem;">/ porsi bambu</span>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem; letter-spacing: 1px;">DESKRIPSI MENU</h5>
                </div>
                <div class="card-warm p-3 p-md-4" style="border-radius: 12px;">
                    <p class="mb-3" style="font-size: 0.95rem; line-height: 1.7; color: var(--text-dark);">
                        {{ $product->description ?? 'Dimsum kukus lembut olahan 100% daging pilihan dengan bumbu rempah autentik oriental.' }}
                    </p>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-square-fill text-brown"></i>
                            <span style="font-size: 0.9rem;">Porsi isi 4 butir ukuran medium-large</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-check-square-fill text-brown"></i>
                            <span style="font-size: 0.9rem;">Bebas bahan pengawet & pewarna sintetis</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ LOGIC BARU: HANYA TAMPILKAN PILIHAN SAUS JIKA BUKAN MINUMAN --}}
            @if(!$isMinuman)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem; letter-spacing: 1px;">PILIHAN SAUS PENDAMPING</h5>
                        <small class="text-muted" style="font-size: 0.85rem;">Pilih salah satu (Wajib)</small>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-12 col-md-4">
                        <label class="card-warm p-3 d-flex align-items-center gap-2 mb-0 h-100" style="cursor: pointer; border: 2px solid var(--brown-dark); border-radius: 12px;">
                            <input type="radio" name="sauce" value="chili_1" checked class="form-check-input" style="width: 18px; height: 18px; accent-color: var(--brown-dark);">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-brown" style="font-size: 0.85rem;">Chili Oil L1</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Sedang</small>
                            </div>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="card-warm p-3 d-flex align-items-center gap-2 mb-0 h-100" style="cursor: pointer; border: 1px solid var(--border-color); border-radius: 12px;">
                            <input type="radio" name="sauce" value="chili_2" class="form-check-input" style="width: 18px; height: 18px; accent-color: var(--brown-dark);">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-brown" style="font-size: 0.85rem;">Chili Oil L2</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Pedas</small>
                            </div>
                        </label>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="card-warm p-3 d-flex align-items-center gap-2 mb-0 h-100" style="cursor: pointer; border: 1px solid var(--border-color); border-radius: 12px;">
                            <input type="radio" name="sauce" value="tomat" class="form-check-input" style="width: 18px; height: 18px; accent-color: var(--brown-dark);">
                            <div class="flex-grow-1">
                                <div class="fw-bold text-brown" style="font-size: 0.85rem;">Saus Tomat</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Asam Manis</small>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            <div class="mb-5">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem; letter-spacing: 1px;">CATATAN PESANAN DAPUR</h5>
                    <small class="font-mono text-muted" style="font-size: 0.75rem;">OPSIONAL</small>
                </div>
                <textarea name="notes" class="form-control-warm w-100" rows="3" maxlength="120"
                          placeholder="Contoh: Saus dipisah dalam wadah plastik, kukus sedikit lebih garing..."
                          oninput="document.getElementById('charCount').textContent = this.value.length"
                          style="border-radius: 12px; border: 1px solid var(--border-color); padding: 12px;"></textarea>
                <div class="text-end mt-1">
                    <small class="font-mono text-muted" style="font-size: 0.75rem;"><span id="charCount">0</span> / 120 Karakter</small>
                </div>
            </div>

            <!-- ✅ DESKTOP ACTION BAR -->
            <div class="d-none d-lg-block">
                @if($availableStock > 0)
                <div class="card-warm p-3" style="border-radius: 12px;">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="qty-control">
                            <button type="button" onclick="changeQty(-1, {{ $availableStock }})" style="width: 44px; height: 44px; border: none; background: transparent; color: var(--brown-dark); font-size: 1.2rem; font-weight: 700; cursor: pointer;"><i class="bi bi-dash"></i></button>
                            <input type="number" id="qty" value="1" min="1" max="{{ $availableStock }}" readonly style="width: 50px; text-align: center; border: none; font-weight: 600; font-size: 1rem; background: transparent;">
                            <button type="button" onclick="changeQty(1, {{ $availableStock }})" style="width: 44px; height: 44px; border: none; background: transparent; color: var(--brown-dark); font-size: 1.2rem; font-weight: 700; cursor: pointer;"><i class="bi bi-plus"></i></button>
                        </div>
                        <form method="POST" action="{{ route('cart.add') }}" style="flex-grow: 1; display: flex;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="qty-input" value="1">
                            <input type="hidden" name="notes" id="notes-input-desktop" value="">
                            <button type="submit" class="w-100" style="background: var(--brown-dark); color: white; border: none; border-radius: 12px; padding: 14px 24px; font-weight: 600; font-size: 1rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                                <span><i class="bi bi-bag-plus me-2"></i>Tambah ke Keranjang</span>
                                <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <button class="btn w-100 py-3" disabled style="background: #d1d5db; color: #6b7280; border-radius: 12px; font-size: 1.1rem; font-weight: 600;">
                    <i class="bi bi-x-circle-fill me-2"></i> Stok Habis (Tidak dapat dipesan)
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- ✅ MOBILE ONLY: Bottom Action Bar Fixed -->
@if($availableStock > 0)
<div class="d-lg-none position-fixed bottom-0 start-0 end-0" style="z-index: 999; background: var(--cream-light); border-top: 1px solid var(--border-color); padding: 12px 16px; padding-bottom: calc(12px + env(safe-area-inset-bottom));">
    <div class="container-fluid px-0">
        <div class="d-flex gap-2 align-items-center">
            <div class="qty-control" style="flex: 0 0 auto;">
                <button type="button" onclick="changeQty(-1, {{ $availableStock }})" style="width: 40px; height: 40px; border: none; background: transparent; color: var(--brown-dark); font-size: 1.2rem; font-weight: 700; cursor: pointer;"><i class="bi bi-dash"></i></button>
                <input type="number" id="qty-mobile" value="1" min="1" max="{{ $availableStock }}" readonly style="width: 40px; text-align: center; border: none; font-weight: 600; font-size: 1rem; background: transparent;">
                <button type="button" onclick="changeQty(1, {{ $availableStock }})" style="width: 40px; height: 40px; border: none; background: transparent; color: var(--brown-dark); font-size: 1.2rem; font-weight: 700; cursor: pointer;"><i class="bi bi-plus"></i></button>
            </div>
            <form method="POST" action="{{ route('cart.add') }}" style="flex-grow: 1; display: flex;">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" id="qty-input-mobile" value="1">
                <input type="hidden" name="notes" id="notes-input-mobile" value="">
                <button type="submit" style="flex-grow: 1; background: var(--brown-dark); color: white; border: none; border-radius: 12px; padding: 12px 20px; font-weight: 600; font-size: 0.95rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                    <span>Tambah</span>
                    <span style="font-family: 'JetBrains Mono', monospace; font-weight: 700;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
@else
{{-- TOMBOL FALLBACK JIKA STOK = 0 AGAR TIDAK BINGUNG --}}
<div class="d-lg-none position-fixed bottom-0 start-0 end-0 p-3" style="z-index: 999; background: var(--cream-light); border-top: 1px solid var(--border-color);">
    <button class="btn w-100 py-3" disabled style="background: #d1d5db; color: #6b7280; border-radius: 12px; font-size: 1.1rem; font-weight: 600;">
        <i class="bi bi-x-circle-fill me-2"></i> Stok Habis (Tidak dapat dipesan)
    </button>
</div>
@endif

@push('scripts')
<script>
    // Ganti foto utama saat thumbnail diklik
    function changeMainImage(src, thumbnailElement) {
        document.getElementById('mainProductImage').src = src;

        // Reset border semua thumbnail
        const thumbnails = thumbnailElement.parentElement.children;
        for (let i = 0; i < thumbnails.length; i++) {
            thumbnails[i].style.border = '3px solid transparent';
        }

        // Set border active ke thumbnail yang diklik
        thumbnailElement.style.border = '3px solid var(--brown-dark)';
    }

    // Sinkronisasi Quantity Desktop & Mobile
    function changeQty(delta, max) {
        const inputs = [
            document.getElementById('qty'),
            document.getElementById('qty-mobile')
        ];
        const hiddenInputs = [
            document.getElementById('qty-input'),
            document.getElementById('qty-input-mobile')
        ];

        // Ambil nilai dari input desktop sebagai acuan utama
        let val = parseInt(document.getElementById('qty').value) + delta;

        if (val < 1) val = 1;
        if (val > max) val = max;

        // Update semua input (desktop & mobile) agar sinkron
        inputs.forEach(input => { if(input) input.value = val; });
        hiddenInputs.forEach(input => { if(input) input.value = val; });
    }

    // Sinkronisasi Catatan Pesanan (Notes) antara Desktop dan Mobile
    const textareaNotes = document.querySelector('textarea[name="notes"]');
    const notesInputDesktop = document.getElementById('notes-input-desktop');
    const notesInputMobile = document.getElementById('notes-input-mobile');

    if (textareaNotes) {
        textareaNotes.addEventListener('input', function() {
            if (notesInputDesktop) notesInputDesktop.value = this.value;
            if (notesInputMobile) notesInputMobile.value = this.value;
        });
    }
</script>
@endpush
@endsection
