@extends('layouts.admin')

@section('title', 'Manajemen User & Staf')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold text-brown mb-0">👥 Manajemen User & Staf</h4>
    <button class="btn text-white" style="background: var(--brown-dark);" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="bi bi-person-plus-fill"></i> Tambah User Baru
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="admin-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background: var(--cream-light);">
                <tr>
                    <th class="ps-4" style="width: 5%;">#</th>
                    <th style="width: 30%;">Nama Lengkap</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 10%;">Dibuat</th>
                    <th class="text-end pe-4" style="width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr>
                    <td class="ps-4 font-mono text-muted">{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 35px; height: 35px; background: var(--brown-dark); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="fw-bold text-brown">{{ $user->name }}</span>
                            @if($user->id === auth()->id())
                                <span class="badge" style="background: var(--cream-dark); color: var(--brown-dark); font-size: 0.6rem;">(ANDA)</span>
                            @endif
                        </div>
                    </td>
                    <td class="text-muted">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'OWNER')
                            <span class="badge" style="background: var(--orange-fav); color: white;">OWNER</span>
                        @else
                            <span class="badge" style="background: var(--green-stock); color: white;">ADMIN</span>
                        @endif
                    </td>
                    <td class="font-mono" style="font-size: 0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit User -->
                <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 16px;">
                            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">Edit User: {{ $user->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Role</label>
                                        <select name="role" class="form-select" required>
                                            <option value="ADMIN" {{ $user->role == 'ADMIN' ? 'selected' : '' }}>Admin (Operasional Harian)</option>
                                            <option value="OWNER" {{ $user->role == 'OWNER' ? 'selected' : '' }}>Owner (Pemilik Bisnis)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">Password Baru <span class="text-muted">(Kosongkan jika tidak ingin mengubah)</span></label>
                                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter">
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn text-white" style="background: var(--brown-dark);">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Belum ada user selain Anda.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah User Baru -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px;">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">➕ Tambah User / Staf Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    {{-- ✅ PERBAIKAN S2-05: Menampilkan Error Validasi Server-Side --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show p-2" role="alert">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email (Untuk Login)</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="budi@dapurthsant.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Role / Jabatan</label>
                        <select name="role" class="form-select" required>
                            <option value="ADMIN" {{ old('role') == 'ADMIN' ? 'selected' : '' }}>Admin (Operasional Harian)</option>
                            <option value="OWNER" {{ old('role') == 'OWNER' ? 'selected' : '' }}>Owner (Pemilik Bisnis)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white" style="background: var(--green-stock);">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
