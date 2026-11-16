@extends('layouts.app')

@section('title', 'Tambah Alat Ukur Presisi Baru - Admin')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                            <i class="bi bi-plus-circle fs-3"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0">Tambah Alat Ukur Presisi Baru</h4>
                            <small class="text-muted">Daftarkan alat ukur baru ke dalam sistem inventaris tool crib</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.tools.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_alat" class="form-label fw-semibold">
                            Nama Lengkap Alat Ukur <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nama_alat" id="nama_alat"
                            class="form-control @error('nama_alat') is-invalid @enderror"
                            placeholder="Contoh: Dial Gauge Mitutoyo 0-10mm (0.01mm) atau Bore / Ball Gauge 50-160mm"
                            value="{{ old('nama_alat') }}" required autofocus>
                        <div class="form-text">Sertakan merk, rentang ukur, dan tingkat presisi/resolusinya jika ada.</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="kode_alat" class="form-label fw-semibold">
                                Kode / ID Alat <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kode_alat" id="kode_alat"
                                class="form-control text-uppercase @error('kode_alat') is-invalid @enderror"
                                placeholder="Contoh: DG-009, BG-010, MIC-011"
                                value="{{ old('kode_alat') }}" required>
                            <div class="form-text">Kode unik inventaris (ID registrasi fisik alat).</div>
                        </div>

                        <div class="col-md-6">
                            <label for="total_stock" class="form-label fw-semibold">
                                Total Stok Fisik (Unit) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="total_stock" id="total_stock" min="1"
                                class="form-control @error('total_stock') is-invalid @enderror"
                                placeholder="Jumlah unit fisik yang tersedia"
                                value="{{ old('total_stock', 5) }}" required>
                            <div class="form-text">Stok tersedia otomatis disamakan dengan total awal.</div>
                        </div>
                    </div>

                    <div class="alert alert-light border p-3 rounded mb-4">
                        <div class="small text-muted">
                            <i class="bi bi-info-circle text-primary me-1"></i>
                            <strong>Petunjuk:</strong> Alat ukur yang baru didaftarkan akan otomatis memiliki status ketersediaan siap dipinjam oleh seluruh pengguna yang terdaftar di sistem.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                        <a href="{{ route('admin.tools.index') }}" class="btn-ghost btn">
                            Batal
                        </a>
                        <button type="submit" class="btn-primary-modern btn">
                            <i class="bi bi-check-lg me-1"></i> Simpan Alat Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
