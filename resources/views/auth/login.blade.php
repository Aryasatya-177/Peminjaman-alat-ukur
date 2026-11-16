@extends('layouts.app')

@section('title', 'Masuk - GaugeHub (Sistem Peminjaman & Inspeksi Alat Ukur Presisi)')

@push('styles')
<style>
    /* GaugeHub Precision Auth Styles (Anti-Slop Compliant) */
    .auth-page-wrapper {
        min-height: calc(100vh - 40px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .auth-card-container {
        width: 100%;
        max-width: 1040px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    /* Left Precision Showcase Panel */
    .auth-showcase {
        background: linear-gradient(150deg, #0b1324 0%, #1e3a5f 60%, #142a45 100%);
        position: relative;
        overflow: hidden;
        color: #ffffff;
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 580px;
    }

    /* Precision Technical Arc Watermark (Vector Metrology Lines) */
    .auth-watermark {
        position: absolute;
        top: -40px;
        right: -60px;
        width: 380px;
        height: 380px;
        pointer-events: none;
        opacity: 0.14;
    }

    .auth-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.03em;
        color: #f8fafc;
        backdrop-filter: blur(4px);
    }

    .auth-feature-list {
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        margin-top: 1.75rem;
    }

    .auth-feature-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .auth-feature-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #fef3c7;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .auth-feature-title {
        font-size: 0.88rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 2px;
    }

    .auth-feature-desc {
        font-size: 0.78rem;
        color: #cbd5e1;
        line-height: 1.45;
    }

    /* Right Form Panel */
    .auth-form-panel {
        padding: 3rem 2.75rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: #ffffff;
    }

    .brand-logo-badge {
        width: 42px;
        height: 42px;
        background: var(--primary);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(30, 58, 95, 0.2);
    }

    .form-floating-custom {
        position: relative;
    }

    .form-control-precision {
        border: 1px solid var(--border-dark);
        border-radius: 8px;
        padding: 0.7rem 0.9rem;
        font-size: 0.9rem;
        color: var(--text-primary);
        background-color: #ffffff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
        min-height: 44px;
    }

    .form-control-precision:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.12);
        outline: none;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper .input-icon-left {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 1rem;
        pointer-events: none;
    }

    .input-icon-wrapper input {
        padding-left: 38px;
    }

    .input-icon-wrapper .password-toggle-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: var(--text-secondary);
        padding: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }

    .input-icon-wrapper .password-toggle-btn:hover {
        color: var(--text-primary);
        background: #f1f5f9;
    }

    .input-icon-wrapper .password-toggle-btn:focus-visible {
        outline: 2px solid var(--primary);
        outline-offset: 1px;
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .auth-form-panel {
            padding: 2.25rem 1.5rem;
        }
        .auth-card-container {
            max-width: 520px;
        }
    }
</style>
@endpush

@section('content')
<div class="auth-page-wrapper">
    <div class="auth-card-container">
        <div class="row g-0">
            {{-- ===================== LEFT: BRAND & PRECISION SHOWCASE ===================== --}}
            <div class="col-lg-5 d-none d-lg-flex auth-showcase">
                {{-- Metrology Concentric & Caliper Technical Linework SVG --}}
                <svg class="auth-watermark" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <circle cx="100" cy="100" r="90" stroke="white" stroke-width="1.2" stroke-dasharray="4 4"/>
                    <circle cx="100" cy="100" r="70" stroke="white" stroke-width="1.2"/>
                    <circle cx="100" cy="100" r="50" stroke="white" stroke-width="1"/>
                    <circle cx="100" cy="100" r="30" stroke="white" stroke-width="1.5"/>
                    <path d="M100 10 L100 25 M100 175 L100 190 M10 100 L25 100 M175 100 L190 100" stroke="white" stroke-width="2"/>
                    <path d="M36 36 L47 47 M153 153 L164 164 M36 164 L47 153 M153 36 L164 47" stroke="white" stroke-width="1.2"/>
                    <line x1="100" y1="100" x2="145" y2="70" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="100" cy="100" r="5" fill="#f59e0b"/>
                </svg>

                {{-- Header / Badge --}}
                <div>
                    <div class="auth-badge-pill mb-4">
                        <i class="bi bi-shield-check text-warning"></i>
                        <span>Sistem Kontrol Kualitas & Kalibrasi</span>
                    </div>

                    <h1 class="h2 fw-bold text-white mb-3" style="letter-spacing: -0.02em; line-height: 1.25;">
                        Presisi Terukur,<br>Keandalan Industri.
                    </h1>
                    <p style="color: #cbd5e1; font-size: 0.9rem; line-height: 1.6; margin-bottom: 0;">
                        Platform terpusat peminjaman, tracking stok, dan sertifikasi inspeksi fisik alat ukur presisi bengkel & laboratorium metrologi.
                    </p>
                </div>

                {{-- Feature Highlights --}}
                <div class="auth-feature-list">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div>
                            <div class="auth-feature-title">Dial & Bore Gauge</div>
                            <div class="auth-feature-desc">Pelacakan nomor seri, toleransi mikron, dan ketersediaan fisik.</div>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="bi bi-rulers"></i>
                        </div>
                        <div>
                            <div class="auth-feature-title">Micrometer & Caliper</div>
                            <div class="auth-feature-desc">Pencatatan kondisi sebelum pemakaian dan saat pengembalian.</div>
                        </div>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="bi bi-clipboard2-pulse"></i>
                        </div>
                        <div>
                            <div class="auth-feature-title">Inspeksi Standar Industri</div>
                            <div class="auth-feature-desc">Verifikasi kelayakan teknis langsung oleh teknisi metrologi.</div>
                        </div>
                    </div>
                </div>

                {{-- Footer Info --}}
                <div class="pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.12) !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <span style="font-size: 0.78rem; color: #94a3b8;">&copy; {{ date('Y') }} GaugeHub</span>
                        <span style="font-size: 0.75rem; color: #cbd5e1;">Presisi & Transparansi Alat</span>
                    </div>
                </div>
            </div>

            {{-- ===================== RIGHT: AUTHENTICATION FORM ===================== --}}
            <div class="col-12 col-lg-7 auth-form-panel">
                <div>
                    {{-- Mobile / Top Brand Bar --}}
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="brand-logo-badge">
                                <i class="bi bi-speedometer2 text-warning"></i>
                            </div>
                            <div>
                                <span class="fw-bold fs-5 text-dark" style="letter-spacing: -0.02em;">Gauge<span style="color: #f59e0b;">Hub</span></span>
                                <div class="text-secondary" style="font-size: 0.7rem; font-weight: 500;">Precision Tools System</div>
                            </div>
                        </div>
                        <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.75rem;">Portal Akses</span>
                    </div>

                    {{-- Form Title --}}
                    <div class="mb-4">
                        <h2 class="h3 fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Selamat Datang! 👋</h2>
                        <p class="text-secondary small mb-0">
                            Belum memiliki akun? 
                            <a href="{{ route('register') }}" class="text-decoration-none fw-semibold" style="color: var(--primary);">
                                Daftar akun baru sekarang
                            </a>
                        </p>
                    </div>

                    {{-- Login Form --}}
                    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                        @csrf
                        
                        {{-- Email Input --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-dark mb-1">Alamat Email Kerja</label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-envelope input-icon-left"></i>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control form-control-precision @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       placeholder="nama@perusahaan.com" 
                                       required 
                                       autofocus 
                                       autocomplete="email">
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password Input --}}
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-dark mb-1">Password</label>
                            <div class="input-icon-wrapper">
                                <i class="bi bi-key input-icon-left"></i>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       class="form-control form-control-precision @error('password') is-invalid @enderror" 
                                       placeholder="Masukkan kata sandi akun" 
                                       required 
                                       autocomplete="current-password">
                                <button type="button" 
                                        class="password-toggle-btn" 
                                        id="togglePasswordBtn" 
                                        aria-label="Tampilkan atau sembunyikan kata sandi"
                                        title="Tampilkan / Sembunyikan Password">
                                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Remember Me & Help --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember" checked>
                                <label class="form-check-label small text-secondary" for="remember">
                                    Ingat saya di perangkat ini
                                </label>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary-modern py-2 fw-semibold fs-6" style="min-height: 44px;">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Sistem
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Password visibility toggle
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput && toggleIcon) {
            toggleBtn.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    });
</script>
@endpush
