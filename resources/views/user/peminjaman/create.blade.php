@extends('layouts.app')

@section('title', 'Katalog & Form Peminjaman Alat Ukur - Gauge Hub')

@php
    function getToolCategory($tool) {
        $code = strtoupper($tool->kode_alat);
        $name = strtolower($tool->nama_alat);
        if (str_starts_with($code, 'MIC') || str_contains($name, 'micrometer')) return 'Micrometer';
        if (str_starts_with($code, 'CAL') || str_contains($name, 'caliper')) return 'Caliper';
        if (str_starts_with($code, 'HG') || str_contains($name, 'height gauge')) return 'Height Gauge';
        if (str_starts_with($code, 'DG') || str_starts_with($code, 'BG') || str_starts_with($code, 'DTI') || str_contains($name, 'dial') || str_contains($name, 'bore')) return 'Dial & Bore Gauge';
        if (str_starts_with($code, 'MAG') || str_contains($name, 'stand') || str_contains($name, 'holder')) return 'Stand & Base';
        if (str_starts_with($code, 'GB') || str_starts_with($code, 'PG') || str_starts_with($code, 'FG') || str_contains($name, 'block') || str_contains($name, 'pin') || str_contains($name, 'feeler')) return 'Block & Pin Gauge';
        return 'Lainnya';
    }

    $categories = [
        'Semua' => 'bi-grid-fill',
        'Micrometer' => 'bi-bullseye',
        'Caliper' => 'bi-rulers',
        'Dial & Bore Gauge' => 'bi-speedometer',
        'Height Gauge' => 'bi-arrow-up-circle',
        'Stand & Base' => 'bi-magnet',
        'Block & Pin Gauge' => 'bi-boxes',
    ];
@endphp

@push('styles')
<style>
    .catalog-card {
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        background: #ffffff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .catalog-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
        border-color: var(--border-dark);
    }

    .catalog-card.is-out-of-stock {
        background: #f8fafc !important;
        border-color: #e2e8f0 !important;
        opacity: 0.78;
    }

    .catalog-card.is-out-of-stock:hover {
        transform: none !important;
        box-shadow: none !important;
        border-color: #cbd5e1 !important;
    }

    .catalog-card.is-selected {
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.12);
    }

    .catalog-card.is-selected::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary);
    }

    .category-pill {
        cursor: pointer;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-secondary);
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        user-select: none;
    }

    .category-pill:hover {
        background: #f8fafc;
        color: var(--text-primary);
        border-color: var(--border-dark);
    }

    .category-pill.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }

    .cart-panel {
        position: sticky;
        top: 80px;
        border-radius: 14px;
        border: 1.5px solid var(--border-color);
        background: #ffffff;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .cart-item {
        border-bottom: 1px dashed var(--border-color);
        padding: 12px 0;
        transition: background-color 0.2s;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .qty-stepper {
        display: inline-flex;
        align-items: center;
        background: #f1f5f9;
        border-radius: 8px;
        padding: 2px;
        border: 1px solid #e2e8f0;
    }

    .qty-stepper button {
        width: 28px;
        height: 28px;
        border: none;
        background: #ffffff;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .qty-stepper button:hover:not(:disabled) {
        background: var(--primary);
        color: #ffffff;
    }

    .qty-stepper button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .qty-stepper input {
        width: 38px;
        border: none;
        background: transparent;
        text-align: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--text-primary);
        outline: none;
        -moz-appearance: textfield;
    }

    .qty-stepper input::-webkit-outer-spin-button,
    .qty-stepper input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .search-input-group {
        position: relative;
    }

    .search-input-group i.search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
    }

    .search-input-group .form-control {
        padding-left: 40px;
        padding-right: 36px;
        border-radius: 10px;
        border: 1.5px solid var(--border-color);
        font-size: 0.9rem;
    }

    .search-input-group .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
    }

    .search-input-group .btn-clear-search {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #94a3b8;
        font-size: 0.9rem;
        cursor: pointer;
        display: none;
    }

    .search-input-group .btn-clear-search:hover {
        color: #475569;
    }

    .empty-cart-state {
        text-align: center;
        padding: 36px 16px;
        color: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        {{-- Header Form --}}
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-3 shadow-sm">
                    <i class="bi bi-cart-plus-fill fs-3"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0">Formulir Peminjaman Alat Ukur Presisi</h3>
                    <p class="text-muted small mb-0">Pilih alat ukur yang Anda butuhkan dari katalog interaktif di bawah ini</p>
                </div>
            </div>
            <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Opsi Mode Peminjaman Jika User Memiliki Peminjaman Aktif (Sedang Dipinjam) --}}
        @if(isset($activePeminjamen) && $activePeminjamen->isNotEmpty())
            <div class="card border-primary shadow-sm mb-4" style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); border-left: 5px solid var(--primary) !important; border-radius: 12px;">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary px-2.5 py-1.5 fs-7"><i class="bi bi-layers-fill me-1"></i> Mode Peminjaman</span>
                            <strong class="text-dark fs-6">Anda Memiliki {{ $activePeminjamen->count() }} Transaksi Peminjaman Aktif</strong>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold">
                            <i class="bi bi-check2-circle me-1"></i>Dapat Digabung (1 Proses)
                        </span>
                    </div>
                    <p class="text-secondary small mb-3">
                        Sistem mendukung <strong>Tambahan Peminjaman</strong>. Anda dapat menambahkan alat baru ke transaksi yang sedang aktif agar dihitung dalam <strong>1 proses peminjaman</strong> (dikembalikan dan dicek bersamaan), atau membuka transaksi peminjaman baru terpisah.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border h-100 shadow-sm mode-card-option {{ ($targetPeminjaman || old('peminjaman_id')) ? 'border-primary ring-2 ring-primary' : '' }}" id="cardModeTambahan" style="cursor: pointer;">
                                <div class="form-check ps-0 mb-2">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="radio_mode_peminjaman" id="radioModeTambahan" value="tambahan"
                                        {{ ($targetPeminjaman || old('peminjaman_id') || $activePeminjamen->count() === 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-primary cursor-pointer" for="radioModeTambahan">
                                        <i class="bi bi-plus-circle-dotted me-1"></i> Tambahan Peminjaman (1 Proses Transaksi)
                                    </label>
                                </div>
                                <small class="text-muted d-block ps-4 mb-2">
                                    Alat tambahan akan digabungkan ke transaksi berjalan. Tetap 1 nomor transaksi bukti peminjaman.
                                </small>
                                <div class="ps-4 mt-2">
                                    <label for="selectActivePeminjaman" class="form-label small fw-bold text-dark mb-1">
                                        Pilih Transaksi Aktif:
                                    </label>
                                    <select class="form-select form-select-sm" id="selectActivePeminjaman">
                                        @foreach($activePeminjamen as $ap)
                                            @php
                                                $toolsList = $ap->details->map(fn($d) => ($d->tool->nama_alat ?? 'Alat') . ' (' . $d->qty . ' unit)')->implode(', ');
                                            @endphp
                                            <option value="{{ $ap->id }}"
                                                data-dept="{{ $ap->department }}"
                                                data-pic="{{ $ap->pic }}"
                                                data-pinjam="{{ $ap->tanggal_pinjam->format('Y-m-d') }}"
                                                data-kembali="{{ $ap->tanggal_kembali->format('Y-m-d') }}"
                                                data-tools="{{ $toolsList }}"
                                                {{ ($targetPeminjaman && $targetPeminjaman->id === $ap->id) || old('peminjaman_id') == $ap->id || ($loop->first && !$targetPeminjaman && !old('peminjaman_id')) ? 'selected' : '' }}>
                                                #{{ str_pad($ap->id, 5, '0', STR_PAD_LEFT) }} - {{ $ap->department }} (PIC: {{ $ap->pic }}) - Rencana: {{ $ap->tanggal_kembali->format('d/m/Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border h-100 shadow-sm mode-card-option {{ (!$targetPeminjaman && !old('peminjaman_id') && $activePeminjamen->count() > 1) ? 'border-primary ring-2 ring-primary' : '' }}" id="cardModeBaru" style="cursor: pointer;">
                                <div class="form-check ps-0 mb-2">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="radio_mode_peminjaman" id="radioModeBaru" value="baru"
                                        {{ (!$targetPeminjaman && !old('peminjaman_id') && $activePeminjamen->count() > 1) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="radioModeBaru">
                                        <i class="bi bi-file-earmark-plus me-1"></i> Buat Transaksi Baru Terpisah
                                    </label>
                                </div>
                                <small class="text-muted d-block ps-4">
                                    Membuat nomor transaksi peminjaman baru yang terpisah secara independen.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Utama --}}
        <form action="{{ route('user.pinjam.store') }}" method="POST" id="formPeminjaman">
            @csrf

            {{-- Hidden Field peminjaman_id untuk Mode Tambahan Peminjaman --}}
            <input type="hidden" name="peminjaman_id" id="hiddenPeminjamanId"
                value="{{ $targetPeminjaman ? $targetPeminjaman->id : (old('peminjaman_id') ?? ($activePeminjamen->isNotEmpty() ? $activePeminjamen->first()->id : '')) }}">

            {{-- Alert Ringkasan Mode Tambahan Peminjaman --}}
            <div class="alert alert-info border-2 shadow-sm mb-4 d-none" id="alertModeTambahan">
                <div class="d-flex align-items-start gap-3">
                    <div class="p-2 bg-primary text-white rounded-circle flex-shrink-0 mt-1">
                        <i class="bi bi-layers-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                            <strong class="fs-6 text-primary">
                                MODE TAMBAHAN PEMINJAMAN (1 PROSES TRANSAKSI <span id="badgeTargetTransaksi">#00000</span>)
                            </strong>
                            <span class="badge bg-primary text-white px-2 py-1">Hitungan 1 Peminjaman</span>
                        </div>
                        <div class="small text-dark mb-2">
                            Alat yang Anda pilih pada form ini akan tercatat sebagai <strong>Tambahan Peminjaman</strong> dan dikirimkan ke <strong>Administrator untuk konfirmasi & persetujuan terlebih dahulu</strong> sebelum stok dipotong dan digabung ke transaksi aktif.
                        </div>
                        <div class="p-2 bg-white rounded border small text-secondary" id="boxAlatSebelumnya">
                            <strong class="text-dark"><i class="bi bi-check2-all text-success me-1"></i>Alat pada Peminjaman Pertama:</strong>
                            <span id="textAlatSebelumnya">-</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 1. Kartu Informasi Peminjam & Jadwal Waktu --}}
            <div class="card p-3 mb-4 shadow-sm border-0">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge text-primary me-2"></i>1. Informasi Peminjam & Jadwal Pemakaian
                    </h6>
                    <span class="badge bg-light text-secondary border" id="badgeHeaderWajib">Wajib Diisi</span>
                </div>
                <div class="row g-3">
                    <div class="col-md-3 col-sm-6">
                        <label for="department" class="form-label small fw-semibold text-secondary">
                            Departemen Pemohon <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="department" id="department"
                            class="form-control form-control-sm @error('department') is-invalid @enderror"
                            value="{{ old('department', $user->department) }}"
                            placeholder="Contoh: Quality Control (QC)" required>
                        <small class="text-muted d-none lock-hint" id="deptLockHint" style="font-size: 0.7rem;">
                            <i class="bi bi-lock-fill me-1"></i>Terkunci sesuai transaksi utama
                        </small>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="pic" class="form-label small fw-semibold text-secondary">
                            PIC (Person In Charge) <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="pic" id="pic"
                            class="form-control form-control-sm @error('pic') is-invalid @enderror"
                            value="{{ old('pic', $user->pic ?? $user->name) }}"
                            placeholder="Nama penanggung jawab" required>
                        <small class="text-muted d-none lock-hint" id="picLockHint" style="font-size: 0.7rem;">
                            <i class="bi bi-lock-fill me-1"></i>Terkunci sesuai transaksi utama
                        </small>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="tanggal_pinjam" class="form-label small fw-semibold text-secondary">
                            Tanggal Pinjam <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam"
                            class="form-control form-control-sm @error('tanggal_pinjam') is-invalid @enderror"
                            value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
                        <small class="text-muted d-none lock-hint" id="pinjamLockHint" style="font-size: 0.7rem;">
                            <i class="bi bi-lock-fill me-1"></i>Tanggal pinjam awal
                        </small>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <label for="tanggal_kembali" class="form-label small fw-semibold text-secondary">
                            Rencana Kembali <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="tanggal_kembali" id="tanggal_kembali"
                            class="form-control form-control-sm @error('tanggal_kembali') is-invalid @enderror"
                            value="{{ old('tanggal_kembali', date('Y-m-d', strtotime('+3 days'))) }}" required>
                        <div class="form-text mt-1 text-primary fw-bold" id="durasiText" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i> Durasi: 3 hari
                        </div>
                    </div>

                    {{-- Catatan Peminjaman Pertama / Utama (Khusus Mode Baru) --}}
                    <div class="col-12" id="colCatatanPertama">
                        <label for="catatan_pertama" class="form-label small fw-semibold text-secondary">
                            Catatan / Keterangan Keperluan Peminjaman Pertama (Opsional)
                        </label>
                        <input type="text" name="catatan_pertama" id="catatan_pertama"
                            class="form-control form-control-sm"
                            value="{{ old('catatan_pertama') }}"
                            placeholder="Contoh: Keperluan verifikasi tooling mold dan inspeksi dimensi line A">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            Keterangan peminjaman pertama/utama ini akan dicatat dan dipisahkan dari tambahan peminjaman.
                        </div>
                    </div>

                    {{-- Catatan Tambahan (Khusus Mode Tambahan Peminjaman) --}}
                    <div class="col-12 d-none" id="colCatatanTambahan">
                        <label for="catatan_tambahan" class="form-label small fw-semibold text-secondary">
                            Catatan / Keterangan Keperluan Tambahan Alat (Opsional)
                        </label>
                        <input type="text" name="catatan_tambahan" id="catatan_tambahan"
                            class="form-control form-control-sm"
                            value="{{ old('catatan_tambahan') }}"
                            placeholder="Contoh: Kebutuhan tambahan alat ukur untuk verifikasi batch 2 tooling">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                            Catatan ini khusus untuk tambahan alat dan akan ditampilkan terpisah dari peminjaman pertama pada bukti tanda terima.
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Dua Kolom: Katalog Alat (Kiri) & Keranjang Peminjaman (Kanan) --}}
            <div class="row g-4">
                {{-- Kolom Kiri: Katalog Alat Ukur --}}
                <div class="col-lg-7 col-xl-8">
                    <div class="card p-3 shadow-sm border-0 h-100">
                        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-1">
                                    <i class="bi bi-tools text-primary me-2"></i>2. Pilih Alat Ukur Presisi
                                </h6>
                                <p class="text-muted small mb-0">Klik <strong>+ Tambah</strong> pada alat yang ingin dipinjam, kuantitas dapat diatur langsung</p>
                            </div>
                            <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1" id="toolCounterBadge">
                                {{ $tools->count() }} Alat Tersedia
                            </span>
                        </div>

                        {{-- Search Bar --}}
                        <div class="search-input-group mb-3">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" id="toolSearchInput" class="form-control"
                                placeholder="Cari nama alat, kode, atau spesifikasi (contoh: Micrometer, Caliper, 0-25mm)...">
                            <button type="button" class="btn-clear-search" id="btnClearSearch">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                        </div>

                        {{-- Category Filter Pills --}}
                        <div class="d-flex gap-2 overflow-auto pb-2 mb-3" style="scrollbar-width: thin;">
                            @foreach($categories as $categoryName => $icon)
                                <button type="button" class="category-pill {{ $loop->first ? 'active' : '' }}"
                                    data-category="{{ $categoryName }}">
                                    <i class="bi {{ $icon }}"></i>
                                    <span>{{ $categoryName }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Grid Kartu Alat --}}
                        @if($tools->isEmpty())
                            <div class="alert alert-warning my-4">
                                <i class="bi bi-exclamation-triangle me-2"></i> Saat ini tidak ada alat ukur dengan stok tersedia untuk dipinjam.
                            </div>
                        @else
                            <div class="row g-3" id="toolGridContainer">
                                @foreach($tools as $tool)
                                    @php
                                        $cat = getToolCategory($tool);
                                    @endphp
                                    <div class="col-md-6 tool-card-wrapper"
                                        data-category="{{ $cat }}"
                                        data-name="{{ strtolower($tool->nama_alat) }}"
                                        data-code="{{ strtolower($tool->kode_alat) }}"
                                        data-tool-id="{{ $tool->id }}">
                                        <div class="catalog-card p-3 {{ $tool->available_stock <= 0 ? 'is-out-of-stock' : '' }}" id="cardTool_{{ $tool->id }}">
                                            {{-- Top Card: Code & Stock Badge --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-light text-primary border fw-bold" style="font-size: 0.75rem;">
                                                    {{ $tool->kode_alat }}
                                                </span>
                                                @if($tool->available_stock > 3)
                                                    <span class="badge bg-success-subtle text-success fw-semibold" style="font-size: 0.72rem;">
                                                        <i class="bi bi-check2-circle me-1"></i>Tersedia: {{ $tool->available_stock }}
                                                    </span>
                                                @elseif($tool->available_stock > 0)
                                                    <span class="badge bg-warning-subtle text-warning fw-semibold" style="font-size: 0.72rem;">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Sisa: {{ $tool->available_stock }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger fw-bold border border-danger-subtle" style="font-size: 0.72rem;">
                                                        <i class="bi bi-x-circle me-1"></i>Stok: 0 (Habis)
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Middle: Tool Name & Category --}}
                                            <div class="mb-3 flex-grow-1">
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.9rem; line-height: 1.35;">
                                                    {{ $tool->nama_alat }}
                                                </h6>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="bi bi-tag me-1"></i>{{ $cat }}
                                                </small>
                                            </div>

                                            {{-- Bottom: Action Button / Counter --}}
                                            <div class="card-action-container mt-auto">
                                                @if($tool->available_stock <= 0)
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm w-100 fw-semibold rounded-2"
                                                        disabled
                                                        style="background-color: #f1f5f9; border-color: #e2e8f0; color: #94a3b8; cursor: not-allowed;">
                                                        <i class="bi bi-slash-circle me-1"></i> Stok Habis (0)
                                                    </button>
                                                @else
                                                    {{-- Tombol Tambah Pertama Kali --}}
                                                    <button type="button"
                                                        class="btn btn-outline-primary btn-sm w-100 fw-semibold rounded-2 btn-add-tool"
                                                        id="btnAdd_{{ $tool->id }}"
                                                        data-id="{{ $tool->id }}"
                                                        data-name="{{ $tool->nama_alat }}"
                                                        data-code="{{ $tool->kode_alat }}"
                                                        data-stock="{{ $tool->available_stock }}"
                                                        data-category="{{ $cat }}">
                                                        <i class="bi bi-plus-lg me-1"></i> Tambah ke Pinjaman
                                                    </button>

                                                    {{-- Counter Stepper (Muncul jika alat terpilih) --}}
                                                    <div class="d-none justify-content-between align-items-center bg-light p-1 rounded-2 counter-wrapper"
                                                        id="counter_{{ $tool->id }}">
                                                        <small class="text-primary fw-bold ms-2" style="font-size: 0.75rem;">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Terpilih
                                                        </small>
                                                        <div class="qty-stepper">
                                                            <button type="button" class="btn-step-minus" data-id="{{ $tool->id }}">-</button>
                                                            <input type="number" class="input-card-qty" id="inputQty_{{ $tool->id }}"
                                                                value="1" min="1" max="{{ $tool->available_stock }}"
                                                                data-id="{{ $tool->id }}" readonly>
                                                            <button type="button" class="btn-step-plus" data-id="{{ $tool->id }}"
                                                                {{ $tool->available_stock <= 1 ? 'disabled' : '' }}>+</button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- No Search Result Alert --}}
                            <div class="empty-state d-none py-5 text-center" id="emptySearchState">
                                <i class="bi bi-search display-6 text-muted mb-2 d-block"></i>
                                <h6 class="fw-bold text-secondary mb-1">Alat tidak ditemukan</h6>
                                <p class="text-muted small mb-0">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kolom Kanan: Keranjang Ringkasan Peminjaman --}}
                <div class="col-lg-5 col-xl-4">
                    <div class="cart-panel p-3">
                        <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-cart3 text-primary me-2"></i>Daftar Alat Dipinjam
                                </h6>
                                <small class="text-muted" id="cartCountLabel">0 jenis alat dipilih</small>
                            </div>
                            <button type="button" class="btn btn-link text-danger text-decoration-none p-0 small fw-semibold d-none"
                                id="btnClearCart" style="font-size: 0.75rem;">
                                Kosongkan
                            </button>
                        </div>

                        {{-- Hidden inputs container for form submission --}}
                        <div id="hiddenInputsContainer"></div>

                        {{-- Empty Cart Placeholder --}}
                        <div class="empty-cart-state" id="emptyCartMessage">
                            <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <div class="fw-semibold text-secondary mb-1" style="font-size: 0.85rem;">Belum ada alat yang dipilih</div>
                            <small class="text-muted d-block">Klik <strong>+ Tambah</strong> pada alat ukur di katalog sebelah kiri untuk mulai meminjam.</small>
                        </div>

                        {{-- List Item Cart --}}
                        <div class="cart-items-container" id="cartItemsList" style="max-height: 320px; overflow-y: auto;">
                            {{-- Item dimasukkan secara dinamis via JavaScript --}}
                        </div>

                        {{-- Summary Box --}}
                        <div class="bg-light rounded-3 p-3 mt-3 border d-none" id="cartSummaryBox">
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Total Jenis Alat:</span>
                                <span class="fw-bold text-dark" id="summaryTotalTypes">0 jenis</span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary mb-1">
                                <span>Total Unit Fisik:</span>
                                <span class="fw-bold text-primary" id="summaryTotalUnits">0 unit</span>
                            </div>
                            <div class="d-flex justify-content-between small text-secondary">
                                <span>Estimasi Durasi:</span>
                                <span class="fw-bold text-dark" id="summaryDuration">3 hari</span>
                            </div>
                        </div>

                        {{-- SOP Alert --}}
                        <div class="alert alert-light border p-2 rounded-2 mt-3 mb-3" style="font-size: 0.75rem;">
                            <div class="fw-bold text-dark mb-1">
                                <i class="bi bi-shield-check text-success me-1"></i> SOP Pemakaian:
                            </div>
                            <span class="text-muted">
                                Alat presisi telah terkalibrasi. Jaga dari benturan keras & simpan di kotak pelindung saat mobilisasi.
                            </span>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="btn btn-primary-modern w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            id="btnSubmitLoan" disabled>
                            <i class="bi bi-send-check"></i>
                            <span id="submitBtnText">Pilih Alat Terlebih Dahulu</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // State Keranjang Peminjaman
    // Map: toolId -> { id, name, code, stock, qty }
    const cart = new Map();

    // DOM Elements
    const tglPinjam = document.getElementById('tanggal_pinjam');
    const tglKembali = document.getElementById('tanggal_kembali');
    const durasiText = document.getElementById('durasiText');
    const summaryDuration = document.getElementById('summaryDuration');

    const searchInput = document.getElementById('toolSearchInput');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const categoryPills = document.querySelectorAll('.category-pill');
    const toolCards = document.querySelectorAll('.tool-card-wrapper');
    const emptySearchState = document.getElementById('emptySearchState');
    const toolCounterBadge = document.getElementById('toolCounterBadge');

    const cartItemsList = document.getElementById('cartItemsList');
    const emptyCartMessage = document.getElementById('emptyCartMessage');
    const cartSummaryBox = document.getElementById('cartSummaryBox');
    const cartCountLabel = document.getElementById('cartCountLabel');
    const btnClearCart = document.getElementById('btnClearCart');
    const btnSubmitLoan = document.getElementById('btnSubmitLoan');
    const submitBtnText = document.getElementById('submitBtnText');
    const summaryTotalTypes = document.getElementById('summaryTotalTypes');
    const summaryTotalUnits = document.getElementById('summaryTotalUnits');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    const formPeminjaman = document.getElementById('formPeminjaman');

    let currentCategory = 'Semua';
    let currentSearch = '';

    // ==========================================
    // 1. Perhitungan Durasi Hari
    // ==========================================
    function hitungDurasi() {
        if (!tglPinjam.value || !tglKembali.value) return 3;
        const start = new Date(tglPinjam.value);
        const end = new Date(tglKembali.value);
        const diffTime = end - start;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays < 0) {
            durasiText.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Tanggal kembali tidak boleh sebelum tanggal pinjam!</span>';
            if (summaryDuration) summaryDuration.textContent = 'Tidak Valid';
            return -1;
        } else if (diffDays === 0) {
            durasiText.innerHTML = '<span class="text-warning"><i class="bi bi-clock me-1"></i> Durasi: 1 hari (hari yang sama)</span>';
            if (summaryDuration) summaryDuration.textContent = '1 hari';
            return 1;
        } else {
            durasiText.innerHTML = `<span class="text-primary"><i class="bi bi-clock me-1"></i> Durasi: ${diffDays} hari</span>`;
            if (summaryDuration) summaryDuration.textContent = `${diffDays} hari`;
            return diffDays;
        }
    }

    tglPinjam.addEventListener('change', function () {
        tglKembali.min = tglPinjam.value;
        hitungDurasi();
    });
    tglKembali.addEventListener('change', hitungDurasi);
    hitungDurasi();

    // ==========================================
    // 2. Filter & Pencarian Alat
    // ==========================================
    function filterTools() {
        let visibleCount = 0;
        const query = currentSearch.toLowerCase().trim();

        toolCards.forEach(wrapper => {
            const name = wrapper.getAttribute('data-name');
            const code = wrapper.getAttribute('data-code');
            const category = wrapper.getAttribute('data-category');

            const matchesCategory = (currentCategory === 'Semua' || category === currentCategory);
            const matchesSearch = (!query || name.includes(query) || code.includes(query) || category.toLowerCase().includes(query));

            if (matchesCategory && matchesSearch) {
                wrapper.classList.remove('d-none');
                visibleCount++;
            } else {
                wrapper.classList.add('d-none');
            }
        });

        if (visibleCount === 0) {
            emptySearchState.classList.remove('d-none');
        } else {
            emptySearchState.classList.add('d-none');
        }

        toolCounterBadge.textContent = `${visibleCount} Alat Tampil`;
    }

    // Event Search
    searchInput.addEventListener('input', function () {
        currentSearch = this.value;
        btnClearSearch.style.display = currentSearch ? 'block' : 'none';
        filterTools();
    });

    btnClearSearch.addEventListener('click', function () {
        searchInput.value = '';
        currentSearch = '';
        this.style.display = 'none';
        filterTools();
        searchInput.focus();
    });

    // Event Category Pills
    categoryPills.forEach(pill => {
        pill.addEventListener('click', function () {
            categoryPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');
            filterTools();
        });
    });

    // ==========================================
    // 3. Manajemen Keranjang (Cart)
    // ==========================================
    function updateCartUI() {
        cartItemsList.innerHTML = '';
        hiddenInputsContainer.innerHTML = '';

        let totalUnits = 0;
        const totalTypes = cart.size;

        if (totalTypes === 0) {
            emptyCartMessage.classList.remove('d-none');
            cartSummaryBox.classList.add('d-none');
            btnClearCart.classList.add('d-none');
            cartCountLabel.textContent = '0 jenis alat dipilih';
            btnSubmitLoan.disabled = true;
            submitBtnText.textContent = 'Pilih Alat Terlebih Dahulu';
        } else {
            emptyCartMessage.classList.add('d-none');
            cartSummaryBox.classList.remove('d-none');
            btnClearCart.classList.remove('d-none');
            cartCountLabel.textContent = `${totalTypes} jenis alat dipilih`;
            btnSubmitLoan.disabled = false;

            cart.forEach((item, toolId) => {
                totalUnits += item.qty;

                // Hidden Inputs for Laravel Request (tool_id[] & qty[])
                const inputTool = document.createElement('input');
                inputTool.type = 'hidden';
                inputTool.name = 'tool_id[]';
                inputTool.value = item.id;
                hiddenInputsContainer.appendChild(inputTool);

                const inputQty = document.createElement('input');
                inputQty.type = 'hidden';
                inputQty.name = 'qty[]';
                inputQty.value = item.qty;
                hiddenInputsContainer.appendChild(inputQty);

                // Render item di Cart List
                const itemEl = document.createElement('div');
                itemEl.className = 'cart-item';
                itemEl.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div style="max-width: 68%;">
                            <div class="badge bg-light text-primary border mb-1" style="font-size: 0.7rem;">${item.code}</div>
                            <div class="fw-semibold text-dark text-truncate" style="font-size: 0.82rem;" title="${item.name}">${item.name}</div>
                            <small class="text-muted" style="font-size: 0.7rem;">Maksimal: ${item.stock} unit</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 btn-remove-item" data-id="${item.id}" title="Hapus dari daftar">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-secondary" style="font-size: 0.75rem;">Kuantitas Pinjam:</span>
                        <div class="qty-stepper">
                            <button type="button" class="btn-step-minus" data-id="${item.id}">-</button>
                            <input type="number" value="${item.qty}" min="1" max="${item.stock}" readonly>
                            <button type="button" class="btn-step-plus" data-id="${item.id}" ${item.qty >= item.stock ? 'disabled' : ''}>+</button>
                        </div>
                    </div>
                `;
                cartItemsList.appendChild(itemEl);
            });

            submitBtnText.textContent = `Ajukan Peminjaman (${totalUnits} Unit)`;
        }

        summaryTotalTypes.textContent = `${totalTypes} jenis`;
        summaryTotalUnits.textContent = `${totalUnits} unit`;

        // Update tampilan kartu di katalog (sinkronkan state)
        toolCards.forEach(wrapper => {
            const toolId = parseInt(wrapper.getAttribute('data-tool-id'));
            const card = document.getElementById(`cardTool_${toolId}`);
            const btnAdd = document.getElementById(`btnAdd_${toolId}`);
            const counter = document.getElementById(`counter_${toolId}`);
            const inputCardQty = document.getElementById(`inputQty_${toolId}`);

            if (cart.has(toolId)) {
                const item = cart.get(toolId);
                if (card) card.classList.add('is-selected');
                if (btnAdd) btnAdd.classList.add('d-none');
                if (counter) {
                    counter.classList.remove('d-none');
                    counter.classList.add('d-flex');
                }
                if (inputCardQty) inputCardQty.value = item.qty;

                const plusBtn = counter ? counter.querySelector('.btn-step-plus') : null;
                if (plusBtn) plusBtn.disabled = (item.qty >= item.stock);
            } else {
                if (card) card.classList.remove('is-selected');
                if (btnAdd) btnAdd.classList.remove('d-none');
                if (counter) {
                    counter.classList.add('d-none');
                    counter.classList.remove('d-flex');
                }
                if (inputCardQty) inputCardQty.value = 1;
            }
        });
    }

    // Tambah alat ke keranjang
    document.addEventListener('click', function (e) {
        const addBtn = e.target.closest('.btn-add-tool');
        if (addBtn) {
            const id = parseInt(addBtn.getAttribute('data-id'));
            const name = addBtn.getAttribute('data-name');
            const code = addBtn.getAttribute('data-code');
            const stock = parseInt(addBtn.getAttribute('data-stock') || 1);

            if (stock > 0) {
                cart.set(id, { id, name, code, stock, qty: 1 });
                updateCartUI();
            }
        }
    });

    // Event Tambah (+) Qty
    document.addEventListener('click', function (e) {
        const plusBtn = e.target.closest('.btn-step-plus');
        if (plusBtn) {
            const id = parseInt(plusBtn.getAttribute('data-id'));
            if (cart.has(id)) {
                const item = cart.get(id);
                if (item.qty < item.stock) {
                    item.qty++;
                    updateCartUI();
                }
            }
        }
    });

    // Event Kurang (-) Qty
    document.addEventListener('click', function (e) {
        const minusBtn = e.target.closest('.btn-step-minus');
        if (minusBtn) {
            const id = parseInt(minusBtn.getAttribute('data-id'));
            if (cart.has(id)) {
                const item = cart.get(id);
                if (item.qty > 1) {
                    item.qty--;
                    updateCartUI();
                } else {
                    // Jika dikurangi dari 1, hapus dari keranjang
                    cart.delete(id);
                    updateCartUI();
                }
            }
        }
    });

    // Event Hapus Item dari keranjang
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.btn-remove-item');
        if (removeBtn) {
            const id = parseInt(removeBtn.getAttribute('data-id'));
            cart.delete(id);
            updateCartUI();
        }
    });

    // Event Kosongkan Keranjang
    btnClearCart.addEventListener('click', function () {
        if (confirm('Apakah Anda yakin ingin mengosongkan seluruh pilihan alat?')) {
            cart.clear();
            updateCartUI();
        }
    });

    // DOM Elements untuk Mode Tambahan Peminjaman
    const radioModeTambahan = document.getElementById('radioModeTambahan');
    const radioModeBaru = document.getElementById('radioModeBaru');
    const selectActivePeminjaman = document.getElementById('selectActivePeminjaman');
    const hiddenPeminjamanId = document.getElementById('hiddenPeminjamanId');
    const alertModeTambahan = document.getElementById('alertModeTambahan');
    const badgeTargetTransaksi = document.getElementById('badgeTargetTransaksi');
    const textAlatSebelumnya = document.getElementById('textAlatSebelumnya');
    const colCatatanPertama = document.getElementById('colCatatanPertama');
    const colCatatanTambahan = document.getElementById('colCatatanTambahan');
    const lockHints = document.querySelectorAll('.lock-hint');
    const deptInput = document.getElementById('department');
    const picInput = document.getElementById('pic');
    const cardModeTambahan = document.getElementById('cardModeTambahan');
    const cardModeBaru = document.getElementById('cardModeBaru');

    // Default user original values
    const defaultDept = deptInput ? deptInput.value : '';
    const defaultPic = picInput ? picInput.value : '';

    function isTambahanMode() {
        return radioModeTambahan && radioModeTambahan.checked;
    }

    function applyModeUI() {
        if (!radioModeTambahan || !radioModeBaru) return;

        if (radioModeTambahan.checked) {
            const selectedOpt = selectActivePeminjaman ? selectActivePeminjaman.selectedOptions[0] : null;
            if (selectedOpt) {
                const targetId = selectedOpt.value;
                const dept = selectedOpt.getAttribute('data-dept');
                const pic = selectedOpt.getAttribute('data-pic');
                const tglP = selectedOpt.getAttribute('data-pinjam');
                const tglK = selectedOpt.getAttribute('data-kembali');
                const tools = selectedOpt.getAttribute('data-tools');

                if (hiddenPeminjamanId) hiddenPeminjamanId.value = targetId;
                if (badgeTargetTransaksi) badgeTargetTransaksi.textContent = '#' + targetId.toString().padStart(5, '0');
                if (textAlatSebelumnya) textAlatSebelumnya.textContent = tools || 'Belum ada data';

                if (deptInput) {
                    deptInput.value = dept;
                    deptInput.readOnly = true;
                }
                if (picInput) {
                    picInput.value = pic;
                    picInput.readOnly = true;
                }
                if (tglPinjam) {
                    tglPinjam.value = tglP;
                    tglPinjam.readOnly = true;
                }
                if (tglKembali && !tglKembali.dataset.userEdited) {
                    tglKembali.value = tglK;
                    tglKembali.min = tglP;
                }

                if (alertModeTambahan) alertModeTambahan.classList.remove('d-none');
                if (colCatatanTambahan) colCatatanTambahan.classList.remove('d-none');
                if (colCatatanPertama) colCatatanPertama.classList.add('d-none');
                lockHints.forEach(el => el.classList.remove('d-none'));

                if (cardModeTambahan) {
                    cardModeTambahan.classList.add('border-primary');
                    cardModeTambahan.style.boxShadow = '0 0 0 2px rgba(30, 58, 95, 0.2)';
                }
                if (cardModeBaru) {
                    cardModeBaru.classList.remove('border-primary');
                    cardModeBaru.style.boxShadow = 'none';
                }
            }
        } else {
            // Mode Buat Transaksi Baru
            if (hiddenPeminjamanId) hiddenPeminjamanId.value = '';
            if (alertModeTambahan) alertModeTambahan.classList.add('d-none');
            if (colCatatanTambahan) colCatatanTambahan.classList.add('d-none');
            if (colCatatanPertama) colCatatanPertama.classList.remove('d-none');
            lockHints.forEach(el => el.classList.add('d-none'));

            if (deptInput) {
                deptInput.readOnly = false;
                deptInput.value = defaultDept;
            }
            if (picInput) {
                picInput.readOnly = false;
                picInput.value = defaultPic;
            }
            if (tglPinjam) {
                tglPinjam.readOnly = false;
                tglPinjam.value = new Date().toISOString().split('T')[0];
            }

            if (cardModeTambahan) {
                cardModeTambahan.classList.remove('border-primary');
                cardModeTambahan.style.boxShadow = 'none';
            }
            if (cardModeBaru) {
                cardModeBaru.classList.add('border-primary');
                cardModeBaru.style.boxShadow = '0 0 0 2px rgba(30, 58, 95, 0.2)';
            }
        }

        hitungDurasi();
        updateCartSubmitBtn();
    }

    if (radioModeTambahan) {
        radioModeTambahan.addEventListener('change', applyModeUI);
    }
    if (radioModeBaru) {
        radioModeBaru.addEventListener('change', applyModeUI);
    }
    if (selectActivePeminjaman) {
        selectActivePeminjaman.addEventListener('change', function () {
            if (radioModeTambahan) {
                radioModeTambahan.checked = true;
            }
            applyModeUI();
        });
    }

    if (cardModeTambahan) {
        cardModeTambahan.addEventListener('click', function (e) {
            if (e.target.tagName !== 'SELECT' && e.target.tagName !== 'OPTION') {
                radioModeTambahan.checked = true;
                applyModeUI();
            }
        });
    }

    if (cardModeBaru) {
        cardModeBaru.addEventListener('click', function () {
            radioModeBaru.checked = true;
            applyModeUI();
        });
    }

    function updateCartSubmitBtn() {
        if (!btnSubmitLoan || !submitBtnText) return;
        const totalUnits = Array.from(cart.values()).reduce((sum, item) => sum + item.qty, 0);

        if (cart.size === 0) {
            btnSubmitLoan.disabled = true;
            submitBtnText.textContent = 'Pilih Alat Terlebih Dahulu';
        } else {
            btnSubmitLoan.disabled = false;
            if (isTambahanMode() && hiddenPeminjamanId && hiddenPeminjamanId.value) {
                const targetId = hiddenPeminjamanId.value.toString().padStart(5, '0');
                submitBtnText.textContent = `Ajukan Tambahan ke Admin #${targetId} (${totalUnits} Unit)`;
            } else {
                submitBtnText.textContent = `Ajukan Peminjaman (${totalUnits} Unit)`;
            }
        }
    }

    // Hook updateCartUI to also update dynamic submit button
    const originalUpdateCartUI = updateCartUI;
    updateCartUI = function () {
        originalUpdateCartUI();
        updateCartSubmitBtn();
    };

    // Validasi sebelum submit form
    formPeminjaman.addEventListener('submit', function (e) {
        if (cart.size === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal satu alat ukur dari katalog sebelum mengajukan peminjaman.');
            return false;
        }

        const duration = hitungDurasi();
        if (duration < 0) {
            e.preventDefault();
            alert('Tanggal pengembalian tidak boleh sebelum tanggal peminjaman!');
            return false;
        }
    });

    // Inisialisasi awal
    applyModeUI();
    filterTools();
    updateCartUI();
});
</script>
@endpush