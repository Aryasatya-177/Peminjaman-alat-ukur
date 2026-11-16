@extends('layouts.app')

@section('title', 'Profil & Data PIC - Gauge Hub')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle">
                        <i class="bi bi-person-gear fs-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">Profil Pengguna & PIC</h4>
                        <small class="text-muted">Data ini menjadi acuan default saat pengajuan peminjaman alat ukur</small>
                    </div>
                </div>

                <form action="{{ route('user.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Terdaftar</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                        <div class="form-text">Alamat email digunakan untuk login dan tidak dapat diubah.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Departemen</label>
                            <input type="text" name="department"
                                class="form-control @error('department') is-invalid @enderror"
                                value="{{ old('department', $user->department) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama PIC (Person In Charge)</label>
                            <input type="text" name="pic" class="form-control @error('pic') is-invalid @enderror"
                                value="{{ old('pic', $user->pic) }}" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="fw-semibold mb-2"><i class="bi bi-shield-lock me-1"></i> Ganti Password (Opsional)</h5>
                    <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin mengubah password.</p>

                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            placeholder="Masukkan password saat ini jika ingin mengubah">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="new_password_confirmation" class="form-control"
                                placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <a href="{{ route('user.dashboard') }}" class="btn-ghost btn">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                        <button type="submit" class="btn-primary-modern btn">
                            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection