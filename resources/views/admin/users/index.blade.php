@extends('layouts.app')

@section('title', 'Manajemen Pengguna & Hak Akses - Admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-people text-primary me-2"></i>Manajemen User & Hak Akses</h2>
            <p class="text-muted mb-0">Kelola akun terdaftar, data departemen, nama PIC, dan tetapkan role (User, Teknisi, Admin)</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Dashboard Admin
        </a>
    </div>

    {{-- Filter Pencarian & Role --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari berdasarkan nama pengguna, email, departemen, atau PIC..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Semua Hak Akses / Role --</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User (Peminjam)</option>
                    <option value="teknisi" {{ request('role') === 'teknisi' ? 'selected' : '' }}>Teknisi (Inspeksi)</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Daftar Pengguna Sistem</h5>
            <span class="badge bg-secondary">{{ $users->total() }} Pengguna</span>
        </div>
        <div class="card-body p-0">
            @if($users->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-people display-4 d-block mb-3"></i>
                    <h5 class="fw-bold text-secondary">Pengguna Tidak Ditemukan</h5>
                    <p class="small text-muted mb-0">Sesuaikan kriteria pencarian nama atau role pengguna.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 5%;">No</th>
                                <th style="width: 25%;">Nama & Email</th>
                                <th style="width: 25%;">Departemen</th>
                                <th style="width: 20%;">PIC Terdaftar</th>
                                <th class="text-center" style="width: 12%;">Hak Akses (Role)</th>
                                <th class="text-end pe-3" style="width: 13%;">Aksi Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                                <tr>
                                    <td class="ps-3 text-muted">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $u->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary border fs-6 fw-semibold">
                                            {{ $u->department ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $u->pic ?? $u->name }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if($u->role === 'admin')
                                            <span class="badge-status badge-admin">Admin</span>
                                        @elseif($u->role === 'teknisi')
                                            <span class="badge-status badge-teknisi">Teknisi</span>
                                        @else
                                            <span class="badge-status badge-user">User</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-sm btn-ghost"
                                            data-bs-toggle="modal" data-bs-target="#modalUser{{ $u->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> Atur Role
                                        </button>
                                    </td>
                                </tr>

                                {{-- Modal Atur Role & Info Pengguna --}}
                                <div class="modal fade" id="modalUser{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.users.updateRole', $u->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-person-gear me-1 text-primary"></i> Atur Role: {{ $u->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Alamat Email (Tetap)</label>
                                                        <input type="text" class="form-control bg-light" value="{{ $u->email }}" readonly disabled>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Hak Akses / Role <span class="text-danger">*</span></label>
                                                        <select name="role" class="form-select" required>
                                                            <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User (Mengajukan peminjaman)</option>
                                                            <option value="teknisi" {{ $u->role === 'teknisi' ? 'selected' : '' }}>Teknisi (Inspeksi fisik & kalibrasi alat)</option>
                                                            <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrator (Manajemen inventaris & laporan)</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama Departemen <span class="text-danger">*</span></label>
                                                        <input type="text" name="department" class="form-control" value="{{ old('department', $u->department) }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama PIC (Person In Charge) <span class="text-danger">*</span></label>
                                                        <input type="text" name="pic" class="form-control" value="{{ old('pic', $u->pic ?? $u->name) }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">
                                                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
