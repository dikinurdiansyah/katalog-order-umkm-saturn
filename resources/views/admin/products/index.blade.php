@extends('layouts.admin')

@section('title', 'Kelola Produk & Kategori')

@section('content')
<!-- Header -->
<div class="mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <h1 class="fw-bold mb-1" style="font-size: 1.8rem; color: var(--brown-dark);">
                Kelola Produk & Kategori
            </h1>
            <p class="text-muted mb-0">Tambah, edit, atau nonaktifkan menu yang tampil di katalog pelanggan.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn-admin" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                <i class="bi bi-plus-lg"></i> Tambah Menu Baru
            </button>
        </div>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-box-seam" style="font-size: 1.2rem; color: var(--brown-dark);"></i>
                <div class="kpi-label mb-0">TOTAL MENU AKTIF</div>
            </div>
            <div class="kpi-value">{{ $products->where('is_active', true)->count() }} Item</div>
            <div class="text-muted" style="font-size: 0.8rem;">Siap dijual di katalog publik</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-exclamation-triangle" style="font-size: 1.2rem; color: var(--orange-fav);"></i>
                <div class="kpi-label mb-0" style="color: var(--orange-fav);">STOK MENIPIS</div>
            </div>
            <div class="kpi-value" style="color: var(--orange-fav);">{{ $products->where('stock', '<', 10)->where('is_active', true)->count() }} Item</div>
            <div class="text-muted" style="font-size: 0.8rem;">Perlu restock segera (&lt; 10 porsi)</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="kpi-card">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-folder" style="font-size: 1.2rem; color: var(--brown-dark);"></i>
                <div class="kpi-label mb-0">TOTAL KATEGORI</div>
            </div>
            <div class="kpi-value">{{ $categories->count() }} Kategori</div>
            <div class="text-muted" style="font-size: 0.8rem;">Pengelompokan menu</div>
        </div>
    </div>
</div>

<!-- Product Table -->
<div class="admin-card">
    <div class="card-header-custom">
        <div class="card-title-custom">
            <i class="bi bi-list-ul"></i> Daftar Menu
        </div>
        <div class="d-flex gap-2">
            <input type="text" id="searchProduct" class="form-control form-control-sm" placeholder="🔍 Cari nama produk..." style="width: 250px;" onkeyup="filterProducts()">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-admin table-hover align-middle" id="productTable">
            <thead>
                <tr>
                    <th style="width: 80px;">FOTO</th>
                    <th>NAMA PRODUK</th>
                    <th>KATEGORI</th>
                    <th>HARGA</th>
                    <th>STOK</th>
                    <th>STATUS</th>
                    <th class="text-end">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="product-row" data-name="{{ strtolower($product->name) }}">
                    <td>
                        @if($product->image_url)
                            {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                            <img src="{{ asset($product->image_url) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                        @else
                            <div style="width: 50px; height: 50px; background: var(--cream); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold">{{ $product->name }}</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Slug: {{ $product->slug }}</small>
                    </td>
                    <td>
                        <span class="badge-admin" style="background: var(--cream); color: var(--brown-dark);">
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        @if($product->stock < 10)
                            <span class="text-danger fw-bold">{{ $product->stock }}</span>
                        @else
                            <span class="fw-bold">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_active)
                            <span class="badge-admin badge-completed">Aktif</span>
                        @else
                            <span class="badge-admin badge-cancelled">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            <button class="btn-admin-outline btn-sm" style="padding: 6px 12px; font-size: 0.8rem;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditProduct{{ $product->id }}">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: #fde8e8; color: #c53030; border: none; padding: 6px 12px; font-size: 0.8rem; border-radius: 6px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Modal Edit Produk -->
                <div class="modal fade" id="modalEditProduct{{ $product->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header" style="background: var(--cream-light); border-bottom: 1px solid var(--border-color);">
                                    <h5 class="modal-title fw-bold">Edit Menu: {{ $product->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Nama Produk *</label>
                                            <input type="text" name="name" class="form-control" value="{{ $product->name }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Kategori *</label>
                                            <select name="id_category" class="form-select" required>
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ $product->id_category == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Harga (Rp) *</label>
                                            <input type="number" name="price" class="form-control" value="{{ $product->price }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Stok *</label>
                                            <input type="number" name="stock" class="form-control" value="{{ $product->stock }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Deskripsi</label>
                                            <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                                        </div>

                                        <!-- ✅ FOTO PRODUK (4 INPUT) - DIPERBAIKI -->
                                        <div class="col-12">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Utama *</label>
                                            <input type="file" name="image" class="form-control" accept="image/*">
                                            @if($product->image_url)
                                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                                                <small class="text-muted d-block mt-1">Foto saat ini: <a href="{{ asset($product->image_url) }}" target="_blank">Lihat</a></small>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 2</label>
                                            <input type="file" name="image_2" class="form-control" accept="image/*">
                                            @if($product->image_2)
                                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                                                <small class="text-muted d-block mt-1"><a href="{{ asset($product->image_2) }}" target="_blank">Lihat</a></small>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 3</label>
                                            <input type="file" name="image_3" class="form-control" accept="image/*">
                                            @if($product->image_3)
                                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                                                <small class="text-muted d-block mt-1"><a href="{{ asset($product->image_3) }}" target="_blank">Lihat</a></small>
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 4</label>
                                            <input type="file" name="image_4" class="form-control" accept="image/*">
                                            @if($product->image_4)
                                                {{-- ✅ DIPERBAIKI: Hapus 'storage/' --}}
                                                <small class="text-muted d-block mt-1"><a href="{{ asset($product->image_4) }}" target="_blank">Lihat</a></small>
                                            @endif
                                        </div>

                                        <div class="col-12">
                                            <div class="form-check p-3 border rounded" style="background: var(--cream-light);">
                                                <input type="hidden" name="is_active" value="0">
                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive{{ $product->id }}" {{ $product->is_active ? 'checked' : '' }} style="accent-color: var(--brown-dark);">
                                                <label class="form-check-label" for="editActive{{ $product->id }}" style="font-size: 0.9rem;">
                                                    Produk Aktif (Tampil di Katalog)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="background: var(--cream-light); border-top: 1px solid var(--border-color);">
                                    <button type="button" class="btn-admin-outline" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn-admin">
                                        <i class="bi bi-save"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2">Belum ada produk ditambahkan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="modalAddProduct" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header" style="background: var(--cream-light); border-bottom: 1px solid var(--border-color);">
                    <h5 class="modal-title fw-bold">Tambah Menu Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Nama Produk *</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Dimsum Ayam Original">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Kategori *</label>
                            <select name="id_category" class="form-select @error('id_category') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('id_category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('id_category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Harga (Rp) *</label>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required placeholder="15000">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Stok Awal *</label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock') }}" required placeholder="50">
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Deskripsi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Deskripsi singkat produk...">{{ old('description') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- ✅ FOTO PRODUK (4 INPUT) -->
                        <div class="col-12">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Utama *</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted" style="font-size: 0.8rem;">Format: JPG, PNG. Maksimal 2MB.</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 2 (Opsional)</label>
                            <input type="file" name="image_2" class="form-control @error('image_2') is-invalid @enderror" accept="image/*">
                            @error('image_2') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 3 (Opsional)</label>
                            <input type="file" name="image_3" class="form-control @error('image_3') is-invalid @enderror" accept="image/*">
                            @error('image_3') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Tambahan 4 (Opsional)</label>
                            <input type="file" name="image_4" class="form-control @error('image_4') is-invalid @enderror" accept="image/*">
                            @error('image_4') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check p-3 border rounded" style="background: var(--cream-light);">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="addActive" {{ old('is_active', 1) ? 'checked' : '' }} style="accent-color: var(--brown-dark);">
                                <label class="form-check-label" for="addActive" style="font-size: 0.9rem;">
                                    Langsung Aktifkan di Katalog
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: var(--cream-light); border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn-admin-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn-admin">
                        <i class="bi bi-save"></i> Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterProducts() {
    const input = document.getElementById('searchProduct').value.toLowerCase();
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        row.style.display = name.includes(input) ? '' : 'none';
    });
}
</script>
@endpush
@endsection
