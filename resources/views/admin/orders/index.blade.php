@extends('layouts.admin')

@section('title', 'Kelola Order')
@section('page-title', 'Daftar Semua Order')

@section('content')
<div class="table-custom p-4 mb-4">
    <form method="GET" action="{{ route('admin.orders') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
            <select id="metode_pembayaran" name="metode_pembayaran" class="form-select">
                <option value="">Semua metode</option>
                @foreach(['Tunai', 'QRIS', 'Transfer'] as $metode)
                    <option value="{{ $metode }}" {{ request('metode_pembayaran') === $metode ? 'selected' : '' }}>{{ $metode }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="status" class="form-label">Status Pesanan</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach(['MENUNGGU_KONFIRMASI', 'DIPROSES', 'DIKIRIM', 'SELESAI', 'DIBATALKAN'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ str_replace('_', ' ', $status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn-admin"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('admin.orders') }}" class="btn-admin-outline">Reset</a>
        </div>
    </form>
</div>

<div class="table-custom p-4 mb-4">
    <div class="text-muted">Total nominal dari {{ $orders->count() }} pesanan terfilter</div>
    <div class="fs-4 fw-bold text-brown">Rp {{ number_format($totalTerfilter, 0, ',', '.') }}</div>
</div>

<div class="table-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Order Ref</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td class="font-monospace fw-bold">{{ $order->order_ref }}</td>
                    <td>
                        <div class="fw-bold">{{ $order->customer_name }}</div>
                        <small class="text-muted">{{ $order->customer_phone }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">
                            {{ $order->metode_pembayaran }}
                        </span>
                    </td>
                    <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $badge = match($order->status) {
                                'MENUNGGU_KONFIRMASI' => 'bg-warning text-dark',
                                'DIPROSES' => 'bg-info text-white',
                                'DIKIRIM' => 'bg-primary text-white',
                                'SELESAI' => 'bg-success text-white',
                                'DIBATALKAN' => 'bg-danger text-white',
                                default => 'bg-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ str_replace('_', ' ', $order->status) }}</span>
                    </td>
                    <td>
                        <!-- ✅ TOMBOL DETAIL YANG BENAR -->
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn-admin btn-sm" style="padding: 6px 12px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-2">Belum ada data order.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="text-center mt-4 font-mono" style="font-size: 0.7rem; color: var(--text-muted);">
    BLUEPRINT: ADMIN_SPEC_v1.0 // WIREFRAME SCHEMATIC // DAPUR TH SANT
</div>
@endsection
