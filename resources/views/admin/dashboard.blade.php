@extends('layouts.app')

@section('title', 'Dashboard Administrator - Gauge Hub')
@section('page_title', 'Dashboard')

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0 text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard</h1>
        </div>
        <div class="d-flex gap-2 no-print">
            <a href="{{ route('admin.tools.create') }}" class="btn-primary-modern btn d-flex align-items-center gap-1.5 btn-sm px-3">
                <i class="bi bi-plus-lg"></i> Tambah Alat
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn-ghost btn d-flex align-items-center gap-1.5 btn-sm px-3">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan
            </a>
        </div>
    </div>

    {{-- Stat Cards Row 1: Inventaris --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-rulers"></i></div>
                <div class="stat-value">{{ $stats['total_tools'] }}</div>
                <div class="stat-label">Total Jenis Alat</div>
                <div class="stat-bg-icon"><i class="bi bi-rulers"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-boxes"></i></div>
                <div class="stat-value">{{ $stats['total_stock_units'] }}</div>
                <div class="stat-label">Total Unit Fisik</div>
                <div class="stat-bg-icon"><i class="bi bi-boxes"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-check2-square"></i></div>
                <div class="stat-value">{{ $stats['available_units'] }}</div>
                <div class="stat-label">Stok Tersedia</div>
                <div class="stat-bg-icon"><i class="bi bi-check2-square"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="stat-value">{{ $stats['borrowed_units'] }}</div>
                <div class="stat-label">Sedang Dipinjam</div>
                <div class="stat-bg-icon"><i class="bi bi-arrow-left-right"></i></div>
            </div>
        </div>
    </div>

    {{-- Stat Cards Row 2: Transaksi --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--primary);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Transaksi</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--text-primary); letter-spacing: -0.02em;">{{ $stats['total_peminjaman'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--status-borrowed);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Dipinjam Aktif</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--status-borrowed); letter-spacing: -0.02em;">{{ $stats['dipinjam'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--status-queue);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Antrian Cek</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--status-queue); letter-spacing: -0.02em;">{{ $stats['menunggu_cek'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--status-repair);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Dalam Perbaikan</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--status-repair); letter-spacing: -0.02em;">{{ $stats['dalam_perbaikan'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--status-danger); background: #fef8f8;">
                <div style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--status-danger);">Butuh Konfirmasi</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--status-danger); letter-spacing: -0.02em;">{{ $stats['menunggu_konfirmasi'] }}</div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card p-3 text-center h-100" style="border-top: 3px solid var(--status-ready);">
                <div class="text-secondary" style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Total Selesai</div>
                <div class="mt-1" style="font-size: 1.75rem; font-weight: 700; color: var(--status-ready); letter-spacing: -0.02em;">{{ $stats['selesai'] }}</div>
            </div>
        </div>
    </div>

    {{-- ALERT KHUSUS ADMIN: ALAT RUSAK TIDAK BISA DIREPAIR MENUNGGU KEPUTUSAN ADMIN --}}
    @if(isset($pendingConfirmations) && $pendingConfirmations->isNotEmpty())
        <div class="card border-danger mb-4 shadow-sm">
            <div class="card-header bg-danger text-white py-2.5 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-shield-exclamation me-1.5"></i>Alat Rusak Butuh Konfirmasi
                </h6>
                <span class="badge bg-white text-danger fw-bold">{{ $pendingConfirmations->count() }} Perlu Tindakan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 15%;">No. Transaksi</th>
                                <th style="width: 20%;">Departemen & PIC</th>
                                <th style="width: 25%;">Alat Rusak</th>
                                <th class="text-center" style="width: 12%;">Jumlah Rusak</th>
                                <th style="width: 18%;">Catatan Teknisi</th>
                                <th class="text-end pe-3" style="width: 10%;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingConfirmations as $pc)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        <a href="{{ route('admin.peminjaman.show', $pc->id) }}">
                                            #{{ str_pad($pc->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        @if($pc->pengembalian?->tanda_kerusakan_berat)
                                            <div>
                                                <span class="badge bg-danger text-white" style="font-size: 0.65rem;">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> RUSAK ≥ 3 UNIT
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $pc->department }}</div>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>PIC: {{ $pc->pic }}</small>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($pc->details as $d)
                                                <li>{{ $d->tool->nama_alat ?? 'Alat' }} ({{ $d->qty }} unit)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-6">{{ $pc->pengembalian?->jumlah_rusak ?? $pc->details->sum('qty') }} unit</span>
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-block" style="max-width: 180px;" title="{{ $pc->pengembalian?->catatan_teknisi }}">
                                            "{{ $pc->pengembalian?->catatan_teknisi ?? '-' }}"
                                        </small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.peminjaman.show', $pc->id) }}" class="btn btn-danger btn-sm fw-bold">
                                            <i class="bi bi-check2-circle me-1"></i> Konfirmasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- PERINGATAN: PENGAJUAN PERPANJANGAN PEMINJAMAN MENUNGGU KONFIRMASI ADMIN --}}
    @if(isset($pendingPerpanjangans) && $pendingPerpanjangans->isNotEmpty())
        <div class="card mb-4 shadow-sm" style="border: 1px solid #fde68a; border-left: 5px solid #d97706; background: #ffffff; border-radius: 10px;">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fffbeb; border-bottom: 1px solid #fde68a;">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-flex align-items-center justify-content-center text-dark flex-shrink-0"
                          style="width: 32px; height: 32px; background: #fef3c7; color: #b45309;">
                        <i class="bi bi-calendar-plus-fill"></i>
                    </span>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Permohonan Perpanjangan & Tambah Alat</h6>
                    </div>
                </div>
                <span class="badge bg-warning text-dark border border-warning fw-bold px-2.5 py-1.5" style="font-size: 0.75rem;">
                    {{ $pendingPerpanjangans->count() }} Menunggu Persetujuan
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 12%;">No. Transaksi</th>
                                <th style="width: 18%;">Peminjam & Departemen</th>
                                <th style="width: 18%;">Alat yang Dipinjam</th>
                                <th style="width: 22%;">Rincian Pengajuan</th>
                                <th style="width: 18%;">Catatan Peminjam</th>
                                <th class="text-end pe-3" style="width: 12%;">Keputusan Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPerpanjangans as $pp)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        <a href="{{ route('admin.peminjaman.show', $pp->peminjaman_id) }}" class="font-monospace fw-bold text-decoration-none" style="color: var(--primary);">
                                            #{{ str_pad($pp->peminjaman_id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="text-muted small" style="font-size: 0.7rem;">
                                            Diajukan: {{ $pp->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $pp->peminjaman->department }}</div>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>PIC: {{ $pp->peminjaman->pic }}</small>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small" style="font-size: 0.78rem;">
                                            @foreach($pp->peminjaman->details as $d)
                                                <li>&bull; {{ $d->tool->nama_alat ?? 'Alat' }} ({{ $d->qty }} pcs)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if($pp->isPerpanjanganTanggal())
                                            <div class="text-muted small">Batas Kembali: <strong class="text-dark">{{ \Carbon\Carbon::parse($pp->tanggal_kembali_lama)->translatedFormat('d M Y') }}</strong></div>
                                            <div class="text-primary fw-bold" style="font-size: 0.82rem;">
                                                &rarr; Baru: {{ \Carbon\Carbon::parse($pp->tanggal_kembali_baru)->translatedFormat('d M Y') }}
                                                <span class="badge bg-light text-secondary border ms-1">+{{ $pp->tambahanHari() }} hari</span>
                                            </div>
                                        @else
                                            <span class="badge bg-light text-secondary border">Hanya Tambah Alat</span>
                                        @endif

                                        @if($pp->hasAlatTambahan())
                                            <div class="mt-1 pt-1 border-top">
                                                <strong class="small text-purple" style="color: #7e22ce; font-size: 0.73rem;">
                                                    <i class="bi bi-cart-plus me-1"></i>Alat Tambahan:
                                                </strong>
                                                <ul class="list-unstyled mb-0 small" style="font-size: 0.75rem;">
                                                    @foreach($pp->getDaftarAlatTambahan() as $at)
                                                        <li>
                                                            &bull; <strong>{{ $at['nama_alat'] }}</strong>: {{ $at['qty'] }} unit
                                                            @if($at['available_stock'] < $at['qty'])
                                                                <span class="badge bg-danger text-white ms-1" style="font-size: 0.65rem;">Stok Kurang ({{ $at['available_stock'] }})</span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">Tersedia: {{ $at['available_stock'] }}</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="p-2 rounded bg-light border text-dark" style="font-size: 0.78rem; max-width: 260px;" title="{{ $pp->alasan }}">
                                            <i class="bi bi-chat-quote-fill text-primary me-1"></i>"{{ $pp->alasan }}"
                                        </div>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-success fw-semibold" style="font-size: 0.75rem;"
                                                    data-bs-toggle="modal" data-bs-target="#modalSetujui_{{ $pp->id }}" title="Setujui perpanjangan tanggal pengembalian">
                                                <i class="bi bi-check-lg"></i> Setujui
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-semibold" style="font-size: 0.75rem;"
                                                    data-bs-toggle="modal" data-bs-target="#modalTolak_{{ $pp->id }}" title="Tolak permohonan perpanjangan">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- MODAL ACTION SETUJUI & TOLAK PERPANJANGAN --}}
        @foreach($pendingPerpanjangans as $pp)
            {{-- Modal Setujui --}}
            <div class="modal fade" id="modalSetujui_{{ $pp->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.perpanjangan.setujui', $pp->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-success text-white">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-check-circle-fill me-2"></i>Setujui Perpanjangan Transaksi #{{ str_pad($pp->peminjaman_id, 5, '0', STR_PAD_LEFT) }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                @if($pp->isPerpanjanganTanggal())
                                    <p class="small text-secondary mb-2">
                                        Perubahan tanggal kembali: <strong>{{ \Carbon\Carbon::parse($pp->tanggal_kembali_lama)->translatedFormat('d F Y') }}</strong> &rarr; <strong>{{ \Carbon\Carbon::parse($pp->tanggal_kembali_baru)->translatedFormat('d F Y') }}</strong> (+{{ $pp->tambahanHari() }} hari).
                                    </p>
                                @endif

                                @if($pp->hasAlatTambahan())
                                    <div class="p-2.5 rounded border mb-3" style="background: #faf5ff; border-color: #d8b4fe !important;">
                                        <strong class="text-purple d-block small text-uppercase mb-1" style="color: #7e22ce; font-size: 0.72rem;">
                                            <i class="bi bi-cart-plus me-1"></i>Persetujuan Alat Ukur Tambahan:
                                        </strong>
                                        <div class="small text-secondary mb-1">Menyetujui akan memotong stok dan menggabungkan alat ke transaksi ini:</div>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($pp->getDaftarAlatTambahan() as $item)
                                                <li>
                                                    &bull; <strong>{{ $item['nama_alat'] }}</strong>: {{ $item['qty'] }} unit
                                                    @if($item['available_stock'] < $item['qty'])
                                                        <span class="badge bg-danger text-white ms-1">Stok Tidak Cukup! (Sisa: {{ $item['available_stock'] }})</span>
                                                    @else
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle ms-1">Stok Aman ({{ $item['available_stock'] }})</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="p-2.5 rounded bg-light border mb-3 small">
                                    <strong class="text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem;">Catatan Peminjam:</strong>
                                    "{{ $pp->alasan }}"
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-dark">Catatan Administrator (Opsional):</label>
                                    <textarea name="catatan_admin" class="form-control form-control-sm" rows="2" placeholder="Contoh: Disetujui. Pastikan alat dijaga dan dikembalikan tepat waktu setelah kalibrasi..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                                    <i class="bi bi-check2-circle me-1"></i> Ya, Setujui Perpanjangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal Tolak --}}
            <div class="modal fade" id="modalTolak_{{ $pp->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <form action="{{ route('admin.perpanjangan.tolak', $pp->id) }}" method="POST">
                            @csrf
                            <div class="modal-header bg-danger text-white">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-x-circle-fill me-2"></i>Tolak Perpanjangan Transaksi #{{ str_pad($pp->peminjaman_id, 5, '0', STR_PAD_LEFT) }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="small text-secondary mb-3">
                                    Batas tanggal kembali peminjaman akan tetap pada <strong>{{ \Carbon\Carbon::parse($pp->tanggal_kembali_lama)->translatedFormat('d F Y') }}</strong>.
                                </p>
                                <div class="p-2.5 rounded bg-light border mb-3 small">
                                    <strong class="text-secondary text-uppercase d-block mb-1" style="font-size: 0.7rem;">Catatan Peminjam:</strong>
                                    "{{ $pp->alasan }}"
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold text-dark">Alasan Penolakan untuk Peminjam <span class="text-danger">*</span>:</label>
                                    <textarea name="catatan_admin" class="form-control form-control-sm" rows="3" required minlength="5" maxlength="1000"
                                              placeholder="Jelaskan alasan penolakan, misalnya: Alat ukur dijadwalkan untuk kalibrasi rutin ISO oleh lab eksternal besok..."></textarea>
                                    <div class="form-text text-muted" style="font-size: 0.72rem;">Catatan ini akan dikirim dan dapat dibaca oleh peminjam.</div>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-sm fw-bold px-3">
                                    <i class="bi bi-x-circle me-1"></i> Tolak Permohonan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    {{-- Bottom Row: Table + Sidebar --}}
    <div class="row g-4">
        {{-- Recent Loans Table --}}
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color);">
                    <h6 class="fw-700 mb-0" style="font-weight: 700;">Transaksi Terbaru</h6>
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn-ghost btn btn-sm">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No. Transaksi</th>
                                <th>Departemen / PIC</th>
                                <th>Alat Ukur</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLoans as $loan)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-700" style="font-weight: 700; color: var(--primary);">
                                            #{{ str_pad($loan->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);">
                                            {{ \Carbon\Carbon::parse($loan->tanggal_pinjam)->translatedFormat('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 0.85rem;">{{ $loan->department }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                            <i class="bi bi-person me-1"></i>{{ $loan->pic }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.82rem; color: var(--text-secondary);">
                                            {{ $loan->details->pluck('tool.nama_alat')->take(2)->join(', ') }}
                                            @if($loan->details->count() > 2)
                                                <span style="color: var(--primary);">+{{ $loan->details->count() - 2 }} lainnya</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $loan->details->sum('qty') }}</td>
                                    <td class="text-center">
                                        @if($loan->status === 'dipinjam')
                                            <span class="badge-status badge-dipinjam">Dipinjam</span>
                                        @elseif($loan->status === 'menunggu_cek_teknisi')
                                            <span class="badge-status badge-menunggu">Cek Teknisi</span>
                                        @elseif($loan->status === 'selesai')
                                            <span class="badge-status badge-selesai">Selesai</span>
                                        @elseif($loan->status === 'rusak')
                                            <span class="badge-status badge-rusak">Rusak</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.peminjaman.show', $loan->id) }}"
                                           class="btn-ghost btn btn-sm" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding: 40px 20px;">
                                            <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                                            <div class="fw-semibold" style="color: var(--text-secondary);">Belum ada transaksi peminjaman</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="col-xl-4">
            {{-- Department Frequency --}}
            <div class="card mb-4">
                <div class="px-4 py-3" style="border-bottom: 1px solid var(--border-color);">
                    <h6 class="fw-700 mb-0" style="font-weight: 700;">
                        <i class="bi bi-building me-2" style="color: var(--primary);"></i>Frekuensi Departemen
                    </h6>
                </div>
                <div class="p-3">
                    @forelse($departmentStats as $i => $ds)
                        <div class="d-flex align-items-center justify-content-between py-2 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--border-color) !important;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 24px; height: 24px; border-radius: 6px; font-size: 0.72rem;
                                            background: {{ $i === 0 ? 'var(--primary)' : ($i === 1 ? '#475569' : '#e2e8f0') }};
                                            color: {{ $i < 2 ? '#ffffff' : '#475569' }};">
                                    {{ $i + 1 }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size: 0.85rem;">{{ $ds->department }}</div>
                                </div>
                            </div>
                            <span class="fw-bold" style="font-size: 0.85rem; color: var(--primary);">
                                {{ $ds->total }}x
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-secondary small">
                            Belum ada data transaksi
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="card p-3">
                <h6 class="fw-bold mb-3 px-1">
                    <i class="bi bi-lightning-charge me-2 text-primary"></i>Aksi Cepat
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.tools.create') }}" class="btn-ghost btn text-start d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-plus-circle text-primary"></i>
                        <span style="font-size: 0.85rem; font-weight: 600;">Tambah Alat Ukur</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn-ghost btn text-start d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-people" style="color: #7c3aed;"></i>
                        <span style="font-size: 0.85rem; font-weight: 600;">Kelola Pengguna ({{ $stats['total_users'] }})</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="btn-ghost btn text-start d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-file-earmark-bar-graph" style="color: #059669;"></i>
                        <span style="font-size: 0.85rem; font-weight: 600;">Laporan & Rekap</span>
                    </a>
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn-ghost btn text-start d-flex align-items-center gap-2 py-2">
                        <i class="bi bi-card-checklist" style="color: #d97706;"></i>
                        <span style="font-size: 0.85rem; font-weight: 600;">Semua Peminjaman</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
