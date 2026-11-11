@extends('layouts.app')

@section('title', 'Detail Pengiriman - Checkout')

@section('navbar')
<div class="top-navbar">
    <a href="{{ route('cart.index') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
    </a>
    <span class="page-title">DETAIL PENGIRIMAN</span>
    <div class="user-icon">
        <i class="bi bi-shield-check text-success"></i>
    </div>
</div>
@endsection

@section('content')
@php
    // ✅ AMBIL KOORDINAT TOKO DARI DATABASE AGAR PETA AKURAT
    $settings = \App\Models\Setting::firstWhere('key', 'outlet_info');
    $data = $settings ? (is_array($settings->value) ? $settings->value : json_decode($settings->value, true)) : [];
    $tokoLat = $data['latitude'] ?? -7.1229831;   // Default Alamendah (sesuaikan DB)
    $tokoLng = $data['longitude'] ?? 107.4331362; // Default Alamendah (sesuaikan DB)
@endphp

<div class="px-3 py-2 d-flex justify-content-between align-items-center" style="background: var(--cream); border-bottom: 1px solid var(--border-color);">
    <small class="font-mono" style="font-size: 0.65rem; color: var(--text-muted);">WF-SPEC: P4-CHECKOUT-PROMO</small>
    <small class="font-mono" style="font-size: 0.65rem; color: var(--text-muted);">REV 1.0.0 // STABLE</small>
</div>

<form method="POST" action="{{ route('orders.store') }}" id="checkoutForm">
    @csrf

    <div class="container py-4 px-3 px-lg-4">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- 1. Informasi Pemesan -->
                <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem;">1. INFORMASI PEMESAN</h5>
                        <span class="font-mono badge-status" style="font-size: 0.65rem;">REQ_STAGE_1</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="section-label">NAMA LENGKAP</label>
                            <input type="text" name="customer_name" class="form-control-warm w-100 @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required placeholder="Nama penerima pesanan">
                            @error('customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="section-label">NOMOR WHATSAPP</label>
                            <input type="text" name="customer_phone" class="form-control-warm w-100 @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" required placeholder="08xxxxxxxxxx">
                            @error('customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="section-label">METODE PEMENUHAN</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" name="fulfillment_method" id="radio-delivery" value="DELIVERY" checked class="d-none" onchange="toggleFulfillment()">
                                <label for="radio-delivery" class="card-warm p-3 text-center w-100 mb-0" id="label-delivery" style="cursor: pointer; border: 2px solid var(--brown-dark); background: var(--brown-dark); color: white; border-radius: 12px;">
                                    <i class="bi bi-check-lg d-block mb-1" style="font-size: 1.2rem;"></i> Delivery
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" name="fulfillment_method" id="radio-pickup" value="PICKUP" class="d-none" onchange="toggleFulfillment()">
                                <label for="radio-pickup" class="card-warm p-3 text-center w-100 mb-0" id="label-pickup" style="cursor: pointer; border: 1px solid var(--border-color); border-radius: 12px;">
                                    <i class="bi bi-square d-block mb-1" style="font-size: 1.2rem; color: var(--text-muted);"></i> Ambil di Tempat
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ KOLOM 1: KHUSUS DELIVERY (DINAMIS KECAMATAN & DESA + PETA) -->
                    <div class="mt-3" id="delivery-section">
                        <label class="section-label mb-2">KECAMATAN</label>
                        <div class="card-warm p-3 mb-2">
                            <select name="kecamatan" id="kecamatan-select" class="form-control-warm w-100 @error('kecamatan') is-invalid @enderror" required onchange="updateDesaOptions()">
                                <option value="">-- Pilih Kecamatan --</option>
                                <option value="Ciwidey">Kec. Ciwidey</option>
                                <option value="Rancabali">Kec. Rancabali</option>
                                <option value="Pasir Jambu">Kec. Pasir Jambu</option>
                            </select>
                            @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <label class="section-label mb-2">DESA / KELURAHAN</label>
                        <div class="card-warm p-3 mb-2">
                            <select name="desa" id="desa-select" class="form-control-warm w-100 @error('desa') is-invalid @enderror" required onchange="updateShippingCost()">
                                <option value="">-- Pilih Kecamatan Terlebih Dahulu --</option>
                            </select>
                            @error('desa') <div class="invalid-feedback">{{ $message }}</div> @enderror

                            <!-- Info Ongkir Dinamis -->
                            <div id="ongkir-info" class="mt-2 p-2" style="background: var(--cream-dark); border-radius: 8px; display: none;">
                                <small class="text-muted" style="font-size: 0.75rem;">
                                    <i class="bi bi-truck"></i> <strong id="ongkir-text">Customer pilih: -</strong>
                                </small>
                            </div>
                        </div>

                        <label class="section-label mb-2">ALAMAT LENGKAP</label>
                        <div class="card-warm p-3">
                            <div class="d-flex gap-2">
                                <i class="bi bi-geo-alt-fill text-brown mt-1"></i>
                                <div class="flex-grow-1">
                                    <textarea name="delivery_address" id="delivery-input" class="form-control-warm w-100 @error('delivery_address') is-invalid @enderror"
                                              rows="2" required placeholder="Contoh: Jl. Raya Ciwidey No. 123, dekat Alun-alun">{{ old('delivery_address') }}</textarea>
                                    @error('delivery_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ✅ TAMBAHAN: PETA LOKASI TOKO (LEAFLET) -->
                        <div class="mt-3">
                            <label class="section-label mb-2">LOKASI DAPUR TH SANT (Titik Pengiriman)</label>
                            <div id="mapCheckout" style="height: 250px; border-radius: 12px; border: 1px solid var(--border-color); z-index: 1;"></div>
                            <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Pesanan akan dikirim dari lokasi ini ke alamat Anda.</small>
                        </div>
                    </div>

                    <!-- KOLOM 2: KHUSUS PICKUP -->
                    <div class="mt-3" id="pickup-section" style="display: none;">
                        <label class="section-label mb-2">CATATAN PENGAMBILAN</label>
                        <div class="card-warm p-3">
                            <textarea name="pickup_note" id="pickup-input" class="form-control-warm w-100 @error('pickup_note') is-invalid @enderror" rows="2" placeholder="Contoh: Ambil di kasir, atas nama Budi">{{ old('pickup_note') }}</textarea>
                            @error('pickup_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;"><i class="bi bi-info-circle"></i> Pesanan dapat diambil di Outlet Dapur Th Sant.</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="section-label">CATATAN TAMBAHAN UNTUK DAPUR (OPSIONAL)</label>
                        <textarea name="notes" class="form-control-warm w-100" rows="2" placeholder="Contoh: Saus dipisah, jangan terlalu pedas...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- 2. Ringkasan Menu -->
                <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem;">2. RINGKASAN MENU PESANAN</h5>
                        <span class="badge-status">{{ count($cartItems ?? []) }} ITEM</span>
                    </div>
                    @foreach($cartItems ?? [] as $item)
                        <div class="card-warm p-3 mb-2 d-flex gap-3 align-items-center" style="background: var(--cream-light);">
                            <div style="width: 60px; height: 60px; flex-shrink: 0; background: var(--cream-dark); border-radius: 8px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                @php $img = $item['product']->image_url ?? null; @endphp
                                @if($img) <img src="{{ asset($img) }}" class="w-100 h-100" style="object-fit: cover; border-radius: 8px;">
                                @else <span class="font-mono" style="font-size: 0.7rem; color: var(--text-muted);">1:1</span> @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold text-brown" style="font-size: 0.9rem;">{{ $item['product']->name ?? 'Produk' }}</div>
                                <small class="text-muted">{{ isset($item['product']) ? Str::limit($item['product']->description, 30) : 'Pedas Sedang' }}</small>
                                <div class="text-muted" style="font-size: 0.75rem;">QTY: {{ $item['quantity'] ?? 1 }}X</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-brown">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. Voucher & Promo -->
                <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem;">3. VOUCHER & PROMO</h5>
                        <span class="font-mono badge-status" style="font-size: 0.65rem;">PROMO_DISC</span>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <input type="text" id="promoInput" name="promo_code" class="form-control-warm flex-grow-1 font-mono" placeholder="CONTOH: DISKON10" value="{{ old('promo_code') }}" style="text-transform: uppercase;">
                        <button type="button" id="checkPromoBtn" class="btn-brown px-4">CEK</button>
                    </div>
                    <div id="promoMessage" class="alert d-none" style="border-radius: 8px; font-size: 0.85rem;"></div>
                    @if(isset($promos) && $promos->count() > 0)
                        <div class="text-muted mt-2" style="font-size: 0.8rem;">
                            Promo tersedia:
                            @foreach($promos as $promo)
                                <span class="badge-stock ms-1 promo-chip" data-code="{{ $promo->code }}" style="background: var(--cream-dark); color: var(--brown-dark); cursor: pointer;">{{ $promo->code }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- ✅ 4. Metode Pembayaran (HANYA QRIS) -->
                <div class="card-warm p-3 p-md-4 mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 text-brown" style="font-size: 1rem;">4. METODE PEMBAYARAN</h5>
                        <span class="font-mono badge-status" style="font-size: 0.65rem;">PAY_SELECT</span>
                    </div>

                    <label class="card-warm p-3 mb-2 d-flex align-items-center gap-3" style="cursor: pointer; border: 2px solid var(--brown-dark); background: var(--cream-dark); border-radius: 12px;">
                        <input type="radio" name="payment_method" value="QRIS" checked class="d-none">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-brown">Pembayaran via QRIS</div>
                            <small class="text-muted" style="font-size: 0.8rem;">Arahkan kamera HP untuk langsung membuka aplikasi e-wallet / mobile banking</small>
                        </div>
                        <i class="bi bi-qr-code-scan text-brown" style="font-size: 1.5rem;"></i>
                    </label>

                    <small class="text-muted mt-2 d-block" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle"></i> Setelah klik "Konfirmasi Pesanan", Anda akan diarahkan ke halaman untuk men-scan QRIS dan mengupload bukti pembayaran.
                    </small>
                </div>
            </div>

            <!-- KOLOM KANAN: RINGKASAN -->
            <div class="col-lg-4">
                <div class="sticky-summary" style="position: sticky; top: 20px;">
                    <div class="card-warm p-3 p-md-4" style="border-radius: 16px; border: 1px solid var(--border-color);">
                        <h6 class="fw-bold text-brown mb-3" style="font-size: 0.9rem; letter-spacing: 1px;">RINCIAN PERHITUNGAN BIAYA</h6>
                        @php
                            $subtotalVal = $subtotal ?? 0;
                            $packaging = 2000;
                            $shipping = 10000; // Default, akan diupdate JS
                            $total = $subtotalVal + $packaging + $shipping;
                        @endphp
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal Pesanan</span>
                            <span class="fw-bold text-brown" id="subtotal-display">Rp {{ number_format($subtotalVal, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Biaya Kemasan Higienis</span>
                            <span class="fw-bold text-brown">Rp {{ number_format($packaging, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2" id="shipping-row">
                            <span class="text-muted">Ongkos Kirim</span>
                            <span class="fw-bold text-brown" id="shipping-value">Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 d-none" id="discount-row" style="color: var(--green-stock);">
                            <span class="fw-bold"><i class="bi bi-tag-fill"></i> Diskon Promo <small id="promoNameDisplay" class="text-muted font-mono"></small></span>
                            <span class="fw-bold" id="discount-value">-Rp 0</span>
                        </div>
                        <hr class="divider" style="border-top: 2px dashed var(--border-color);">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <div class="fw-bold text-brown" style="font-size: 0.85rem; letter-spacing: 1px;">TOTAL PEMBAYARAN</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-brown" style="font-size: 1.5rem;" id="total-value">Rp {{ number_format($total, 0, ',', '.') }}</div>
                                <span class="badge-status">TERM: NETT</span>
                            </div>
                        </div>
                        <button type="submit" class="btn-brown w-100 d-flex justify-content-between align-items-center shadow-sm" style="padding: 16px 24px; border-radius: 12px; border: none; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                            <span class="fw-bold">KONFIRMASI PESANAN</span>
                            <span class="d-flex align-items-center gap-2">
                                <span id="button-total">Rp {{ number_format($total, 0, ',', '.') }}</span> <i class="bi bi-arrow-right"></i>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<!-- Pastikan Leaflet CSS & JS sudah ada di layouts.app, jika belum, uncomment baris di bawah ini -->
<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" /> -->
<!-- <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script> -->

<script>
    const baseSubtotal = {{ $subtotal ?? 0 }};
    const packagingCost = 2000;
    let currentDiscount = 0;

    // Data Desa per Kecamatan (Ongkir berdasarkan jarak dari patokan)
    const desaData = {
        'Ciwidey': [
            { name: 'Ciwidey', ongkir: 5000 },
            { name: 'Panyocokan', ongkir: 5000 },
            { name: 'Rawabogo', ongkir: 6000 },
            { name: 'Patengan', ongkir: 7000 },
            { name: 'Panundaan', ongkir: 6000 },
            { name: 'Mekarsari', ongkir: 8000 },
            { name: 'Nengkelan', ongkir: 8000 },
            { name: 'Sukawening', ongkir: 9000 }
        ],
        'Rancabali': [
            { name: 'Rancabali', ongkir: 10000 },
            { name: 'Alam Endah', ongkir: 12000 },
            { name: 'Lebakmuncang', ongkir: 12000 },
            { name: 'Cipelah', ongkir: 18000 }
        ],
        'Pasir Jambu': [
            { name: 'Pasir Jambu', ongkir: 12000 },
            { name: 'Cangkuang', ongkir: 14000 },
            { name: 'Margamukti', ongkir: 15000 }
        ]
    };

    function updateDesaOptions() {
        const kecamatanSelect = document.getElementById('kecamatan-select');
        const desaSelect = document.getElementById('desa-select');
        const selectedKecamatan = kecamatanSelect.value;

        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

        if (selectedKecamatan && desaData[selectedKecamatan]) {
            desaData[selectedKecamatan].forEach(desa => {
                const option = document.createElement('option');
                option.value = desa.name;
                option.textContent = `Desa ${desa.name} (Rp ${desa.ongkir.toLocaleString('id-ID')})`;
                option.setAttribute('data-ongkir', desa.ongkir);
                desaSelect.appendChild(option);
            });
        }
        updateShippingCost();
    }

    function updateShippingCost() {
        const kecamatanSelect = document.getElementById('kecamatan-select');
        const desaSelect = document.getElementById('desa-select');
        const ongkirInfo = document.getElementById('ongkir-info');
        const ongkirText = document.getElementById('ongkir-text');

        const selectedKecamatan = kecamatanSelect.value;
        const selectedDesaOption = desaSelect.options[desaSelect.selectedIndex];
        const ongkir = parseInt(selectedDesaOption.getAttribute('data-ongkir')) || 0;

        if (selectedKecamatan && selectedDesaOption.value) {
            ongkirInfo.style.display = 'block';
            ongkirText.innerHTML = `Kec. ${selectedKecamatan} - Desa ${selectedDesaOption.value} = Rp ${ongkir.toLocaleString('id-ID')}`;
        } else {
            ongkirInfo.style.display = 'none';
        }
        updateTotalDisplay();
    }

    function updateTotalDisplay() {
        const isPickup = document.getElementById('radio-pickup').checked;
        const desaSelect = document.getElementById('desa-select');
        let currentShipping = 0;

        if (!isPickup && desaSelect.selectedIndex > 0) {
            currentShipping = parseInt(desaSelect.options[desaSelect.selectedIndex].getAttribute('data-ongkir')) || 0;
        }

        const finalTotal = baseSubtotal + packagingCost + currentShipping - currentDiscount;
        const formattedTotal = 'Rp ' + finalTotal.toLocaleString('id-ID');

        document.getElementById('total-value').innerText = formattedTotal;
        document.getElementById('button-total').innerText = formattedTotal;

        const shippingRow = document.getElementById('shipping-row');
        if (isPickup) {
            shippingRow.style.display = 'none';
        } else {
            shippingRow.style.display = 'flex';
            document.getElementById('shipping-value').innerText = 'Rp ' + currentShipping.toLocaleString('id-ID');
        }
    }

    function checkPromo() {
        const promoCode = document.getElementById('promoInput').value.trim().toUpperCase();
        const messageDiv = document.getElementById('promoMessage');
        const checkBtn = document.getElementById('checkPromoBtn');

        if (!promoCode) {
            messageDiv.className = 'alert alert-warning';
            messageDiv.textContent = 'Masukkan kode promo terlebih dahulu.';
            messageDiv.classList.remove('d-none');
            return;
        }

        checkBtn.disabled = true;
        checkBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cek...';

        fetch('{{ route("promo.check") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ promo_code: promoCode, subtotal: baseSubtotal })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentDiscount = data.discount;
                messageDiv.className = 'alert alert-success';
                messageDiv.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + data.message;
                messageDiv.classList.remove('d-none');
                document.getElementById('discount-row').classList.remove('d-none');
                document.getElementById('discount-value').innerText = '-Rp ' + currentDiscount.toLocaleString('id-ID');
                document.getElementById('promoNameDisplay').innerText = '(' + data.promo_name + ')';
                updateTotalDisplay();
            } else {
                currentDiscount = 0;
                messageDiv.className = 'alert alert-danger';
                messageDiv.innerHTML = '<i class="bi bi-x-circle-fill"></i> ' + data.message;
                messageDiv.classList.remove('d-none');
                document.getElementById('discount-row').classList.add('d-none');
                updateTotalDisplay();
            }
        })
        .catch(() => {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Terjadi kesalahan jaringan.';
            messageDiv.classList.remove('d-none');
        })
        .finally(() => {
            checkBtn.disabled = false;
            checkBtn.innerText = 'CEK';
        });
    }

    document.getElementById('checkPromoBtn').addEventListener('click', checkPromo);
    document.getElementById('promoInput').addEventListener('keypress', function(e) { if (e.key === 'Enter') { e.preventDefault(); checkPromo(); } });
    document.querySelectorAll('.promo-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.getElementById('promoInput').value = this.dataset.code;
            checkPromo();
        });
    });

    function toggleFulfillment() {
        const isPickup = document.getElementById('radio-pickup').checked;
        const deliverySection = document.getElementById('delivery-section');
        const pickupSection = document.getElementById('pickup-section');
        const deliveryInput = document.getElementById('delivery-input');
        const pickupInput = document.getElementById('pickup-input');
        const labelDelivery = document.getElementById('label-delivery');
        const labelPickup = document.getElementById('label-pickup');

        if (isPickup) {
            deliverySection.style.display = 'none';
            pickupSection.style.display = 'block';
            deliveryInput.disabled = true; deliveryInput.required = false;
            pickupInput.disabled = false; pickupInput.required = true;
            labelDelivery.style.background = 'transparent'; labelDelivery.style.color = 'var(--brown-dark)';
            labelDelivery.querySelector('i').className = 'bi bi-square';
            labelPickup.style.background = 'var(--brown-dark)'; labelPickup.style.color = 'white';
            labelPickup.querySelector('i').className = 'bi bi-check-lg';
        } else {
            deliverySection.style.display = 'block';
            pickupSection.style.display = 'none';
            deliveryInput.disabled = false; deliveryInput.required = true;
            pickupInput.disabled = true; pickupInput.required = false;
            labelDelivery.style.background = 'var(--brown-dark)'; labelDelivery.style.color = 'white';
            labelDelivery.querySelector('i').className = 'bi bi-check-lg';
            labelPickup.style.background = 'transparent'; labelPickup.style.color = 'var(--brown-dark)';
            labelPickup.querySelector('i').className = 'bi bi-square';
        }
        updateTotalDisplay();
    }

    // ✅ INISIALISASI PETA LEAFLET DI CHECKOUT
    document.addEventListener('DOMContentLoaded', function() {
        toggleFulfillment();

        // Cek apakah elemen map ada (hanya jalankan jika di section delivery)
        if (document.getElementById('mapCheckout')) {
            const tokoLat = {{ $tokoLat }};
            const tokoLng = {{ $tokoLng }};

            const map = L.map('mapCheckout').setView([tokoLat, tokoLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            L.marker([tokoLat, tokoLng]).addTo(map)
                .bindPopup('<b>🏠 Dapur Th Sant</b><br>{{ $data["address"] ?? "Lokasi Toko" }}')
                .openPopup();
        }
    });
</script>
@endpush
@endsection
