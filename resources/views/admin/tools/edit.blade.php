@extends('layouts.app')

@section('title', 'Edit Alat Ukur Presisi - ' . $tool->nama_alat)

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                            <i class="bi bi-pencil-square fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Edit Alat Ukur & Penyesuaian Stok</h4>
                            <small class="text-muted">Kode Alat: <span class="font-monospace fw-bold">{{ $tool->kode_alat }}</span></small>
                        </div>
                    </div>
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.tools.update', $tool->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nama_alat" class="form-label fw-semibold">
                            Nama Lengkap Alat Ukur <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_alat" id="nama_alat"
                            class="form-control @error('nama_alat') is-invalid @enderror"
                            value="{{ old('nama_alat', $tool->nama_alat) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="kode_alat" class="form-label fw-semibold">
                            Kode / ID Alat <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="kode_alat" id="kode_alat"
                            class="form-control text-uppercase @error('kode_alat') is-invalid @enderror"
                            value="{{ old('kode_alat', $tool->kode_alat) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="total_stock" class="form-label fw-semibold">
                                Total Stok Fisik <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="total_stock" id="total_stock" min="1"
                                class="form-control @error('total_stock') is-invalid @enderror"
                                value="{{ old('total_stock', $tool->total_stock) }}" required>
                            <div class="form-text">Total keseluruhan unit alat yang dimiliki perusahaan.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="available_stock" class="form-label fw-semibold">
                                Stok Tersedia (Siap Pinjam) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="available_stock" id="available_stock" min="0"
                                class="form-control @error('available_stock') is-invalid @enderror"
                                value="{{ old('available_stock', $tool->available_stock) }}" required>
                            <div class="form-text">Stok di rak yang belum dipinjam. Tidak boleh melebihi total stok.</div>
                        </div>
                    </div>

                    <div class="alert alert-warning border p-3 rounded mb-4">
                        <div class="small">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            <strong>Perhatian:</strong> Perubahan stok fisik akan berdampak langsung pada kuantitas alat yang dapat diajukan peminjamannya oleh pengguna di lapangan.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="{{ route('admin.tools.index') }}" class="btn-ghost btn">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary-modern btn">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
