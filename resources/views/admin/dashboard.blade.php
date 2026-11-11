@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.8rem; color: var(--brown-dark);">
                Selamat Datang Kembali, {{ Auth::user()->name }} 👋
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                Operasional Hari Ini: {{ now()->translatedFormat('l, d F Y') }} • Jam Buka 09.00 - 21.00 WIB
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.products') }}" class="btn d-flex align-items-center gap-2" style="background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <i class="bi bi-plus-lg"></i> Tambah Menu
            </a>
            <button class="btn btn-outline-secondary d-flex align-items-center gap-2" style="border-radius: 10px; padding: 10px 20px;" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Refresh Data
            </button>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="alert d-flex align-items-center gap-2 mb-4" style="border-radius: 12px; border: none; background: var(--green-bg); color: var(--green-stock);">
        <i class="bi bi-circle-fill" style="font-size: 0.5rem;"></i>
        <strong>Dapur Buka</strong> • Menerima Pesanan
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- KPI 1: Order Flow -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100" style="border-radius: 16px; border: 1px solid var(--border-color); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(61, 40, 23, 0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Order Baru Masuk</div>
                            <div class="badge bg-success mt-1" style="font-size: 0.7rem;">Realtime</div>
                        </div>
                        <i class="bi bi-bag-check" style="font-size: 1.5rem; color: var(--brown-dark);"></i>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 2rem; color: var(--brown-dark);">{{ $stats['pending_orders'] ?? 0 }} Pesanan</h2>
                    <div class="d-flex gap-3" style="font-size: 0.85rem;">
                        <div>
                            <span class="badge bg-warning text-dark">{{ $stats['pending_orders'] ?? 0 }}</span>
                            <span class="text-muted ms-1">Konfirmasi</span>
                        </div>
                        <div>
                            <span class="badge bg-info text-white">{{ max(0, ($stats['total_orders'] ?? 0) - ($stats['pending_orders'] ?? 0) - ($stats['completed_orders'] ?? 0)) }}</span>
                            <span class="text-muted ms-1">Sedang Dikukus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Revenue -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100" style="border-radius: 16px; border: 1px solid var(--border-color); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(61, 40, 23, 0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Penjualan Hari Ini</div>
                            <div class="badge bg-success mt-1" style="font-size: 0.7rem;">+18%</div>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 1.5rem; color: var(--brown-dark);"></i>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 2rem; color: var(--brown-dark);">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h2>
                    <div style="font-size: 0.85rem;">
                        <span class="text-muted">vs Kemarin: Rp 1.258.000</span>
                        <span class="text-success fw-bold ms-2">+18% ↑</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Inventory -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100" style="border-radius: 16px; border: 1px solid var(--border-color); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(61, 40, 23, 0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Produk Aktif di Katalog</div>
                            <div class="badge bg-danger mt-1" style="font-size: 0.7rem;">{{ $lowStockProducts->count() }} Habis</div>
                        </div>
                        <i class="bi bi-box-seam" style="font-size: 1.5rem; color: var(--brown-dark);"></i>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 2rem; color: var(--brown-dark);">{{ $stats['active_products'] ?? 0 }} Menu Aktif</h2>
                    <div class="d-flex gap-3" style="font-size: 0.85rem;">
                        <div>
                            <span class="text-danger fw-bold">{{ $lowStockProducts->count() }}</span>
                            <span class="text-muted ms-1">Stok Menipis</span>
                        </div>
                        <div>
                            <span class="text-success fw-bold">{{ max(0, ($stats['total_products'] ?? 0) - $lowStockProducts->count()) }}</span>
                            <span class="text-muted ms-1">Siap Jual</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Vouchers -->
        <div class="col-md-6 col-lg-3">
            <div class="card h-100" style="border-radius: 16px; border: 1px solid var(--border-color); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(61, 40, 23, 0.08)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="text-muted" style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Promo Digunakan</div>
                            <div class="badge bg-primary mt-1" style="font-size: 0.7rem;">Aktif: {{ $stats['active_promos'] ?? 0 }}</div>
                        </div>
                        <i class="bi bi-ticket-perforated" style="font-size: 1.5rem; color: var(--brown-dark);"></i>
                    </div>
                    <h2 class="fw-bold mb-2" style="font-size: 2rem; color: var(--brown-dark);">24x Klaim</h2>
                    <div style="font-size: 0.85rem;">
                        <span class="text-muted">Kode 'SANT10'</span>
                        <span class="text-primary fw-bold ms-2">Diskon Hemat 10%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card" style="border-radius: 16px; border: 1px solid var(--border-color);">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--brown-dark);">Order Masuk Perlu Tindakan Cepat</h5>
                            <small class="text-muted">
                                <span class="badge bg-success me-1">Realtime</span>
                                {{ $recentOrders->where('status', 'MENUNGGU_KONFIRMASI')->count() }} Pesanan Menunggu
                            </small>
                        </div>
                        <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">Lihat Semua</a>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($recentOrders->where('status', 'MENUNGGU_KONFIRMASI') as $order)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="fw-bold" style="font-size: 0.9rem;">#{{ $order->order_ref }}</span>
                                <span class="text-muted ms-2" style="font-size: 0.75rem;">{{ $order->created_at->format('H:i') }} WIB</span>
                            </div>
                            <div class="d-flex gap-2">
                                <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $order->status) }}</span>
                                <span class="badge bg-light text-dark" style="font-size: 0.75rem;">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <div class="text-muted" style="font-size: 0.7rem; margin-bottom: 4px; text-transform: uppercase; font-weight: 600;">Nama Pelanggan</div>
                                <div class="fw-bold">{{ $order->customer_name }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">WA: {{ $order->customer_phone }}</div>
                            </div>
                            <div class="col-md-7">
                                <div class="text-muted" style="font-size: 0.7rem; margin-bottom: 4px; text-transform: uppercase; font-weight: 600;">Detail Pesanan</div>
                                @foreach($order->orderItems as $item)
                                <div class="d-flex justify-content-between" style="font-size: 0.85rem;">
                                    <span>{{ $item->quantity }}x {{ $item->product->name }}</span>
                                    <span class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                                @endforeach
                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                    <span class="fw-bold">Total Tagihan</span>
                                    <span class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($order->rejection_reason)
                        <div class="mt-3 p-2" style="background: var(--cream); border-radius: 6px; font-size: 0.85rem;">
                            <span class="fw-bold">Catatan:</span> {{ $order->rejection_reason }}
                        </div>
                        @endif

                        <div class="d-flex gap-2 mt-3 justify-content-end">
                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan order ini? Stok akan dikembalikan.')">
                                @csrf
                                <input type="hidden" name="status" value="DIBATALKAN">
                                <input type="hidden" name="rejection_reason" value="Dibatalkan oleh Admin dari Dashboard">
                                <button type="submit" class="btn btn-outline-danger btn-sm" style="border-radius: 8px; padding: 8px 16px;">
                                    Tolak / Batal
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="DIPROSES">
                                <button type="submit" class="btn btn-success btn-sm" style="border-radius: 8px; padding: 8px 16px; background: var(--green-stock); border: none;">
                                    <i class="bi bi-check-lg"></i> Konfirmasi & Mulai Kukus
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <div style="width: 60px; height: 60px; background: var(--cream); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="bi bi-inbox" style="font-size: 1.5rem;"></i>
                        </div>
                        <p class="mb-0">Tidak ada order menunggu konfirmasi. Dapur siap menerima pesanan baru!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Stok Kritis -->
            <div class="card mb-4" style="border-radius: 16px; border: 1px solid var(--border-color);">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1" style="color: var(--brown-dark);">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>Stok Produk Kritis
                            </h5>
                            <small class="text-muted">Perlu Restock Segera</small>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    @forelse($lowStockProducts as $product)
                    <div class="d-flex align-items-center gap-3 mb-3 pb-3" style="border-bottom: 1px solid var(--border-color);">
                        <div style="width: 50px; height: 50px; background: var(--cream); border-radius: 10px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                            @if($product->image_url)
                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' agar gambar muncul --}}
                                <img src="{{ asset($product->image_url) }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.9rem;">{{ $product->name }}</div>
                            <small class="text-muted">{{ $product->category->name ?? 'Tanpa Kategori' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-danger" style="font-size: 0.8rem;">{{ $product->stock }} pcs</div>
                            <div style="font-size: 0.7rem; color: var(--text-muted);">Sisa Stok</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle" style="font-size: 2.5rem; color: var(--green-stock); opacity: 0.5;"></i>
                        <p class="text-muted mt-2 mb-0" style="font-size: 0.9rem;">Semua stok aman</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Saluran Pesanan -->
            <div class="card mb-4" style="border-radius: 16px; border: 1px solid var(--border-color);">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-1" style="color: var(--brown-dark);">Saluran Pesanan Hari Ini</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                            <span>WhatsApp Direct (45%)</span>
                            <span class="fw-bold">18 Order</span>
                        </div>
                        <div class="progress" style="height: 8px; background: var(--cream-dark); border-radius: 4px;">
                            <div class="progress-bar" style="width: 45%; background: var(--brown-dark); border-radius: 4px;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                            <span>Web Katalog Publik (40%)</span>
                            <span class="fw-bold">16 Order</span>
                        </div>
                        <div class="progress" style="height: 8px; background: var(--cream-dark); border-radius: 4px;">
                            <div class="progress-bar" style="width: 40%; background: var(--brown-medium); border-radius: 4px;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1" style="font-size: 0.85rem;">
                            <span>Ojol Delivery (15%)</span>
                            <span class="fw-bold">6 Order</span>
                        </div>
                        <div class="progress" style="height: 8px; background: var(--cream-dark); border-radius: 4px;">
                            <div class="progress-bar" style="width: 15%; background: var(--brown-light); border-radius: 4px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksi Cepat -->
            <div class="card" style="border-radius: 16px; border: 1px solid var(--border-color);">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-1" style="color: var(--brown-dark);">Aksi Cepat</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <a href="{{ route('admin.products') }}" class="d-flex align-items-center gap-3 p-3 mb-2 border rounded" style="text-decoration: none; color: var(--text-dark); transition: all 0.2s;" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='white'">
                        <i class="bi bi-plus-square fs-4" style="color: var(--brown-dark);"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.9rem;">Tambah Menu Baru</div>
                            <small class="text-muted">Input foto, varian & stok dapur</small>
                        </div>
                        <i class="bi bi-arrow-right" style="color: var(--text-muted);"></i>
                    </a>

                    <a href="{{ route('admin.promos') }}" class="d-flex align-items-center gap-3 p-3 border rounded" style="text-decoration: none; color: var(--text-dark); transition: all 0.2s;" onmouseover="this.style.background='var(--cream)'" onmouseout="this.style.background='white'">
                        <i class="bi bi-tag fs-4" style="color: var(--brown-dark);"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.9rem;">Pengaturan Promo</div>
                            <small class="text-muted">Kelola voucher & diskon aktif</small>
                        </div>
                        <i class="bi bi-arrow-right" style="color: var(--text-muted);"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Efficiency Matrix -->
    <div class="card mt-4" style="border-radius: 16px; border: 1px solid var(--border-color);">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-1" style="color: var(--brown-dark);">Matriks Efisiensi Operasional Dapur</h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center" style="background: var(--cream-light);">
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Rata-rata Konfirmasi</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: var(--brown-dark);">2.4 mnt</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center" style="background: var(--cream-light);">
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Durasi Kukus Rata-rata</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: var(--brown-dark);">11.8 mnt</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 border rounded text-center" style="background: var(--cream-light);">
                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600;">Tingkat Kepuasan Pelanggan</div>
                        <div class="fw-bold text-success" style="font-size: 1.5rem;">98.5%</div>
                    </div>
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

</div>
@endsection
