@extends('layouts.app')

@section('title', 'Tanda Terima Peminjaman #' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT))
@section('page_title', 'Bukti Peminjaman')

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
                    {{-- Header Tanda Terima --}}
                    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-primary px-3 py-1 fw-semibold">
                                    <i class="bi bi-rulers me-1"></i> BUKTI PEMINJAMAN ALAT UKUR PRESISI
                                </span>
                                <div class="qc-stamp">
                                    <i class="bi bi-patch-check-fill"></i> ISO QC CALIBRATION VERIFIED
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
                                        <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                    </span>
                                @elseif($peminjaman->status === 'rusak')
                                    <span class="badge bg-danger fs-6 px-3 py-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Selesai (Ada Kerusakan)
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                                @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->id() === $peminjaman->user_id))
                                    @if($peminjaman->isMasihDalamWaktuPinjam())
                                        <a href="{{ route('user.pinjam.create', ['peminjaman_id' => $peminjaman->id]) }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm no-print">
                                            <i class="bi bi-cart-plus-fill me-1"></i> + Tambah Alat Ukur
                                        </a>
                                    @elseif($peminjaman->status === 'dipinjam')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 no-print" style="font-size: 0.78rem;">
                                            <i class="bi bi-lock-fill me-1"></i> Batas Waktu Pinjam Berakhir
                                        </span>
                                    @endif

                                    @if($peminjaman->status === 'dipinjam')
                                        @if($peminjaman->hasPendingPerpanjangan())
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-2 no-print" style="font-size: 0.78rem;">
                                                <i class="bi bi-hourglass-split me-1"></i> Perpanjangan Ditinjau Admin
                                            </span>
                                        @else
                                            <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-3 shadow-sm no-print fw-semibold" data-bs-toggle="modal" data-bs-target="#modalPerpanjangShow">
                                                <i class="bi bi-calendar-plus me-1"></i> Perpanjang Pinjam
                                            </button>
                                        @endif
                                    @endif
                                @endif
                                <button type="button" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm" onclick="window.print()">
                                    <i class="bi bi-printer me-1"></i> Cetak Bukti Peminjaman
                                </button>
                            </div>
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

                    {{-- Alert Status Ringkasan --}}
                    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-4 p-3 flex-wrap gap-2 rounded-3">
                        <div>
                            <small class="text-muted text-uppercase fw-bold d-block">Status Transaksi Terkini:</small>
                            <strong class="fs-6">
                                @if($peminjaman->status === 'dipinjam')
                                    <span class="text-warning-emphasis"><i class="bi bi-circle-fill me-1 small"></i> Sedang Digunakan di Lapangan</span>
                                @elseif($peminjaman->status === 'menunggu_cek_teknisi')
                                    <span class="text-info"><i class="bi bi-hourglass-split me-1 small"></i> Menunggu Uji Fisik & Titik Nol oleh Teknisi</span>
                                @elseif($peminjaman->status === 'dalam_perbaikan')
                                    <span class="text-primary"><i class="bi bi-wrench me-1 small"></i> Sedang Dalam Proses Perbaikan & Kalibrasi di Bengkel</span>
                                @elseif($peminjaman->status === 'menunggu_konfirmasi')
                                    <span class="text-danger"><i class="bi bi-shield-exclamation me-1 small"></i> Alat Rusak Tidak Bisa Di-repair (Menunggu Keputusan)</span>
                                @elseif($peminjaman->status === 'rusak_dikonfirmasi')
                                    <span class="text-secondary"><i class="bi bi-check2-all me-1 small"></i> Laporan Kerusakan Selesai & Dikonfirmasi</span>
                                @elseif($peminjaman->status === 'selesai')
                                    <span class="text-success"><i class="bi bi-check-circle-fill me-1 small"></i> Selesai - Alat Normal & Telah Pulih di Stok</span>
                                @elseif($peminjaman->status === 'rusak')
                                    <span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1 small"></i> Selesai - Terdapat Kerusakan Aset</span>
                                @endif
                            </strong>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Metode Peminjaman:</small>
                            <span class="badge bg-secondary-subtle text-secondary border">Tool Crib & QC Calibration System</span>
                        </div>
                    </div>

                    {{-- Ringkasan Identitas Peminjam & Jadwal Rentang Waktu --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="text-secondary small fw-bold mb-2 text-uppercase d-flex align-items-center gap-1">
                                    <i class="bi bi-person-vcard text-primary"></i> Informasi Peminjam
                                </div>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 40%;">Nama Pemohon:</td>
                                        <td class="fw-semibold text-dark">{{ $peminjaman->user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">Departemen:</td>
                                        <td class="fw-bold text-primary">{{ $peminjaman->department }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted">PIC (Penanggung Jawab):</td>
                                        <td class="fw-semibold text-dark">{{ $peminjaman->pic }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border h-100">
                                <div class="text-secondary small fw-bold mb-2 text-uppercase d-flex align-items-center gap-1">
                                    <i class="bi bi-calendar-range text-primary"></i> Jadwal & Rentang Waktu
                                </div>
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted" style="width: 42%;">Tanggal Pinjam:</td>
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

                    {{-- Status / Pengajuan Perpanjangan Durasi --}}
                    @if($peminjaman->hasPendingPerpanjangan())
                        @php
                            $pendingPerpanjangan = $peminjaman->perpanjangans->firstWhere('status', 'menunggu');
                        @endphp
                        @if($pendingPerpanjangan)
                            <div class="card border-warning mb-4 shadow-sm rounded-3" style="border-color: #f59e0b !important; background: #fffdfa;">
                                <div class="card-header py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fffbeb; border-bottom: 1px solid #fde68a;">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-warning text-dark border border-warning fw-bold px-2 py-1">
                                            <i class="bi bi-hourglass-split me-1"></i> Pengajuan Perpanjangan Sedang Ditinjau Admin
                                        </span>
                                    </div>
                                    <span class="small text-muted">
                                        Diajukan pada: {{ $pendingPerpanjangan->created_at->translatedFormat('d F Y, H:i') }} WIB
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3 align-items-center mb-2">
                                        <div class="col-sm-4">
                                            <div class="p-2 rounded bg-light border text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem; font-weight: 700;">Batas Waktu Lama</small>
                                                <strong class="text-dark font-monospace" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_lama)->translatedFormat('d M Y') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="p-2 rounded border text-center" style="background: #eff6ff; border-color: #bfdbfe !important;">
                                                <small class="text-primary d-block text-uppercase" style="font-size: 0.68rem; font-weight: 700;">Usulan Batas Baru</small>
                                                <strong class="text-primary font-monospace" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_baru)->translatedFormat('d M Y') }}</strong>
                                                <span class="badge bg-primary text-white ms-1" style="font-size: 0.65rem;">+{{ $pendingPerpanjangan->tambahanHari() }} Hari</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="p-2 rounded bg-light border text-center">
                                                <small class="text-muted d-block text-uppercase" style="font-size: 0.68rem; font-weight: 700;">Status Permohonan</small>
                                                <span class="badge bg-warning text-dark fw-bold">Menunggu Konfirmasi</span>
                                            </div>
                                        </div>
                                    </div>
                                    @if($pendingPerpanjangan->hasAlatTambahan())
                                        <div class="p-2.5 rounded mt-2 border" style="background: #faf5ff; border-color: #d8b4fe !important;">
                                            <strong class="text-purple d-block small text-uppercase mb-1" style="color: #7e22ce; font-size: 0.7rem; font-weight: 700;">
                                                <i class="bi bi-cart-plus me-1"></i>Permohonan Alat Tambahan (Menunggu Persetujuan Admin):
                                            </strong>
                                            <div class="d-flex flex-wrap gap-1.5">
                                                @foreach($pendingPerpanjangan->getDaftarAlatTambahan() as $item)
                                                    <span class="badge bg-white text-dark border px-2 py-1" style="font-size: 0.78rem;">
                                                        <span class="font-monospace text-primary">[{{ $item['kode_alat'] }}]</span>
                                                        <strong>{{ $item['nama_alat'] }}</strong>: {{ $item['qty'] }} unit
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div class="p-2.5 rounded mt-2" style="background: #ffffff; border: 1px solid #fed7aa;">
                                        <strong class="text-dark d-block small text-uppercase" style="font-size: 0.7rem;">Catatan Alasan Anda ke Admin:</strong>
                                        <span class="fst-italic text-dark small">"{{ $pendingPerpanjangan->alasan }}"</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Riwayat Perpanjangan Jika Ada --}}
                    @if($peminjaman->perpanjangans->where('status', '!=', 'menunggu')->isNotEmpty())
                        <div class="card border mb-4 shadow-sm rounded-3 overflow-hidden">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                    <i class="bi bi-clock-history text-secondary"></i>
                                    <span>Riwayat Perpanjangan Peminjaman</span>
                                </h6>
                                <span class="badge bg-white text-secondary border" style="font-size: 0.7rem;">
                                    {{ $peminjaman->perpanjangans->where('status', '!=', 'menunggu')->count() }} Catatan
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 0.8rem;">
                                    <thead class="table-light text-secondary">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">No</th>
                                            <th style="width: 20%;">Tgl Diajukan</th>
                                            <th style="width: 25%;">Perubahan Batas</th>
                                            <th style="width: 25%;">Alasan Anda</th>
                                            <th class="text-center" style="width: 12%;">Status</th>
                                            <th style="width: 18%;">Tanggapan Admin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($peminjaman->perpanjangans->where('status', '!=', 'menunggu') as $rp)
                                            <tr>
                                                <td class="text-center text-muted">{{ $loop->iteration }}</td>
                                                <td>{{ $rp->created_at->translatedFormat('d M Y, H:i') }}</td>
                                                <td>
                                                    <span class="text-muted">{{ \Carbon\Carbon::parse($rp->tanggal_kembali_lama)->translatedFormat('d M Y') }}</span>
                                                    &rarr;
                                                    <strong class="text-dark">{{ \Carbon\Carbon::parse($rp->tanggal_kembali_baru)->translatedFormat('d M Y') }}</strong>
                                                    <span class="badge bg-light text-secondary border ms-1">+{{ $rp->tambahanHari() }} hr</span>
                                                </td>
                                                <td><span class="fst-italic text-secondary">"{{ $rp->alasan }}"</span></td>
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
                                                    @if($rp->catatan_admin)
                                                        <span class="small text-dark"><em>"{{ $rp->catatan_admin }}"</em></span>
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

                    {{-- Banner Informasi Tambahan Peminjaman Jika Ada --}}
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
                                            <span class="text-muted">({{ $peminjaman->detailsPertama->count() }} jenis alat ukur)</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="p-2 bg-white rounded border">
                                            <span class="text-muted d-block">Tambahan Peminjaman:</span>
                                            <strong class="text-purple fs-6" style="color: #7e22ce;">{{ $peminjaman->totalQtyTambahan() }} unit</strong>
                                            <span class="text-muted">({{ $peminjaman->detailsTambahan->count() }} jenis alat ukur tambahan)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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

                    {{-- HISTORI & STATUS PENGAJUAN PERPANJANGAN PEMINJAMAN ALAT --}}
                    @if($peminjaman->perpanjangans->isNotEmpty())
                        <div class="card p-3 p-md-4 mb-4 shadow-sm rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom flex-wrap gap-2">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="bi bi-calendar-range text-primary me-2"></i>Riwayat & Status Pengajuan Perpanjangan
                                </h6>
                                <span class="badge bg-light text-secondary border">
                                    {{ $peminjaman->perpanjangans->count() }} Riwayat Pengajuan
                                </span>
                            </div>

                            <div class="d-flex flex-column gap-3">
                                @foreach($peminjaman->perpanjangans as $perp)
                                    <div class="p-3 bg-white rounded-3 border"
                                         style="border-left: 4px solid {{ $perp->status === 'disetujui' ? '#059669' : ($perp->status === 'ditolak' ? '#dc2626' : '#d97706') }} !important;">
                                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                            <div>
                                                @if($perp->status === 'menunggu')
                                                    <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                                        <i class="bi bi-hourglass-split me-1"></i>Menunggu Konfirmasi Admin
                                                    </span>
                                                @elseif($perp->status === 'disetujui')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Disetujui Admin
                                                    </span>
                                                @elseif($perp->status === 'ditolak')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill me-1"></i>Ditolak Admin
                                                    </span>
                                                @endif
                                                <span class="small text-muted ms-2">
                                                    Diajukan: {{ $perp->created_at->translatedFormat('d F Y, H:i') }} WIB
                                                </span>
                                            </div>
                                            <div class="text-end">
                                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                                    {{ \Carbon\Carbon::parse($perp->tanggal_kembali_lama)->translatedFormat('d M Y') }}
                                                    &rarr;
                                                    <span class="text-primary">{{ \Carbon\Carbon::parse($perp->tanggal_kembali_baru)->translatedFormat('d M Y') }}</span>
                                                </span>
                                                <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">+{{ $perp->tambahanHari() }} hari</span>
                                            </div>
                                        </div>

                                        {{-- Catatan / Alasan dari User --}}
                                        <div class="p-2.5 rounded mb-2" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.82rem;">
                                            <strong class="text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem; letter-spacing: 0.03em;">
                                                <i class="bi bi-chat-quote-fill me-1 text-primary"></i>Catatan / Alasan Peminjam untuk Admin:
                                            </strong>
                                            <div class="text-dark">"{{ $perp->alasan }}"</div>
                                        </div>

                                        {{-- Catatan Evaluasi Admin Jika Sudah Dikonfirmasi --}}
                                        @if($perp->status !== 'menunggu')
                                            <div class="p-2.5 rounded" style="background: {{ $perp->status === 'disetujui' ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $perp->status === 'disetujui' ? '#a7f3d0' : '#fecaca' }}; font-size: 0.82rem;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <strong class="{{ $perp->status === 'disetujui' ? 'text-success' : 'text-danger' }}" style="font-size: 0.72rem; text-transform: uppercase;">
                                                        <i class="bi bi-shield-check me-1"></i>Keputusan Administrator ({{ $perp->admin->name ?? 'Admin' }}):
                                                    </strong>
                                                    <span class="small text-muted">
                                                        {{ $perp->confirmed_at ? \Carbon\Carbon::parse($perp->confirmed_at)->translatedFormat('d M Y, H:i') : '' }}
                                                    </span>
                                                </div>
                                                <div class="text-dark">
                                                    {{ $perp->catatan_admin ? '"' . $perp->catatan_admin . '"' : ($perp->status === 'disetujui' ? 'Disetujui tanpa catatan tambahan.' : 'Ditolak tanpa catatan tambahan.') }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Bagian Pengecekan Teknisi / Pengembalian / Hasil Uji --}}
                    @if($peminjaman->pengembalian)
                        @php
                            $p = $peminjaman->pengembalian;
                        @endphp
                        <div class="card border-info bg-light p-3 p-md-4 mb-4 shadow-sm rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 border-bottom pb-2">
                                <h5 class="fw-bold text-dark mb-0">
                                    <i class="bi bi-clipboard2-check text-primary me-2"></i>Hasil Inspeksi Fisik & Presisi Oleh Teknisi
                                </h5>
                                @if($p->tanda_kerusakan_berat)
                                    <span class="badge bg-danger fs-6 px-3 py-1">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> TANDA KERUSAKAN BERAT (≥ 3 UNIT RUSAK)
                                    </span>
                                @endif
                            </div>

                            <div class="row g-3 small">
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Teknisi Pemeriksa:</span>
                                    <strong class="fs-6 text-dark">{{ $p->teknisi->name ?? 'Teknisi Kalibrasi' }}</strong>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Tanggal Pengecekan:</span>
                                    <strong class="text-dark">{{ \Carbon\Carbon::parse($p->tanggal_dikembalikan)->translatedFormat('d F Y') }}</strong>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Kondisi Hasil Uji:</span>
                                    <span class="badge bg-{{ $p->kondisi_alat === 'baik' ? 'success' : 'danger' }} fs-6">
                                        {{ $p->kondisi_alat === 'baik' ? 'KONDISI BAIK & NORMAL' : 'TERDAPAT KERUSAKAN' }}
                                    </span>
                                </div>
                                <div class="col-md-3 col-6">
                                    <span class="text-muted d-block">Rincian Unit:</span>
                                    <strong>Baik: {{ $p->jumlah_baik }} unit</strong> | <strong class="text-danger">Rusak: {{ $p->jumlah_rusak }} unit</strong>
                                </div>

                                {{-- Status Tindakan Repair --}}
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <span class="text-muted d-block fw-semibold mb-1">Status Kelaikan Perbaikan (Repair):</span>
                                    @if($p->status_repair === 'selesai_direpair')
                                        <div class="alert alert-success d-flex align-items-center gap-2 mb-0 py-2 rounded-3">
                                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                            <div>
                                                <strong class="text-success">Alat Telah Dibenarkan (Berhasil Diperbaiki oleh Teknisi)</strong>
                                                <div class="small">Seluruh unit alat ukur telah diperbaiki, dikalibrasi normal, dan stok telah dipulihkan ke inventaris siap pakai.</div>
                                            </div>
                                        </div>
                                    @elseif($p->status_repair === 'dalam_perbaikan')
                                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0 py-2 rounded-3">
                                            <i class="bi bi-wrench-adjustable text-info fs-5"></i>
                                            <div>
                                                <strong class="text-info-emphasis">Sedang Dalam Proses Perbaikan & Kalibrasi di Bengkel Teknisi</strong>
                                                <div class="small">Unit rusak ({{ $p->jumlah_rusak }} unit) ditahan untuk pengerjaan perbaikan. Unit yang baik ({{ $p->jumlah_baik }} unit) telah kembali ke stok inventaris.</div>
                                            </div>
                                        </div>
                                    @elseif($p->status_repair === 'tidak_bisa_direpair')
                                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-0 py-2 rounded-3">
                                            <i class="bi bi-x-octagon-fill text-danger fs-5"></i>
                                            <div>
                                                <strong class="text-danger">Alat Dinyatakan TIDAK BISA DI-REPAIR (Kerusakan Fatal / Permanen)</strong>
                                                <div class="small">Unit rusak ({{ $p->jumlah_rusak }} unit) tidak dapat diperbaiki dan membutuhkan konfirmasi serta keputusan resmi dari Admin Tool Crib dan User peminjam.</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($p->catatan_teknisi)
                                    <div class="col-md-6 mt-2 pt-2 border-top">
                                        <span class="text-muted d-block fw-semibold">Uraian Temuan Kerusakan:</span>
                                        <div class="p-2 bg-white rounded border mt-1">
                                            <em>"{{ $p->catatan_teknisi }}"</em>
                                        </div>
                                    </div>
                                @endif

                                @if($p->catatan_perbaikan)
                                    <div class="col-md-6 mt-2 pt-2 border-top">
                                        <span class="text-muted d-block fw-semibold text-success">Catatan Tindakan Perbaikan / Kalibrasi:</span>
                                        <div class="p-2 bg-white rounded border mt-1 text-success-emphasis">
                                            <em>"{{ $p->catatan_perbaikan }}"</em>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- BLOK STATUS KONFIRMASI HAK AKSES ADMIN & USER --}}
                        @if($p->status_repair === 'tidak_bisa_direpair' || in_array($peminjaman->status, ['menunggu_konfirmasi', 'rusak_dikonfirmasi', 'rusak']))
                            <div class="card border-warning mb-4 shadow-sm rounded-3">
                                <div class="card-header bg-warning-subtle py-2 d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold mb-0 text-warning-emphasis">
                                        <i class="bi bi-shield-lock-fill me-2"></i>Status Konfirmasi Hak Akses Admin & User Terkait
                                    </h6>
                                    <span class="badge bg-warning text-dark">Prosedur Evaluasi Kerusakan Fatal</span>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        {{-- Kolom 1: Konfirmasi Admin --}}
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-dark"><i class="bi bi-shield-check text-primary me-1"></i>Hak Akses: Admin Tool Crib</strong>
                                                    @if($p->konfirmasi_admin)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Konfirmasi</span>
                                                    @endif
                                                </div>

                                                @if($p->konfirmasi_admin)
                                                    <div class="small text-muted mb-1">
                                                        Dikonfirmasi oleh: <strong>{{ $p->adminKonfirmasi->name ?? 'Admin Tool Crib' }}</strong>
                                                        ({{ $p->konfirmasi_admin_at ? \Carbon\Carbon::parse($p->konfirmasi_admin_at)->translatedFormat('d M Y, H:i') : '-' }})
                                                    </div>
                                                    <div class="small mb-1">
                                                        <strong>Tindakan Admin:</strong>
                                                        <span class="badge bg-secondary text-uppercase">{{ str_replace('_', ' ', $p->tindakan_admin ?? 'Afkir Aset') }}</span>
                                                    </div>
                                                    <div class="p-2 bg-white rounded border small mt-1">
                                                        <strong>Catatan Admin:</strong> "{{ $p->catatan_admin ?? '-' }}"
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-2">
                                                        Admin belum memberikan keputusan tindak lanjut aset atas unit rusak permanen ini.
                                                    </p>

                                                    {{-- Form Konfirmasi untuk Admin jika sedang login sebagai admin --}}
                                                    @if(auth()->check() && auth()->user()->role === 'admin' && $peminjaman->status === 'menunggu_konfirmasi')
                                                        <div class="mt-3 p-3 bg-white rounded border border-warning">
                                                            <h6 class="fw-bold text-primary mb-2">Formulir Keputusan Admin:</h6>
                                                            <form action="{{ route('admin.peminjaman.konfirmasi', $peminjaman->id) }}" method="POST">
                                                                @csrf
                                                                <div class="mb-2">
                                                                    <label class="form-label small fw-bold">Pilihan Tindakan Administratif: <span class="text-danger">*</span></label>
                                                                    <select name="tindakan_admin" class="form-select form-select-sm" required>
                                                                        <option value="afkir_aset">Afkir / Hapus dari Inventaris Total (Aset Rusak Permanen)</option>
                                                                        <option value="bebankan_ganti_rugi">Bebankan Ganti Rugi / Penggantian ke Departemen/User</option>
                                                                        <option value="toleransi">Toleransi Kerusakan / Beban Biaya Maintenance Pabrik</option>
                                                                        <option value="lainnya">Lainnya / Koordinasi Internal</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label small fw-bold">Catatan / Arahan Admin: <span class="text-danger">*</span></label>
                                                                    <textarea name="catatan_admin" rows="2" class="form-control form-control-sm" required
                                                                        placeholder="Tuliskan arahan resmi admin..."></textarea>
                                                                </div>
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input" type="checkbox" name="potong_total_stock" id="chkPotongStock" value="1" checked>
                                                                    <label class="form-check-label small text-muted" for="chkPotongStock">
                                                                        Kurangi <strong>{{ $p->jumlah_rusak }} unit</strong> dari Total Stok Sistem (Afkir permanen)
                                                                    </label>
                                                                </div>
                                                                <button type="submit" class="btn btn-warning btn-sm fw-bold w-100">
                                                                    <i class="bi bi-check2-circle me-1"></i> Simpan Konfirmasi Keputusan Admin
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Kolom 2: Konfirmasi User Terkait --}}
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-dark"><i class="bi bi-person-check text-primary me-1"></i>Hak Akses: User Peminjam ({{ $peminjaman->pic }})</strong>
                                                    @if($p->konfirmasi_user)
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Sudah Dikonfirmasi</span>
                                                    @else
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Menunggu Konfirmasi</span>
                                                    @endif
                                                </div>

                                                @if($p->konfirmasi_user)
                                                    <div class="small text-muted mb-1">
                                                        Dikonfirmasi pada: <strong>{{ $p->konfirmasi_user_at ? \Carbon\Carbon::parse($p->konfirmasi_user_at)->translatedFormat('d M Y, H:i') : '-' }}</strong>
                                                    </div>
                                                    <div class="p-2 bg-white rounded border small mt-1">
                                                        <strong>Tanggapan / Catatan Peminjam:</strong> "{{ $p->catatan_user ?? '-' }}"
                                                    </div>
                                                @else
                                                    <p class="small text-muted mb-2">
                                                        Peminjam / PIC Departemen terkait belum memberikan konfirmasi tanggapan atas laporan kerusakan ini.
                                                    </p>

                                                    {{-- Form Konfirmasi untuk User pemilik peminjaman --}}
                                                    @if(auth()->check() && (auth()->user()->role === 'user' && auth()->id() === $peminjaman->user_id) && $peminjaman->status === 'menunggu_konfirmasi')
                                                        <div class="mt-3 p-3 bg-white rounded border border-danger">
                                                            <h6 class="fw-bold text-danger mb-2">Formulir Konfirmasi Peminjam:</h6>
                                                            <form action="{{ route('user.pinjam.konfirmasi', $peminjaman->id) }}" method="POST">
                                                                @csrf
                                                                <div class="mb-2">
                                                                    <label class="form-label small fw-bold">Tanggapan / Klarifikasi Anda: <span class="text-danger">*</span></label>
                                                                    <textarea name="catatan_user" rows="2" class="form-control form-control-sm" required
                                                                        placeholder="Contoh: Kami telah menerima laporan kerusakan dan berkoordinasi dengan kepala departemen untuk tindak lanjut pertanggungjawaban..."></textarea>
                                                                </div>
                                                                <button type="submit" class="btn btn-danger btn-sm fw-bold w-100">
                                                                    <i class="bi bi-check-circle-fill me-1"></i> Saya Mengetahui & Mengonfirmasi Laporan Kerusakan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @elseif($peminjaman->status === 'menunggu_cek_teknisi')
                        <div class="alert alert-info d-flex align-items-center mb-4 rounded-3" role="alert">
                            <i class="bi bi-hourglass-split fs-3 me-3 text-info"></i>
                            <div>
                                <strong>Pengembalian Sedang Menunggu Pemeriksaan Teknisi</strong><br>
                                Alat ukur telah diajukan untuk dikembalikan. Silakan serahkan unit fisik ke ruang kalibrasi / tool crib untuk dilakukan uji visual, gerak spindle/indikator, dan titik nol oleh <strong>Teknisi</strong>.
                            </div>
                        </div>
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
                                    ( {{ $peminjaman->pengembalian?->adminKonfirmasi?->name ?? 'Admin Tool Crib' }} )
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

                    {{-- Navigasi Tombol Bawah (Disembunyikan saat Print) --}}
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top mt-4 no-print flex-wrap gap-2">
                        @php
                            $backRoute = route('user.dashboard');
                            if (auth()->check()) {
                                if (auth()->user()->role === 'admin') {
                                    $backRoute = route('admin.peminjaman.index');
                                } elseif (auth()->user()->role === 'teknisi') {
                                    $backRoute = route('teknisi.antrian');
                                }
                            }
                        @endphp
                        <a href="{{ $backRoute }}" class="btn btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>

                        <div class="d-flex gap-2 flex-wrap">
                            @if($peminjaman->status === 'dipinjam' && auth()->user()->role === 'user')
                                @if($peminjaman->hasPendingPerpanjangan())
                                    <span class="badge d-inline-flex align-items-center py-2 px-3 rounded-pill" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.82rem;">
                                        <i class="bi bi-hourglass-split me-1"></i> Perpanjangan Sedang Ditinjau Admin
                                    </span>
                                @else
                                    <button type="button" class="btn btn-outline-warning text-dark rounded-pill px-3 fw-semibold shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalPerpanjangShow">
                                        <i class="bi bi-calendar-plus me-1"></i> Ajukan Perpanjangan
                                    </button>
                                @endif

                                <form action="{{ route('user.return', $peminjaman->id) }}" method="POST"
                                    onsubmit="return confirm('Apakah Anda ingin mengajukan pengembalian alat sekarang? Status akan berpindah ke antrian cek Teknisi.');">
                                    @csrf
                                    <button type="submit" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
                                        <i class="bi bi-arrow-return-left me-1"></i> Ajukan Pengembalian Alat
                                    </button>
                                </form>
                            @endif

                            @if($peminjaman->status === 'menunggu_cek_teknisi' && auth()->user()->role === 'teknisi')
                                <a href="{{ route('teknisi.cek', $peminjaman->id) }}" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-clipboard2-check me-1"></i> Lakukan Pengecekan Fisik Sekarang
                                </a>
                            @endif

                            @if($peminjaman->status === 'dalam_perbaikan' && auth()->user()->role === 'teknisi')
                                <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="btn btn-success rounded-pill px-4 fw-semibold shadow-sm">
                                    <i class="bi bi-wrench-adjustable-circle me-1"></i> Benarkan Alat di Menu Perbaikan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL AJUKAN PERPANJANGAN DI HALAMAN SHOW --}}
    @if($peminjaman->status === 'dipinjam')
        <div class="modal fade" id="modalPerpanjangShow" tabindex="-1" aria-labelledby="modalPerpanjangShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 60px);">
                <form action="{{ route('user.pinjam.perpanjang', $peminjaman->id) }}" method="POST" class="modal-content border-0 shadow" style="max-height: calc(100vh - 60px); display: flex; flex-direction: column;">
                    @csrf
                    <div class="modal-header text-white flex-shrink-0" style="background: var(--primary); border-bottom: 2px solid var(--accent); padding: 0.75rem 1.25rem;">
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="modalPerpanjangShowLabel">
                                <i class="bi bi-calendar-plus me-2"></i>Ajukan Perpanjangan Peminjaman
                            </h5>
                            <small class="text-white-50">
                                Transaksi #{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $peminjaman->department }}
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-3 p-md-4" style="overflow-y: auto; max-height: calc(100vh - 210px);">
                        {{-- Informasi Tanggal Saat Ini --}}
                        <div class="p-3 mb-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small text-secondary">Batas Waktu Kembali Saat Ini:</span>
                                <span class="fw-bold text-dark font-monospace" style="font-size: 0.85rem;">
                                    {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('l, d F Y') }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary">Status Durasi:</span>
                                @php $sisaHari = $peminjaman->sisaHariPinjam(); @endphp
                                @if($sisaHari < 0)
                                    <span class="badge bg-danger text-white">Terlambat {{ abs($sisaHari) }} hari</span>
                                @elseif($sisaHari === 0)
                                    <span class="badge bg-warning text-dark border border-warning">Jatuh Tempo Hari Ini</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Sisa {{ $sisaHari }} hari lagi</span>
                                @endif
                            </div>
                        </div>

                        {{-- Input Tanggal Kembali Baru --}}
                        <div class="mb-3">
                            <label for="tanggal_kembali_baru_show" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">
                                Tanggal Pengembalian Baru yang Diajukan <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="tanggal_kembali_baru"
                                   id="tanggal_kembali_baru_show"
                                   class="form-control"
                                   min="{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->addDay()->format('Y-m-d') }}"
                                   required>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                Pilih tanggal setelah batas kembali saat ini (minimal: {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->addDay()->translatedFormat('d F Y') }}).
                            </div>
                        </div>

                        {{-- Pilihan Alat Tambahan Sekaligus (Opsional) --}}
                        @if(isset($availableTools) && $availableTools->isNotEmpty())
                            <div class="mb-3 pt-2 border-top">
                                <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                                    <i class="bi bi-cart-plus text-primary me-1"></i>Ingin Menambah Alat Sekaligus? (Opsional)
                                </label>
                                <div class="row g-2 align-items-center">
                                    <div class="col-8">
                                        <select name="tool_id[]" class="form-select form-select-sm">
                                            <option value="">-- Tidak menambah alat --</option>
                                            @foreach($availableTools as $tool)
                                                <option value="{{ $tool->id }}" {{ $tool->available_stock <= 0 ? 'disabled' : '' }}>
                                                    [{{ $tool->kode_alat }}] {{ $tool->nama_alat }} ({{ $tool->available_stock > 0 ? 'Tersedia: '.$tool->available_stock : 'Stok: 0 - Habis' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-sm">
                                            <input type="number" name="qty[]" class="form-control form-control-sm text-center" min="1" value="1">
                                            <span class="input-group-text">unit</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.72rem;">
                                    Pilih jika ingin menambah alat ukur lain sekaligus memperpanjang batas waktu.
                                </div>
                            </div>
                        @endif

                        {{-- Input Catatan / Alasan --}}
                        <div class="mb-3">
                            <label for="alasan_show" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">
                                Catatan / Alasan Perpanjangan untuk Admin <span class="text-danger">*</span>
                            </label>
                            <textarea name="alasan"
                                      id="alasan_show"
                                      class="form-control"
                                      rows="3"
                                      minlength="5"
                                      maxlength="1000"
                                      placeholder="Jelaskan kebutuhan pekerjaan Anda, seperti: Pekerjaan inspeksi komponen batch 2 masih berjalan di lini machining..."
                                      required></textarea>
                            <div class="form-text text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-info-circle me-1"></i>Catatan ini akan langsung dibaca oleh Administrator untuk persetujuan perpanjangan.
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.78rem;">
                            <i class="bi bi-shield-lock-fill me-1 text-primary"></i>
                            Pengajuan perpanjangan memerlukan konfirmasi persetujuan dari Administrator sebelum tanggal pengembalian resmi diperbarui.
                        </div>
                    </div>

                    <div class="modal-footer bg-light px-3 py-2 flex-shrink-0 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold px-3 shadow-sm" style="background: #f59e0b; border: 1px solid #d97706;">
                            <i class="bi bi-send-check me-1"></i> Kirim Pengajuan ke Admin
                        </button>
                    </div>
                </form>
            </div>
        </div>
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