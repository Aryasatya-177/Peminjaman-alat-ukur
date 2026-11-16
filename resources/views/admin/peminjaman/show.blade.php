@extends('layouts.app')

@section('title', 'Detail Monitoring Transaksi #' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) . ' - Admin')
@section('page_title', 'Detail Peminjaman')

@push('styles')
<style>
    .receipt-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }
    .receipt-header-strip {
        height: 4px;
        background: var(--primary);
    }
    /* Stepper Progress Bar */
    .stepper-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 24px;
    }
    .stepper-wrapper::before {
        content: '';
        position: absolute;
        top: 18px;
        left: 20px;
        right: 20px;
        height: 3px;
        background: #e2e8f0;
        z-index: 1;
    }
    .stepper-item {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex: 1;
    }
    .stepper-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 6px;
        transition: all 0.2s ease;
    }
    .stepper-item.completed .stepper-circle {
        background: var(--status-ready);
        border-color: var(--status-ready);
        color: #ffffff;
    }
    .stepper-item.active .stepper-circle {
        background: var(--primary);
        border-color: var(--primary);
        color: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.2);
    }
    .stepper-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .stepper-item.completed .stepper-label,
    .stepper-item.active .stepper-label {
        color: var(--text-primary);
        font-weight: 700;
    }
    .qc-stamp {
        border: 2px dashed #059669;
        color: #059669;
        border-radius: 8px;
        padding: 6px 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    @media print {
        .no-print, .btn, .stepper-wrapper { display: none !important; }
        .receipt-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        body { background: white !important; }
        .signature-block { page-break-inside: avoid; }
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="receipt-card">
                <div class="receipt-header-strip no-print"></div>

                <div class="p-4 p-md-5">
                    {{-- Header Monitoring --}}
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-primary px-3 py-1 fw-semibold">
                                    <i class="bi bi-shield-check me-1"></i> MONITORING ADMIN TOOL CRIB
                                </span>
                                <div class="qc-stamp">
                                    <i class="bi bi-patch-check-fill"></i> QC & ASSET CONTROL VERIFIED
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                Transaksi #{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill no-print" onclick="copyTransactionId('{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}')" title="Salin ID Transaksi">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </h2>
                            <div class="text-muted small">
                                <i class="bi bi-clock me-1"></i> Diajukan pada: {{ $peminjaman->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </div>
                        </div>

                        <div class="text-end no-print">
                            <div class="mb-2">
                                @if($peminjaman->status === 'dipinjam')
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                        <i class="bi bi-clock me-1"></i> Sedang Dipinjam
                                    </span>
                                @elseif($peminjaman->status === 'menunggu_cek_teknisi')
                                    <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu Cek Teknisi
                                    </span>
                                @elseif($peminjaman->status === 'dalam_perbaikan')
                                    <span class="badge bg-primary fs-6 px-3 py-2">
                                        <i class="bi bi-wrench me-1"></i> Dalam Perbaikan Bengkel
                                    </span>
                                @elseif($peminjaman->status === 'menunggu_konfirmasi')
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="bi bi-shield-exclamation me-1"></i> Menunggu Konfirmasi Kerusakan
                                    </span>
                                @elseif($peminjaman->status === 'rusak_dikonfirmasi')
                                    <span class="badge bg-secondary fs-6 px-3 py-2">
                                        <i class="bi bi-clipboard2-x me-1"></i> Rusak (Dikonfirmasi)
                                    </span>
                                @elseif($peminjaman->status === 'selesai')
                                    <span class="badge bg-success fs-6 px-3 py-2">
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai (Kondisi Baik)
                                    </span>
                                @elseif($peminjaman->status === 'rusak')
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Selesai (Ada Kerusakan)
                                    </span>
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                                <i class="bi bi-printer me-1"></i> Cetak Bukti Dokumen
                            </button>
                        </div>
                    </div>

                    {{-- Stepper Progress Milestone Tracker (no-print) --}}
                    @php
                        $st = $peminjaman->status;
                        $step1 = true;
                        $step2 = in_array($st, ['dipinjam', 'menunggu_cek_teknisi', 'dalam_perbaikan', 'menunggu_konfirmasi', 'rusak_dikonfirmasi', 'selesai', 'rusak']);
                        $step3 = in_array($st, ['menunggu_cek_teknisi', 'dalam_perbaikan', 'menunggu_konfirmasi', 'rusak_dikonfirmasi', 'selesai', 'rusak']);
                        $step4 = in_array($st, ['dalam_perbaikan', 'menunggu_konfirmasi', 'rusak_dikonfirmasi', 'selesai', 'rusak']);
                        $step5 = in_array($st, ['selesai', 'rusak', 'rusak_dikonfirmasi']);
                    @endphp
                    <div class="stepper-wrapper no-print mb-4 pt-2">
                        <div class="stepper-item {{ $st === 'dipinjam' ? 'active' : ($step2 ? 'completed' : '') }}">
                            <div class="stepper-circle">{!! $step2 && $st !== 'dipinjam' ? '<i class="bi bi-check-lg"></i>' : '1' !!}</div>
                            <div class="stepper-label">Dipinjam</div>
                        </div>
                        <div class="stepper-item {{ $st === 'menunggu_cek_teknisi' ? 'active' : ($step3 ? 'completed' : '') }}">
                            <div class="stepper-circle">{!! $step3 && $st !== 'menunggu_cek_teknisi' ? '<i class="bi bi-check-lg"></i>' : '2' !!}</div>
                            <div class="stepper-label">Pengembalian</div>
                        </div>
                        <div class="stepper-item {{ in_array($st, ['dalam_perbaikan', 'menunggu_konfirmasi']) ? 'active' : ($step4 ? 'completed' : '') }}">
                            <div class="stepper-circle">{!! $step4 && !in_array($st, ['dalam_perbaikan', 'menunggu_konfirmasi']) ? '<i class="bi bi-check-lg"></i>' : '3' !!}</div>
                            <div class="stepper-label">Uji Kalibrasi</div>
                        </div>
                        <div class="stepper-item {{ $step5 ? 'completed active' : '' }}">
                            <div class="stepper-circle">{!! $step5 ? '<i class="bi bi-check-lg"></i>' : '4' !!}</div>
                            <div class="stepper-label">Selesai</div>
                        </div>
                    </div>

                    {{-- Informasi Peminjam & Jadwal Durasi --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="text-secondary small fw-bold mb-2 text-uppercase d-flex align-items-center gap-1">
                                    <i class="bi bi-person-vcard text-primary"></i> Informasi Peminjam
                                </div>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Departemen:</td>
                                        <td class="fw-bold text-primary">{{ $peminjaman->department }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">PIC Pengembali:</td>
                                        <td class="fw-semibold text-dark">{{ $peminjaman->pic }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Akun Pemohon:</td>
                                        <td class="text-dark">{{ $peminjaman->user->name ?? '-' }} ({{ $peminjaman->user->email ?? '-' }})</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="text-secondary small fw-bold mb-2 text-uppercase d-flex align-items-center gap-1">
                                    <i class="bi bi-calendar-range text-primary"></i> Jadwal Durasi
                                </div>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Tanggal Pinjam:</td>
                                        <td class="fw-semibold text-success">
                                            <i class="bi bi-calendar-check me-1"></i>
                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Batas Pengembalian:</td>
                                        <td class="fw-semibold text-danger">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Durasi Pinjam:</td>
                                        <td class="fw-bold text-dark">
                                            {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($peminjaman->tanggal_kembali)) + 1 }}
                                            Hari Kalender
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- SEKSI PERPANJANGAN DURASI PEMINJAMAN ALAT --}}
                    @if($peminjaman->hasPendingPerpanjangan())
                        @php
                            $pendingPerpanjangan = $peminjaman->perpanjangans->firstWhere('status', 'menunggu');
                        @endphp
                        @if($pendingPerpanjangan)
                            <div class="card border-warning mb-4 shadow-sm rounded-3 overflow-hidden" style="border-color: #f59e0b !important;">
                                <div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fffbeb; border-bottom: 1px solid #fde68a;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark border border-warning fw-bold px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i> Permohonan Perpanjangan Menunggu Konfirmasi Admin
                                        </span>
                                    </div>
                                    <span class="small text-muted">
                                        Diajukan pada: {{ $pendingPerpanjangan->created_at->translatedFormat('d F Y, H:i') }} WIB ({{ $pendingPerpanjangan->created_at->diffForHumans() }})
                                    </span>
                                </div>
                                <div class="card-body p-3 p-md-4">
                                    <div class="row g-3 align-items-center mb-3">
                                        <div class="col-md-4">
                                            <div class="p-2.5 rounded bg-light border text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Batas Waktu Saat Ini</small>
                                                <strong class="text-dark font-monospace fs-6">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_lama)->translatedFormat('d M Y') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2.5 rounded border text-center" style="background: #eff6ff; border-color: #bfdbfe !important;">
                                                <small class="text-primary d-block text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Usulan Batas Baru</small>
                                                <strong class="text-primary font-monospace fs-6">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_baru)->translatedFormat('d M Y') }}</strong>
                                                <span class="badge bg-primary text-white ms-1" style="font-size: 0.68rem;">+{{ $pendingPerpanjangan->tambahanHari() }} Hari</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-2.5 rounded bg-light border text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.7rem; font-weight: 700;">Pemohon / PIC</small>
                                                <strong class="text-dark fs-6">{{ $peminjaman->pic }}</strong>
                                                <div class="small text-muted" style="font-size: 0.72rem;">{{ $peminjaman->department }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Rincian Alat Tambahan jika Ada --}}
                                    @if($pendingPerpanjangan->hasAlatTambahan())
                                        <div class="p-3 rounded mb-3" style="background: #faf5ff; border: 1px solid #d8b4fe;">
                                            <strong class="text-purple d-block small text-uppercase mb-2" style="color: #7e22ce; font-size: 0.73rem; letter-spacing: 0.5px;">
                                                <i class="bi bi-cart-plus me-1"></i>Permintaan Alat Ukur Tambahan (Perlu Persetujuan):
                                            </strong>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($pendingPerpanjangan->getDaftarAlatTambahan() as $item)
                                                    <div class="p-2 bg-white rounded border d-flex align-items-center gap-2">
                                                        <span class="badge bg-secondary font-monospace">{{ $item['kode_alat'] }}</span>
                                                        <strong class="text-dark small">{{ $item['nama_alat'] }}</strong>
                                                        <span class="badge bg-primary text-white">{{ $item['qty'] }} unit</span>
                                                        @if($item['available_stock'] < $item['qty'])
                                                            <span class="badge bg-danger text-white">Stok Kurang! (Sisa: {{ $item['available_stock'] }})</span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Stok: {{ $item['available_stock'] }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Catatan / Alasan Peminjam --}}
                                    <div class="p-3 rounded mb-3" style="background: #fffdf5; border: 1px solid #fef3c7;">
                                        <div class="d-flex align-items-start gap-2">
                                            <i class="bi bi-chat-left-text-fill text-warning mt-1 fs-6"></i>
                                            <div class="w-100">
                                                <strong class="text-dark d-block small text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    Catatan / Alasan Perpanjangan dari Peminjam:
                                                </strong>
                                                <p class="mb-0 text-dark mt-1 fst-italic" style="font-size: 0.88rem; line-height: 1.5;">
                                                    "{{ $pendingPerpanjangan->alasan }}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Tombol Tindakan Admin --}}
                                    <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap pt-2 border-top no-print">
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold"
                                                data-bs-toggle="modal" data-bs-target="#modalTolakPerpanjangan_{{ $pendingPerpanjangan->id }}">
                                            <i class="bi bi-x-circle me-1"></i> Tolak Perpanjangan
                                        </button>
                                        <button type="button" class="btn btn-success btn-sm rounded-pill px-3 fw-bold"
                                                data-bs-toggle="modal" data-bs-target="#modalSetujuiPerpanjangan_{{ $pendingPerpanjangan->id }}">
                                            <i class="bi bi-check-circle-fill me-1"></i> Setujui Perpanjangan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Riwayat Perpanjangan Jika Pernah Ada Sebelumnya --}}
                    @if($peminjaman->perpanjangans->where('status', '!=', 'menunggu')->isNotEmpty())
                        <div class="card border mb-4 shadow-sm rounded-3 overflow-hidden">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-secondary"></i>
                                    <span>Riwayat Pengajuan Perpanjangan Peminjaman</span>
                                </h6>
                                <span class="badge bg-white text-secondary border">
                                    {{ $peminjaman->perpanjangans->where('status', '!=', 'menunggu')->count() }} Catatan
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th style="width: 15%;">Tgl Diajukan</th>
                                            <th style="width: 25%;">Perubahan Batas Kembali</th>
                                            <th style="width: 25%;">Alasan Peminjam</th>
                                            <th class="text-center" style="width: 12%;">Status</th>
                                            <th style="width: 18%;">Keputusan & Catatan Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman->perpanjangans->where('status', '!=', 'menunggu') as $rp)
                                            <tr>
                                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                <td>
                                                    <div>{{ $rp->created_at->translatedFormat('d M Y') }}</div>
                                                    <small class="text-muted">{{ $rp->created_at->format('H:i') }} WIB</small>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ \Carbon\Carbon::parse($rp->tanggal_kembali_lama)->translatedFormat('d M Y') }}</span>
                                                    &rarr;
                                                    <strong class="text-dark">{{ \Carbon\Carbon::parse($rp->tanggal_kembali_baru)->translatedFormat('d M Y') }}</strong>
                                                    <span class="badge bg-light text-secondary border ms-1">+{{ $rp->tambahanHari() }} hari</span>
                                                </td>
                                                <td>
                                                    <div class="fst-italic text-secondary">"{{ $rp->alasan }}"</div>
                                                </td>
                                                <td class="text-center">
                                                    @if($rp->status === 'disetujui')
                                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Disetujui
                                                        </span>
                                                    @elseif($rp->status === 'ditolak')
                                                        <span class="badge bg-danger-subtle text-danger border border-danger px-2 py-1">
                                                            <i class="bi bi-x-circle-fill me-1"></i>Ditolak
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($rp->admin)
                                                        <div class="fw-semibold text-dark">{{ $rp->admin->name }}</div>
                                                    @endif
                                                    @if($rp->catatan_admin)
                                                        <div class="small text-muted mt-0.5"><em>"{{ $rp->catatan_admin }}"</em></div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- Banner Tambahan Peminjaman Jika Ada --}}
                    @if($peminjaman->hasTambahan())
                        <div class="card border-primary-subtle shadow-sm mb-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border-left: 4px solid var(--primary) !important;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary px-2 py-1"><i class="bi bi-layers-fill me-1"></i> 1 Proses Transaksi</span>
                                        <strong class="text-dark">Transaksi Ini Memiliki Tambahan Peminjaman</strong>
                                    </div>
                                    <span class="badge bg-light text-secondary border">Total: {{ $peminjaman->totalQtyAll() }} Unit Fisik</span>
                                </div>
                                <div class="row g-2 small text-secondary mt-1">
                                    <div class="col-sm-6">
                                        <div class="p-2 bg-white rounded border">
                                            <span class="text-muted d-block">Peminjaman Pertama (Awal):</span>
                                            <strong class="text-primary fs-6">{{ $peminjaman->totalQtyPertama() }} unit</strong>
                                            <span class="text-muted">({{ $peminjaman->detailsPertama->count() }} jenis alat)</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="p-2 bg-white rounded border">
                                            <span class="text-muted d-block">Tambahan Peminjaman:</span>
                                            <strong class="text-purple fs-6" style="color: #7e22ce;">{{ $peminjaman->totalQtyTambahan() }} unit</strong>
                                            <span class="text-muted">({{ $peminjaman->detailsTambahan->count() }} jenis alat tambahan)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Daftar Alat Ukur Presisi --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">
                                <i class="bi bi-rulers text-primary me-2"></i>Daftar Alat Ukur Presisi yang Dipinjam
                            </h5>
                            <small class="text-muted">Seluruh alat terakumulasi dalam 1 proses transaksi peminjaman ini</small>
                        </div>
                        <span class="badge bg-light text-muted border">{{ $peminjaman->details->count() }} Entri Alat</span>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- 1. RINCIAN ALAT PEMINJAMAN PERTAMA (UTAMA) & KETERANGANNYA --}}
                    {{-- ========================================================= --}}
                    <div class="card border mb-4 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header bg-light py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                            <div>
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-1-circle-fill me-1"></i>Peminjaman Pertama (Utama)
                                    </span>
                                    <span>Alat Ukur Presisi Awal</span>
                                </h6>
                            </div>
                            <span class="badge bg-white text-dark border fw-semibold">
                                Subtotal: {{ $peminjaman->totalQtyPertama() }} unit
                            </span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light text-secondary" style="font-size: 0.8rem;">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No</th>
                                        <th style="width: 20%;">Kode Alat</th>
                                        <th style="width: 55%;">Nama Alat Ukur Presisi</th>
                                        <th class="text-center" style="width: 20%;">Kuantitas (Qty)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($peminjaman->detailsPertama as $d1)
                                        <tr>
                                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="badge bg-secondary font-monospace">{{ $d1->tool->kode_alat ?? '-' }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $d1->tool->nama_alat ?? 'Alat' }}</div>
                                            </td>
                                            <td class="text-center fw-bold text-primary">{{ $d1->qty }} unit</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-2">Tidak ada data peminjaman pertama.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Keterangan Khusus Peminjaman Pertama --}}
                        <div class="p-3 bg-light-subtle border-top">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-chat-left-quote-fill text-primary mt-0.5"></i>
                                <div>
                                    <strong class="small text-secondary text-uppercase d-block" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                        Keterangan / Keperluan Peminjaman Pertama:
                                    </strong>
                                    <span class="small text-dark fst-italic">
                                        {{ $peminjaman->getCatatanPertama() ?? 'Tidak ada catatan khusus pada peminjaman pertama.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- 2. RINCIAN ALAT TAMBAHAN PEMINJAMAN & KETERANGANNYA       --}}
                    {{-- ========================================================= --}}
                    @if($peminjaman->hasTambahan())
                        <div class="card border-purple mb-4 shadow-sm rounded-3 overflow-hidden" style="border-color: #d8b4fe !important;">
                            <div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom" style="background: #faf5ff;">
                                <div>
                                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: #6b21a8;">
                                        <span class="badge text-white px-2 py-1" style="background: #7e22ce; font-size: 0.75rem;">
                                            <i class="bi bi-layers-fill me-1"></i>Tambahan Peminjaman
                                        </span>
                                        <span>Alat Ukur Tambahan (Digabungkan ke Transaksi Ini)</span>
                                    </h6>
                                </div>
                                <span class="badge bg-white border fw-semibold" style="color: #7e22ce;">
                                    Subtotal: {{ $peminjaman->totalQtyTambahan() }} unit
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="text-secondary" style="font-size: 0.8rem; background: #fdf4ff;">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th style="width: 15%;">Tahap Tambahan</th>
                                            <th style="width: 18%;">Kode Alat</th>
                                            <th style="width: 42%;">Nama Alat Ukur Presisi</th>
                                            <th class="text-center" style="width: 20%;">Kuantitas (Qty)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman->detailsTambahan as $dt)
                                            <tr style="background: #faf5ff;">
                                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="badge text-white px-2 py-0.5" style="background: #7e22ce; font-size: 0.7rem;">
                                                        Tahap {{ $dt->tahap }}
                                                    </span>
                                                    <small class="text-muted d-block mt-0.5" style="font-size: 0.68rem;">
                                                        {{ $dt->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td><span class="badge bg-secondary font-monospace">{{ $dt->tool->kode_alat ?? '-' }}</span></td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $dt->tool->nama_alat ?? 'Alat' }}</div>
                                                    @if($dt->catatan)
                                                        <small class="text-purple fst-italic d-block mt-0.5" style="color: #7e22ce; font-size: 0.73rem;">
                                                            <i class="bi bi-chat-right-dots me-1"></i>"{{ $dt->catatan }}"
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center fw-bold text-purple" style="color: #7e22ce;">{{ $dt->qty }} unit</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Keterangan Khusus Tambahan Peminjaman --}}
                            @php
                                $catatanTambahanList = $peminjaman->getCatatanTambahan();
                            @endphp
                            <div class="p-3 border-top" style="background: #fbf8ff;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-chat-right-text-fill mt-0.5" style="color: #7e22ce;"></i>
                                    <div>
                                        <strong class="small text-uppercase d-block" style="color: #6b21a8; font-size: 0.72rem; letter-spacing: 0.5px;">
                                            Keterangan / Keperluan Tambahan Alat:
                                        </strong>
                                        @if(!empty($catatanTambahanList))
                                            @foreach($catatanTambahanList as $ct)
                                                <div class="small fst-italic" style="color: #4c1d95;">
                                                    &bull; "{{ $ct }}"
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="small text-muted fst-italic">Tidak ada catatan khusus pada tambahan alat.</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Total Keseluruhan Banner --}}
                    <div class="card p-3 mb-4 shadow-sm bg-light border-2 d-flex flex-row justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong class="fs-6 text-dark d-block">Total Keseluruhan Alat dalam 1 Proses Peminjaman:</strong>
                            <small class="text-muted">
                                Peminjaman Pertama: <strong>{{ $peminjaman->totalQtyPertama() }} unit</strong>
                                @if($peminjaman->hasTambahan())
                                    + Tambahan: <strong style="color: #7e22ce;">{{ $peminjaman->totalQtyTambahan() }} unit</strong>
                                @endif
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary fs-5 px-3 py-2">
                                {{ $peminjaman->totalQtyAll() }} Unit Fisik
                            </span>
                        </div>
                    </div>

                    {{-- Hasil Inspeksi Teknisi --}}
                    @if($peminjaman->pengembalian)
                        @php
                            $p = $peminjaman->pengembalian;
                        @endphp
                        <div class="card border-info bg-light p-3 p-md-4 mb-4 shadow-sm rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 flex-wrap gap-2">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-clipboard2-check text-primary me-2"></i>Hasil Inspeksi Fisik & Uji Kalibrasi Teknisi
                                </h5>
                                @if($p->tanda_kerusakan_berat)
                                    <span class="badge bg-danger fs-6 px-3 py-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> TANDA KERUSAKAN BERAT (≥ 3 UNIT RUSAK)
                                    </span>
                                @endif
                            </div>

                            <div class="row g-3 small">
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Teknisi:</span>
                                    <strong class="fs-6 text-dark">{{ $p->teknisi->name ?? 'Teknisi Kalibrasi' }}</strong>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Tgl Cek:</span>
                                    <strong class="text-dark">{{ \Carbon\Carbon::parse($p->tanggal_dikembalikan)->translatedFormat('d F Y') }}</strong>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Kondisi Akhir:</span>
                                    <span class="badge bg-{{ $p->kondisi_alat === 'baik' ? 'success' : 'danger' }} fs-6">
                                        {{ strtoupper($p->kondisi_alat) }}
                                    </span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Rincian Fisik:</span>
                                    <strong>Baik: {{ $p->jumlah_baik }} unit</strong> | <strong class="text-danger">Rusak: {{ $p->jumlah_rusak }} unit</strong>
                                </div>

                                {{-- Status Tindakan Repair --}}
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <span class="text-muted d-block fw-semibold mb-1">Status Kelaikan Perbaikan (Repair):</span>
                                    @if($p->status_repair === 'selesai_direpair')
                                        <span class="badge bg-success-subtle text-success border border-success p-2">
                                            <i class="bi bi-check-circle-fill me-1"></i> Dibenarkan / Selesai Diperbaiki oleh Teknisi (Stok dipulihkan normal)
                                        </span>
                                    @elseif($p->status_repair === 'dalam_perbaikan')
                                        <span class="badge bg-info-subtle text-info border border-info p-2">
                                            <i class="bi bi-wrench me-1"></i> Dalam Pengerjaan Reparasi Teknisi (Bisa diperbaiki)
                                        </span>
                                    @elseif($p->status_repair === 'tidak_bisa_direpair')
                                        <span class="badge bg-danger-subtle text-danger border border-danger p-2">
                                            <i class="bi bi-x-octagon-fill me-1"></i> Tidak Bisa Di-repair (Kerusakan Fatal / Afkir)
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>

                                @if($p->catatan_teknisi)
                                    <div class="col-md-6 mt-2 pt-2 border-top">
                                        <span class="text-muted d-block fw-semibold">Temuan Kerusakan:</span>
                                        <div class="p-2 bg-white rounded border mt-1">
                                            <em>"{{ $p->catatan_teknisi }}"</em>
                                        </div>
                                    </div>
                                @endif

                                @if($p->catatan_perbaikan)
                                    <div class="col-md-6 mt-2 pt-2 border-top">
                                        <span class="text-muted d-block fw-semibold text-success">Catatan Tindakan Perbaikan:</span>
                                        <div class="p-2 bg-white rounded border mt-1 text-success-emphasis">
                                            <em>"{{ $p->catatan_perbaikan }}"</em>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- BLOK KEPUTUSAN & KONFIRMASI ADMIN --}}
                        @if($p->status_repair === 'tidak_bisa_direpair' || in_array($peminjaman->status, ['menunggu_konfirmasi', 'rusak_dikonfirmasi', 'rusak']))
                            <div class="card border-warning mb-4 shadow-sm rounded-3">
                                <div class="card-header bg-warning-subtle py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-warning-emphasis">
                                        <i class="bi bi-shield-lock-fill me-2"></i>Keputusan Hak Akses Admin & Konfirmasi User
                                    </h6>
                                    <span class="badge bg-warning text-dark">Prosedur Evaluasi Aset Rusak</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        {{-- Kolom Admin --}}
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-dark"><i class="bi bi-shield-check text-primary me-1"></i>Keputusan Admin Tool Crib</strong>
                                                    @if($p->konfirmasi_admin)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge bg-danger"><i class="bi bi-clock me-1"></i>Belum Konfirmasi</span>
                                                    @endif
                                                </div>

                                                @if($p->konfirmasi_admin)
                                                    <div class="small text-muted mb-1">
                                                        Dikonfirmasi oleh: <strong>{{ $p->adminKonfirmasi->name ?? 'Admin Tool Crib' }}</strong>
                                                        ({{ $p->konfirmasi_admin_at ? \Carbon\Carbon::parse($p->konfirmasi_admin_at)->translatedFormat('d M Y, H:i') : '-' }})
                                                    </div>
                                                    <div class="small mb-2">
                                                        <strong>Tindakan Administratif:</strong>
                                                        <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $p->tindakan_admin ?? 'Afkir Aset') }}</span>
                                                    </div>
                                                    <div class="p-2 bg-white rounded border small">
                                                        <strong>Catatan Keputusan Admin:</strong> "{{ $p->catatan_admin ?? '-' }}"
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-2">
                                                        Alat ini dilaporkan rusak permanen ({{ $p->jumlah_rusak }} unit). Silakan tentukan keputusan administratif berikut:
                                                    </p>
                                                    <div class="p-3 bg-white rounded border border-warning">
                                                        <form action="{{ route('admin.peminjaman.konfirmasi', $peminjaman->id) }}" method="POST">
                                                            @csrf
                                                            <div class="mb-2">
                                                                <label class="form-label small fw-bold">Tindakan Administratif: <span class="text-danger">*</span></label>
                                                                <select name="tindakan_admin" class="form-select form-select-sm" required>
                                                                    <option value="afkir_aset">Afkir / Hapus dari Total Inventaris Aset</option>
                                                                    <option value="bebankan_ganti_rugi">Bebankan Penggantian / Ganti Rugi ke Departemen/User</option>
                                                                    <option value="toleransi">Toleransi Kerusakan / Biaya Perusahaan</option>
                                                                    <option value="lainnya">Lainnya</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label small fw-bold">Catatan Arahan Admin: <span class="text-danger">*</span></label>
                                                                <textarea name="catatan_admin" rows="2" class="form-control form-control-sm" required
                                                                    placeholder="Tuliskan arahan resmi..."></textarea>
                              <ctrl61>                              </div>
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input" type="checkbox" name="potong_total_stock" id="chkPotongStockAdmin" value="1" checked>
                                                                <label class="form-check-label small text-muted" for="chkPotongStockAdmin">
                                                                    Kurangi <strong>{{ $p->jumlah_rusak }} unit</strong> dari Total Stok Sistem
                                                                </label>
                                                            </div>
                                                            <button type="submit" class="btn btn-warning btn-sm fw-bold w-100">
                                                                <i class="bi bi-check2-circle me-1"></i> Simpan Konfirmasi Keputusan Admin
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Kolom User --}}
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-dark"><i class="bi bi-person-check text-primary me-1"></i>Konfirmasi User Peminjam</strong>
                                                    @if($p->konfirmasi_user)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Tanggapan User</span>
                                                    @endif
                                                </div>

                                                @if($p->konfirmasi_user)
                                                    <div class="small text-muted mb-1">
                                                        Dikonfirmasi pada: <strong>{{ $p->konfirmasi_user_at ? \Carbon\Carbon::parse($p->konfirmasi_user_at)->translatedFormat('d M Y, H:i') : '-' }}</strong>
                                                    </div>
                                                    <div class="p-2 bg-white rounded border small mt-2">
                                                        <strong>Tanggapan Peminjam:</strong> "{{ $p->catatan_user ?? '-' }}"
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-0">
                                                        User peminjam (PIC: <strong>{{ $peminjaman->pic }}</strong>) belum memberikan tanggapan atas laporan kerusakan ini.
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Kolom Tanda Tangan Resmi (Tampil di Cetakan Dokumen) --}}
                    <div class="mt-4 pt-4 border-top signature-block">
                        <div class="row text-center g-3">
                            <div class="col-4">
                                <small class="text-muted d-block mb-4">Peminjam / PIC Departemen,</small>
                                <div style="height: 45px;"></div>
                                <div class="fw-bold border-top pt-1 text-dark">({{ $peminjaman->pic }})</div>
                                <small class="text-muted">{{ $peminjaman->department }}</small>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-4">Petugas Tool Crib / Admin,</small>
                                <div style="height: 45px;"></div>
                                <div class="fw-bold border-top pt-1 text-dark">
                                    ( {{ $peminjaman->pengembalian?->adminKonfirmasi?->name ?? auth()->user()->name }} )
                                </div>
                                <small class="text-muted">Inventory & Tooling</small>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block mb-4">Teknisi Kalibrasi / QC,</small>
                                <div style="height: 45px;"></div>
                                <div class="fw-bold border-top pt-1 text-dark">
                                    ( {{ $peminjaman->pengembalian->teknisi->name ?? 'Teknisi Kalibrasi' }} )
                                </div>
                                <small class="text-muted">QC & Calibration</small>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Bawah Navigasi --}}
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-4 no-print">
                        <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Monitoring Peminjaman
                        </a>
                    </div>
                </div>
            </div>
    {{-- MODALS KONFIRMASI PERPANJANGAN ADMIN --}}
    @if($peminjaman->hasPendingPerpanjangan())
        @php
            $pendingPerpanjangan = $peminjaman->perpanjangans->firstWhere('status', 'menunggu');
        @endphp
        @if($pendingPerpanjangan)
            {{-- Modal Setujui --}}
            <div class="modal fade" id="modalSetujuiPerpanjangan_{{ $pendingPerpanjangan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.perpanjangan.setujui', $pendingPerpanjangan->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-check-circle-fill me-2"></i>Setujui Perpanjangan Peminjaman
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Anda akan menyetujui pengajuan perpanjangan/tambahan alat untuk Transaksi <strong>#{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}</strong> (PIC: {{ $peminjaman->pic }}).
                                </p>
                                @if($pendingPerpanjangan->isPerpanjanganTanggal())
                                    <div class="p-3 bg-light rounded border mb-3">
                                        <div class="small text-muted mb-1">Perubahan Batas Pengembalian Resmi:</div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="text-decoration-line-through text-muted">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_lama)->translatedFormat('d F Y') }}</span>
                                            <i class="bi bi-arrow-right text-success"></i>
                                            <strong class="text-success fs-6">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_baru)->translatedFormat('d F Y') }}</strong>
                                            <span class="badge bg-success text-white">(+{{ $pendingPerpanjangan->tambahanHari() }} hari)</span>
                                        </div>
                                    </div>
                                @endif

                                @if($pendingPerpanjangan->hasAlatTambahan())
                                    <div class="p-3 rounded border mb-3" style="background: #faf5ff; border-color: #d8b4fe !important;">
                                        <strong class="text-purple d-block small text-uppercase mb-1" style="color: #7e22ce; font-size: 0.72rem;">
                                            <i class="bi bi-cart-plus me-1"></i>Persetujuan Alat Tambahan:
                                        </strong>
                                        <div class="small text-secondary mb-1">Alat berikut akan dipotong stoknya dan digabung ke transaksi ini:</div>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($pendingPerpanjangan->getDaftarAlatTambahan() as $item)
                                                <li>
                                                    &bull; <strong>{{ $item['nama_alat'] }}</strong>: {{ $item['qty'] }} unit
                                                    @if($item['available_stock'] < $item['qty'])
                                                        <span class="badge bg-danger text-white ms-1">Stok Kurang! (Sisa: {{ $item['available_stock'] }})</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Tersedia: {{ $item['available_stock'] }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-1">Alasan Peminjam:</small>
                                    <div class="p-2 bg-white rounded border fst-italic small">
                                        "{{ $pendingPerpanjangan->alasan }}"
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Catatan Arahan Admin (Opsional):</label>
                                    <textarea name="catatan_admin" rows="2" class="form-control form-control-sm"
                                              placeholder="Contoh: Disetujui, pastikan alat dijaga dan dikembalikan tepat waktu."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                                    <i class="bi bi-check2-circle me-1"></i> Ya, Setujui Perpanjangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Tolak --}}
            <div class="modal fade" id="modalTolakPerpanjangan_{{ $pendingPerpanjangan->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.perpanjangan.tolak', $pendingPerpanjangan->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-x-circle-fill me-2"></i>Tolak Perpanjangan Peminjaman
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Anda akan menolak permohonan perpanjangan waktu pengembalian alat untuk Transaksi <strong>#{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}</strong>. Tanggal pengembalian akan tetap berlaku sebagaimana semula ({{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_lama)->translatedFormat('d F Y') }}).
                                </p>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Alasan Penolakan dari Admin <span class="text-danger">*</span>:</label>
                                    <textarea name="catatan_admin" rows="3" class="form-control form-control-sm" required minlength="5"
                                              placeholder="Tuliskan alasan penolakan, contoh: Alat sudah dijadwalkan untuk kalibrasi rutin / departemen lain telah membooking alat ini."></textarea>
                                    <div class="form-text text-muted" style="font-size: 0.72rem;">
                                        Alasan ini akan disampaikan kepada peminjam agar dapat segera mengembalikan alat sesuai jadwal.
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3">
                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-sm fw-bold px-3">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Permohonan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@push('scripts')
<script>
    function copyTransactionId(id) {
        navigator.clipboard.writeText('#' + id).then(() => {
            alert('Nomor Transaksi #' + id + ' berhasil disalin ke clipboard!');
        }).catch(err => {
            console.error('Gagal menyalin:', err);
        });
    }
</script>
@endpush
