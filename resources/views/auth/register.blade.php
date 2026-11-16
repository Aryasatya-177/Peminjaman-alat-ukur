@extends('layouts.app')

@section('title', 'Pendaftaran Akun - GaugeHub')

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-xl-6 col-lg-7 col-md-9">
        <div class="card p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex p-2 rounded-3 text-white mb-3" style="background: var(--primary); border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 2px 8px rgba(30, 58, 95, 0.2);">
                    <i class="bi bi-speedometer2 fs-3 px-1 text-warning"></i>
                </div>
                <h3 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.02em;">Pendaftaran Akun : Gauge<span style="color: #f59e0b;">Hub</span></h3>
                <p class="text-secondary small mb-0">Daftarkan akun peminjam alat ukur presisi untuk personil laboratorium atau workshop</p>
            </div>

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold small text-dark">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="name" id="name" class="form-control form-control-modern @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required autofocus>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold small text-dark">Email Kerja <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" class="form-control form-control-modern @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" placeholder="user@perusahaan.com" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="department" class="form-label fw-semibold small text-dark">Nama Departemen <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-building"></i></span>
                            <input type="text" name="department" id="department" class="form-control form-control-modern @error('department') is-invalid @enderror" 
                                   value="{{ old('department') }}" placeholder="Contoh: QC / Machining / Assembly" required>
                        </div>
                        <div class="form-text small text-secondary">Bagian atau seksi pemakai alat ukur.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="pic" class="form-label fw-semibold small text-dark">Nama PIC (Person In Charge) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-person-badge"></i></span>
                            <input type="text" name="pic" id="pic" class="form-control form-control-modern @error('pic') is-invalid @enderror" 
                                   value="{{ old('pic') }}" placeholder="Contoh: Budi Santoso (Lead QC)" required>
                        </div>
                        <div class="form-text small text-secondary">Penanggung jawab fisik atas alat ukur.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold small text-dark">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" id="password" class="form-control form-control-modern @error('password') is-invalid @enderror" 
                                   placeholder="Minimal 6 karakter" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold small text-dark">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-secondary border-end-0"><i class="bi bi-check2-circle"></i></span>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-modern" 
                                   placeholder="Ulangi password di atas" required>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary-modern py-2 fw-semibold">
                        <i class="bi bi-check-lg me-1"></i> Selesaikan Pendaftaran
                    </button>
                </div>

                <div class="text-center mt-3 small text-secondary">
                    Sudah memiliki akun? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">Masuk ke Sistem</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
