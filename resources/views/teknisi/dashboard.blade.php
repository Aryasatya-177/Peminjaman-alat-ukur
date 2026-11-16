@extends('layouts.app')

@section('title', 'Dashboard Teknisi Kalibrasi & Inspeksi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-0" style="font-size: 1.4rem; letter-spacing: -0.02em;">
                <i class="bi bi-tools me-2 text-primary"></i>Dashboard Teknisi
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="btn-ghost btn btn-sm">
                <i class="bi bi-wrench me-1"></i> Perbaikan ({{ $perbaikanCount }})
            </a>
            <a href="{{ route('teknisi.antrian') }}" class="btn-primary-modern btn btn-sm">
                <i class="bi bi-inbox-fill me-1"></i> Antrian Cek ({{ $antrianCount }})
            </a>
        </div>
    </div>

    {{-- Kartu Metrik --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value" style="color: var(--status-borrowed);">{{ $antrianCount }}</div>
                <div class="stat-label">Antrian Cek</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-wrench-adjustable"></i></div>
                <div class="stat-value" style="color: var(--status-queue);">{{ $perbaikanCount }}</div>
                <div class="stat-label">Dalam Perbaikan</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon"><i class="bi bi-shield-exclamation"></i></div>
                <div class="stat-value" style="color: var(--status-danger);">{{ $konfirmasiCount }}</div>
                <div class="stat-label">Menunggu Konfirmasi</div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="stat-card stat-card-purple">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-value" style="color: var(--status-repair);">{{ $rusakBeratCount }}</div>
                <div class="stat-label">Rusak ≥ 3 Unit</div>
            </div>
        </div>
    </div>

    {{-- ALERT: JIKA ADA ALAT DALAM PERBAIKAN ATAU MENUNGGU KONFIRMASI --}}
    @if($perbaikanCount > 0)
        <div class="alert alert-info border shadow-sm d-flex justify-content-between align-items-center py-2 px-3 mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-tools text-info fs-5"></i>
                <span class="small"><strong>{{ $perbaikanCount }} transaksi</strong> dalam proses perbaikan teknisi.</span>
            </div>
            <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="btn btn-info btn-sm fw-bold py-1 px-3">
                Kelola Perbaikan
            </a>
        </div>
    @endif

    {{-- Antrian Pengecekan Mendesak --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-clock-history me-2 text-warning"></i>Antrian Menunggu Pemeriksaan
            </h6>
            <a href="{{ route('teknisi.antrian') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                Lihat Semua &raquo;
            </a>
        </div>
        <div class="card-body p-0">
            @if($antrianTerbaru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check2-all display-4 text-success d-block mb-2"></i>
                    <h6 class="fw-bold">Tidak ada antrian pengembalian saat ini</h6>
                    <small>Semua alat ukur yang diajukan kembali sudah selesai diperiksa.</small>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Transaksi</th>
                                <th>Departemen & PIC</th>
                                <th>Daftar Alat Ukur Presisi</th>
                                <th class="text-center">Total Qty</th>
                                <th>Tanggal Pinjam</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antrianTerbaru as $item)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        <a href="{{ route('user.pinjam.show', $item->id) }}" class="text-decoration-none">
                                            #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="small text-muted">{{ $item->updated_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $item->department }}</div>
                                        <small class="text-muted"><i class="bi bi-person-badge me-1"></i>PIC: {{ $item->pic }}</small>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($item->details as $d)
                                                <li>
                                                    <i class="bi bi-check2 text-primary me-1"></i>
                                                    <strong>{{ $d->tool->nama_alat ?? 'Alat' }}</strong> ({{ $d->qty }} unit)
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center fw-bold">{{ $item->details->sum('qty') }} unit</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('teknisi.cek', $item->id) }}" class="btn btn-warning btn-sm fw-bold px-3 shadow-sm">
                                            <i class="bi bi-clipboard2-check me-1"></i> Periksa
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Daftar Alat Sedang Dalam Perbaikan (Repair) --}}
    @if($perbaikanTerbaru->isNotEmpty())
        <div class="card shadow-sm mb-4 border-info">
            <div class="card-header bg-info-subtle py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-info-emphasis">
                    <i class="bi bi-wrench me-2"></i>Alat Dalam Perbaikan
                </h6>
                <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="btn btn-sm btn-info fw-bold">
                    Lihat Semua ({{ $perbaikanCount }}) &raquo;
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Transaksi</th>
                                <th>Departemen / PIC</th>
                                <th>Alat yang Diperbaiki</th>
                                <th class="text-center">Rusak / Total</th>
                                <th>Catatan Tindakan Teknisi</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($perbaikanTerbaru as $pItem)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        <a href="{{ route('user.pinjam.show', $pItem->id) }}">
                                             #{{ str_pad($pItem->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        @if($pItem->pengembalian?->tanda_kerusakan_berat)
                                            <div><span class="badge bg-danger text-white">RUSAK ≥ 3 UNIT</span></div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $pItem->department }}</div>
                                        <small class="text-muted">PIC: {{ $pItem->pic }}</small>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($pItem->details as $d)
                                                <li>{{ $d->tool->nama_alat ?? 'Alat' }} ({{ $d->qty }} unit)</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $pItem->pengembalian?->jumlah_rusak ?? $pItem->details->sum('qty') }} unit rusak</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ Str::limit($pItem->pengembalian?->catatan_perbaikan ?? $pItem->pengembalian?->catatan_teknisi, 60) }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="btn btn-success btn-sm fw-bold">
                                            <i class="bi bi-wrench-adjustable-circle me-1"></i> Benarkan Alat
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

    {{-- Riwayat Inspeksi Terbaru --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-check2-circle me-2 text-success"></i>Riwayat Pemeriksaan Terakhir
            </h6>
            <a href="{{ route('teknisi.history') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                Lihat Semua &raquo;
            </a>
        </div>
        <div class="card-body p-0">
            @if($inspeksiTerbaru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <p class="mb-0">Belum ada riwayat pengecekan alat yang tercatat.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">No. Transaksi</th>
                                <th>Departemen</th>
                                <th>Tanggal Selesai Cek</th>
                                <th class="text-center">Hasil Kondisi</th>
                                <th>Status Repair / Konfirmasi</th>
                                <th class="text-end pe-3">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inspeksiTerbaru as $insp)
                                <tr>
                                    <td class="ps-3 fw-bold">
                                        #{{ str_pad($insp->peminjaman_id, 5, '0', STR_PAD_LEFT) }}
                                        @if($insp->tanda_kerusakan_berat)
                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">RUSAK ≥ 3</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $insp->peminjaman->department ?? '-' }}</div>
                                        <small class="text-muted">PIC: {{ $insp->peminjaman->pic ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($insp->tanggal_dikembalikan)->translatedFormat('d M Y') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($insp->kondisi_alat === 'baik')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Baik</span>
                                        @else
                                            <span class="badge bg-danger"><i class="bi bi-exclamation-octagon me-1"></i>Rusak ({{ $insp->jumlah_rusak }} unit)</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($insp->status_repair === 'selesai_direpair')
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                <i class="bi bi-check-all me-1"></i>Telah Dibenarkan
                                            </span>
                                        @elseif($insp->status_repair === 'dalam_perbaikan')
                                            <span class="badge bg-info-subtle text-info border border-info">
                                                <i class="bi bi-wrench me-1"></i>Sedang Diperbaiki
                                            </span>
                                        @elseif($insp->status_repair === 'tidak_bisa_direpair')
                                            <span class="badge bg-danger-subtle text-danger border border-danger">
                                                <i class="bi bi-x-circle me-1"></i>Tidak Bisa Direpair
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('user.pinjam.show', $insp->peminjaman_id) }}" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
