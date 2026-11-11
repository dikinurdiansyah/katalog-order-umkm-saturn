@extends('layouts.admin')

@section('title', 'Kelola Kode Promo')

@section('content')
<!-- Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.8rem; color: var(--brown-dark);">
                Manajemen Kode Promo & Voucher
            </h1>
            <p class="text-muted mb-0">Kelola kode promo yang dapat digunakan pelanggan saat checkout.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-admin-outline" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Refresh Data
            </button>
            <button class="btn-admin" data-bs-toggle="modal" data-bs-target="#modalAddPromo">
                <i class="bi bi-plus-lg"></i> Buat Kode Promo Baru
            </button>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="kpi-label mb-0">Total Promo Aktif</div>
                <span class="badge bg-success">Live</span>
            </div>
            <div class="kpi-value">{{ $promos->where('is_active', true)->count() }} Voucher</div>
            <div class="text-muted" style="font-size: 0.8rem;">Siap dipakai pada keranjang belanja</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="kpi-label mb-0">Total Digunakan</div>
                <span class="badge" style="background: #dbeafe; color: #1e40af;">+18% Minggu Ini</span>
            </div>
            <div class="kpi-value">142x</div>
            <div class="text-success" style="font-size: 0.8rem;">
                <i class="bi bi-arrow-up"></i> Meningkat dari 120x minggu lalu
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="kpi-label mb-0">Total Diskon Diberikan</div>
                <span class="badge" style="background: var(--cream); color: var(--brown-dark);">Tahun 2026</span>
            </div>
            <div class="kpi-value">Rp 738.400</div>
            <div class="text-muted" style="font-size: 0.8rem;">Hemat yang dinikmati pelanggan</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: List Promo -->
    <div class="col-lg-7">
        <div class="admin-tabs mb-3" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0;">
            <a href="#" class="admin-tab active" onclick="filterByStatus('all', this); return false;">Semua ({{ $promos->count() }})</a>
            <a href="#" class="admin-tab" onclick="filterByStatus('active', this); return false;">Aktif ({{ $promos->where('is_active', true)->count() }})</a>
            <a href="#" class="admin-tab" onclick="filterByStatus('expired', this); return false;">Kedaluwarsa</a>
            <a href="#" class="admin-tab" onclick="filterByStatus('inactive', this); return false;">Nonaktif</a>

            <div class="ms-auto">
                <input type="text" id="searchPromo" class="form-control form-control-sm" placeholder="🔍 Cari kode..." style="width: 200px;" onkeyup="filterPromos()">
            </div>
        </div>

        @foreach($promos as $promo)
        @php
            $validUntil = $promo->valid_until ? \Carbon\Carbon::parse($promo->valid_until) : null;
            $isExpired = $validUntil && $validUntil->isPast();
        @endphp
        <div class="admin-card mb-3 promo-card" data-status="{{ $promo->is_active ? 'active' : 'inactive' }}" data-valid-until="{{ $validUntil ? $validUntil->format('Y-m-d') : '' }}" data-code="{{ strtolower($promo->code) }}" style="{{ !$promo->is_active || $isExpired ? 'opacity: 0.7;' : '' }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="badge-admin" style="background: {{ $promo->discount_type === 'PERCENT' ? 'var(--warning-bg)' : 'var(--cream-dark)' }}; color: var(--brown-dark); font-size: 0.9rem; padding: 8px 12px; font-weight: 700;">
                        {{ $promo->discount_type === 'PERCENT' ? $promo->discount_amount . '%' : 'Rp ' . number_format($promo->discount_amount, 0, ',', '.') }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="font-mono" style="font-size: 1.1rem; color: var(--brown-dark);">{{ $promo->code }}</strong>
                            @if($promo->is_active && !$isExpired)
                                <span class="badge-admin badge-completed">Aktif</span>
                            @elseif($isExpired)
                                <span class="badge-admin badge-cancelled">Kedaluwarsa</span>
                            @else
                                <span class="badge-admin" style="background: var(--cream-dark); color: var(--text-muted);">Nonaktif</span>
                            @endif
                        </div>
                        <div class="text-muted" style="font-size: 0.85rem; margin-top: 4px;">
                            {{ $promo->discount_type === 'PERCENT' ? 'Diskon Persentase ' . $promo->discount_amount . '%' : 'Potongan Langsung Rp ' . number_format($promo->discount_amount, 0, ',', '.') }}
                            @if($promo->min_order_amount > 0)
                                • Min. Belanja Rp {{ number_format($promo->min_order_amount, 0, ',', '.') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <form method="POST" action="{{ route('admin.promos.update', $promo) }}" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="code" value="{{ $promo->code }}">
                        <input type="hidden" name="discount_type" value="{{ $promo->discount_type }}">
                        <input type="hidden" name="discount_amount" value="{{ $promo->discount_amount }}">
                        <input type="hidden" name="min_order_amount" value="{{ $promo->min_order_amount }}">
                        <input type="hidden" name="valid_until" value="{{ $validUntil ? $validUntil->format('Y-m-d') : \Carbon\Carbon::now()->addDays(30)->format('Y-m-d') }}">
                        <input type="hidden" name="is_active" value="{{ $promo->is_active ? '0' : '1' }}">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" {{ $promo->is_active ? 'checked' : '' }} onchange="this.form.submit()" style="accent-color: var(--brown-dark); cursor: pointer;">
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Min. Belanja: <strong class="text-dark">Rp {{ number_format($promo->min_order_amount, 0, ',', '.') }}</strong></div>
                </div>
                <div class="col-6 text-end">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Berlaku hingga: <strong class="{{ $isExpired ? 'text-danger' : 'text-dark' }}">{{ $validUntil ? $validUntil->format('d M Y') : '-' }}</strong></div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                <div style="font-size: 0.75rem; color: var(--text-muted);">ID: {{ $promo->id }}</div>
                <div class="d-flex gap-2">
                    <button class="btn-admin-outline btn-sm" style="padding: 6px 12px; font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#modalEditPromo{{ $promo->id }}">
                        <i class="bi bi-pencil"></i> Edit
                    </button>
                    <form method="POST" action="{{ route('admin.promos.destroy', $promo) }}" onsubmit="return confirm('Yakin ingin menghapus promo {{ $promo->code }}?')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="background: #fde8e8; color: #c53030; border: none; padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Promo -->
        <div class="modal fade" id="modalEditPromo{{ $promo->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.promos.update', $promo) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-header" style="background: var(--cream-light); border-bottom: 1px solid var(--border-color);">
                            <h5 class="modal-title fw-bold">Edit Promo: {{ $promo->code }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Kode Promo *</label>
                                    <input type="text" name="code" class="form-control" value="{{ $promo->code }}" required style="text-transform: uppercase;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Tipe Diskon *</label>
                                    <select name="discount_type" class="form-select" required>
                                        <option value="PERCENT" {{ $promo->discount_type == 'PERCENT' ? 'selected' : '' }}>Persentase (%)</option>
                                        <option value="FIXED" {{ $promo->discount_type == 'FIXED' ? 'selected' : '' }}>Nominal (Rp)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Nilai Diskon *</label>
                                    <input type="number" name="discount_amount" class="form-control" value="{{ $promo->discount_amount }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Min. Pembelian *</label>
                                    <input type="number" name="min_order_amount" class="form-control" value="{{ $promo->min_order_amount }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Berlaku Sampai *</label>
                                    <input type="date" name="valid_until" class="form-control" value="{{ $validUntil ? $validUntil->format('Y-m-d') : '' }}" required>
                                </div>
                                <div class="col-12">
                                    <div class="form-check p-3 border rounded" style="background: var(--cream-light);">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive{{ $promo->id }}" {{ $promo->is_active ? 'checked' : '' }} style="accent-color: var(--brown-dark);">
                                        <label class="form-check-label" for="editActive{{ $promo->id }}" style="font-size: 0.9rem;">Aktifkan promo ini</label>
                                    </div>
                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: var(--cream-light); border-top: 1px solid var(--border-color);">
                            <button type="button" class="btn-admin-outline" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-admin"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Right Column: Form Create -->
    <div class="col-lg-5">
        <div class="admin-card">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-file-earmark-plus"></i> Buat Kode Promo Baru</div>
            </div>

            <form method="POST" action="{{ route('admin.promos.store') }}" id="formCreatePromo">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Kode Promo (Kupon) *</label>
                    <div class="input-group">
                        <span class="input-group-text font-mono">#</span>
                        <input type="text" name="code" id="inputCode" class="form-control" placeholder="DIMSUMJUMAT" style="text-transform: uppercase;" required>
                    </div>
                    <small class="text-muted">Gunakan huruf kapital semua, tanpa spasi.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Tipe Diskon *</label>
                    <div class="d-flex gap-2">
                        <label class="flex-grow-1 text-center p-2 border rounded discount-type-label active-type" style="cursor: pointer; background: var(--brown-dark); color: white; transition: all 0.2s;" onclick="selectDiscountType('PERCENT', this)">
                            <input type="radio" name="discount_type" value="PERCENT" checked class="d-none">
                            Persentase (%)
                        </label>
                        <label class="flex-grow-1 text-center p-2 border rounded discount-type-label" style="cursor: pointer; transition: all 0.2s;" onclick="selectDiscountType('FIXED', this)">
                            <input type="radio" name="discount_type" value="FIXED" class="d-none">
                            Nominal (Rp)
                        </label>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Nilai Diskon *</label>
                        <input type="number" name="discount_amount" id="inputDiscount" class="form-control" placeholder="15" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Min. Pembelian</label>
                        <input type="number" name="min_order_amount" id="inputMin" class="form-control" placeholder="50000">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold" style="font-size: 0.85rem;">Periode Berlaku *</label>
                    <input type="date" name="valid_until" id="inputDate" class="form-control" required>
                </div>

                <div class="form-check mb-3 p-3 border rounded" style="background: var(--cream-light);">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActive" checked style="accent-color: var(--brown-dark);">
                    <label class="form-check-label" for="addActive" style="font-size: 0.9rem;">
                        Aktifkan promo segera
                    </label>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn-admin-outline flex-grow-1" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-admin flex-grow-2"><i class="bi bi-save"></i> Simpan Promo</button>
                </div>
            </form>
        </div>

        <!-- Preview Voucher -->
        <div class="admin-card mt-3">
            <div class="card-header-custom">
                <div class="card-title-custom"><i class="bi bi-phone"></i> Pratinjau Voucher</div>
            </div>
            <div class="p-3 border rounded" style="border-style: dashed !important; background: var(--cream-light);">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="font-mono fw-bold" style="font-size: 1rem; color: var(--brown-dark);" id="previewCode">DIMSUMJUMAT</div>
                        <span class="badge bg-success" style="font-size: 0.7rem;">Siap Klaim</span>
                    </div>
                    <button class="btn btn-sm" style="background: var(--brown-dark); color: white; border: none; border-radius: 6px;" type="button">Pakai</button>
                </div>
                <div class="fw-bold" style="font-size: 1.2rem; color: var(--brown-dark);" id="previewDiscount">Diskon 15%</div>
                <div class="text-muted" style="font-size: 0.85rem;" id="previewDetail">Min. belanja Rp 50.000 • Berlaku hingga 31 Okt 2026</div>
            </div>
        </div>
    </div>
</div>

<!-- Clean Professional Footer -->
<div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border-color) !important;">
    <small class="text-muted" style="font-size: 0.8rem;">
        &copy; {{ date('Y') }} Dapur Th Sant. All rights reserved.<br>
        <span style="font-size: 0.75rem; opacity: 0.7;">Sistem Manajemen Operasional Dapur</span>
    </small>
</div>
@endsection

@push('scripts')
<script>
// 1. Fungsi agar tombol Tipe Diskon bisa diklik dan berubah warna
function selectDiscountType(type, element) {
    document.querySelectorAll('.discount-type-label').forEach(label => {
        label.style.background = '';
        label.style.color = '';
    });
    element.style.background = 'var(--brown-dark)';
    element.style.color = 'white';
    updatePreview();
}

// 2. Fungsi update preview secara real-time (Tanpa teks wireframe)
function updatePreview() {
    const code = document.getElementById('inputCode').value || 'DIMSUMJUMAT';
    const discount = document.getElementById('inputDiscount').value || '15';
    const min = document.getElementById('inputMin').value || '50000';
    const dateVal = document.getElementById('inputDate').value;
    const type = document.querySelector('input[name="discount_type"]:checked').value;

    let discText = type === 'PERCENT' ? `Diskon ${discount}%` : `Diskon Rp ${parseInt(discount).toLocaleString('id-ID')}`;
    let minText = min ? `Min. belanja Rp ${parseInt(min).toLocaleString('id-ID')}` : 'Tanpa min. belanja';
    let dateText = dateVal ? new Date(dateVal).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'}) : '31 Okt 2026';

    document.getElementById('previewCode').textContent = code.toUpperCase();
    document.getElementById('previewDiscount').textContent = discText;
    document.getElementById('previewDetail').textContent = `${minText} • Berlaku hingga ${dateText}`;
}

// 3. Pasang event listener ke semua input
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['inputCode', 'inputDiscount', 'inputMin', 'inputDate'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if(el) {
            el.addEventListener('input', updatePreview);
            el.addEventListener('change', updatePreview);
        }
    });
    document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });
    updatePreview(); // Jalankan sekali di awal
});

// 4. Filter Pencarian
function filterPromos() {
    const input = document.getElementById('searchPromo').value.toLowerCase();
    document.querySelectorAll('.promo-card').forEach(card => {
        card.style.display = card.dataset.code.includes(input) ? '' : 'none';
    });
}

// 5. Filter by Status Tab
function filterByStatus(status, element) {
    document.querySelectorAll('.admin-tab').forEach(tab => tab.classList.remove('active'));
    element.classList.add('active');

    const rows = document.querySelectorAll('.promo-card');
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const validUntil = new Date(row.getAttribute('data-valid-until'));
        const isExpired = validUntil < new Date();

        let show = false;
        if (status === 'all') {
            show = true;
        } else if (status === 'active') {
            show = rowStatus === 'active' && !isExpired;
        } else if (status === 'expired') {
            show = isExpired;
        } else if (status === 'inactive') {
            show = rowStatus === 'inactive';
        }

        row.style.display = show ? '' : 'none';
    });
}
</script>
@endpush
