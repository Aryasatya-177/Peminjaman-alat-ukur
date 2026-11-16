@extends('layouts.app')

@section('title', 'Manajemen Antrian & Perbaikan Alat - Teknisi')
@section('page_title', 'Antrian & Perbaikan')

@push('styles')
<style>
    .queue-tab-nav {
        display: flex;
        gap: 8px;
        background: #ffffff;
        padding: 8px;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        overflow-x: auto;
    }
    .queue-tab-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .queue-tab-link:hover {
        background: #f1f5f9;
        color: var(--text-primary);
    }
    .queue-tab-link.active-antrian {
        background: var(--status-borrowed);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }
    .queue-tab-link.active-perbaikan {
        background: var(--status-queue);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }
    .queue-tab-link.active-konfirmasi {
        background: var(--status-danger);
        color: #ffffff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }
    .queue-tab-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.72rem;
        font-weight: 700;
    }
    .sop-card {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
</style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-tools me-1"></i>Ruang QC & Kalibrasi
                </span>
                <span class="text-muted small">Peran: <strong>Teknisi Presisi</strong></span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-inbox-fill text-warning me-2"></i>Antrian Pengecekan & Perbaikan Alat
            </h1>
            <p class="text-muted mb-0">Kelola inspeksi fisik pengembalian, perbaikan alat rusak di bengkel kalibrasi, dan eskalasi konfirmasi aset</p>
        </div>
        <div class="d-flex gap-2 no-print flex-wrap">
            <a href="{{ route('teknisi.history') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-journal-check me-1"></i> Riwayat Inspeksi
            </a>
            <a href="{{ route('teknisi.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Teknisi
            </a>
        </div>
    </div>

    {{-- Workload Mini Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <a href="{{ route('teknisi.antrian', ['tab' => 'antrian']) }}" class="text-decoration-none">
                <div class="card p-3 border-0 shadow-sm h-100 {{ ($tab ?? 'antrian') === 'antrian' ? 'border-start border-warning border-4' : '' }}" style="background: #fffdf5;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">1. Antrian Menunggu Cek</div>
                            <div class="fs-3 fw-bold text-warning">{{ $counts['antrian'] ?? 0 }} Transaksi</div>
                            <div class="small text-muted">Perlu inspeksi fisik segera</div>
                        </div>
                        <div class="p-3 bg-warning-subtle text-warning rounded-3 fs-3">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}" class="text-decoration-none">
                <div class="card p-3 border-0 shadow-sm h-100 {{ ($tab ?? '') === 'perbaikan' ? 'border-start border-primary border-4' : '' }}" style="background: #f8faff;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">2. Dalam Perbaikan (Repair)</div>
                            <div class="fs-3 fw-bold text-primary">{{ $counts['perbaikan'] ?? 0 }} Transaksi</div>
                            <div class="small text-muted">Sedang dikalibrasi di bengkel</div>
                        </div>
                        <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-3">
                            <i class="bi bi-wrench-adjustable"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('teknisi.antrian', ['tab' => 'konfirmasi']) }}" class="text-decoration-none">
                <div class="card p-3 border-0 shadow-sm h-100 {{ ($tab ?? '') === 'konfirmasi' ? 'border-start border-danger border-4' : '' }}" style="background: #fff8f8;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small fw-bold text-uppercase">3. Butuh Konfirmasi Fatal</div>
                            <div class="fs-3 fw-bold text-danger">{{ $counts['konfirmasi'] ?? 0 }} Transaksi</div>
                            <div class="small text-muted">Menunggu keputusan Admin & User</div>
                        </div>
                        <div class="p-3 bg-danger-subtle text-danger rounded-3 fs-3">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Collapsible Quick SOP Checklist Banner --}}
    <div class="sop-card p-3 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clipboard2-pulse-fill text-primary fs-5"></i>
                <div>
                    <strong class="text-dark small d-block">Panduan Standar Operasional Prosedur (SOP) Kalibrasi:</strong>
                    <span class="text-muted small">Pastikan alat melalui 3 tahap inspeksi sebelum menentukan kondisi laik atau rusak.</span>
                </div>
            </div>
            <button class="btn btn-sm btn-link text-decoration-none p-0 fw-semibold text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#sopDetails">
                Lihat Checklist SOP <i class="bi bi-chevron-down ms-1"></i>
            </button>
        </div>
        <div class="collapse mt-3 pt-3 border-top" id="sopDetails">
            <div class="row g-2 text-dark small">
                <div class="col-md-4">
                    <div class="p-2 bg-white rounded border">
                        <strong class="text-primary d-block mb-1">1. Inspeksi Visual</strong>
                        Periksa kebersihan anvil/measuring face, goresan pada bodi, kaca indikator dial utuh, dan keausan fisik.
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 bg-white rounded border">
                        <strong class="text-primary d-block mb-1">2. Uji Kelancaran Mekanis</strong>
                        Putar ratchet & thimble micrometer, geser slider caliper, pastikan jarum dial bergerak bebas tanpa macet.
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-2 bg-white rounded border">
                        <strong class="text-primary d-block mb-1">3. Uji Titik Nol (Zero-Point)</strong>
                        Cek titik nol dengan gauge block standar. Jika deviasi melebihi toleransi ISO, lakukan re-kalibrasi/repair.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigasi Tab Manajemen --}}
    <div class="queue-tab-nav mb-4">
        <a class="queue-tab-link {{ ($tab ?? 'antrian') === 'antrian' ? 'active-antrian' : '' }}"
           href="{{ route('teknisi.antrian', ['tab' => 'antrian']) }}">
            <i class="bi bi-hourglass-split"></i>
            <span>Menunggu Pengecekan</span>
            <span class="queue-tab-badge {{ ($tab ?? 'antrian') === 'antrian' ? 'bg-white text-dark' : 'bg-warning-subtle text-warning-emphasis' }}">
                {{ $counts['antrian'] ?? 0 }}
            </span>
        </a>

        <a class="queue-tab-link {{ ($tab ?? '') === 'perbaikan' ? 'active-perbaikan' : '' }}"
           href="{{ route('teknisi.antrian', ['tab' => 'perbaikan']) }}">
            <i class="bi bi-wrench-adjustable"></i>
            <span>Dalam Perbaikan (Repair)</span>
            <span class="queue-tab-badge {{ ($tab ?? '') === 'perbaikan' ? 'bg-white text-dark' : 'bg-primary-subtle text-primary' }}">
                {{ $counts['perbaikan'] ?? 0 }}
            </span>
        </a>

        <a class="queue-tab-link {{ ($tab ?? '') === 'konfirmasi' ? 'active-konfirmasi' : '' }}"
           href="{{ route('teknisi.antrian', ['tab' => 'konfirmasi']) }}">
            <i class="bi bi-shield-exclamation"></i>
            <span>Menunggu Konfirmasi Admin & User</span>
            <span class="queue-tab-badge {{ ($tab ?? '') === 'konfirmasi' ? 'bg-white text-dark' : 'bg-danger-subtle text-danger' }}">
                {{ $counts['konfirmasi'] ?? 0 }}
            </span>
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('teknisi.antrian') }}" class="row g-2 align-items-center">
            <input type="hidden" name="tab" value="{{ $tab ?? 'antrian' }}">
            <div class="col-md-9">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari transaksi, nama alat (Dial Gauge, Caliper...), departemen, atau PIC..."
                        value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Saring Data
                </button>
                @if(request('search'))
                    <a href="{{ route('teknisi.antrian', ['tab' => $tab ?? 'antrian']) }}" class="btn btn-outline-secondary btn-sm" title="Reset pencarian">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Antrian --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    @if(($tab ?? 'antrian') === 'perbaikan')
                        <i class="bi bi-wrench text-primary"></i>Daftar Alat Sedang Dalam Proses Perbaikan Bengkel
                    @elseif(($tab ?? '') === 'konfirmasi')
                        <i class="bi bi-exclamation-octagon text-danger"></i>Alat Rusak Tidak Bisa Di-repair (Eskalasi Keputusan)
                    @else
                        <i class="bi bi-hourglass-split text-warning"></i>Alat Menunggu Verifikasi Fisik & Kalibrasi
                    @endif
                </h5>
                <small class="text-muted">Total {{ $antrian->total() }} transaksi pada antrian ini</small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchQueue" class="form-control border-start-0 ps-0" placeholder="Cari cepat di tabel...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($antrian->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle-fill display-4 text-success d-block mb-3"></i>
                    <h5 class="fw-bold text-dark">Tidak Ada Transaksi pada Kategori Ini</h5>
                    <p class="small text-muted mb-0">Semua item telah diperiksa atau diproses dengan baik.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern" id="queueTable">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 12%;">No. Transaksi</th>
                                <th style="width: 20%;">Departemen & PIC</th>
                                <th style="width: 28%;">Alat Ukur Presisi</th>
                                <th class="text-center" style="width: 12%;">Kondisi / Unit</th>
                                <th style="width: 15%;">Status / Waktu</th>
                                <th class="text-end pe-3" style="width: 13%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($antrian as $item)
                                <tr class="queue-row">
                                    <td class="ps-3">
                                        <a href="{{ route('user.pinjam.show', $item->id) }}" class="fw-bold text-decoration-none">
                                            #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="small text-muted" style="font-size: 0.72rem;">
                                            Diajukan: {{ $item->updated_at->diffForHumans() }}
                                        </div>
                                        @if($item->pengembalian && $item->pengembalian->tanda_kerusakan_berat)
                                            <span class="badge bg-danger text-white mt-1 small" style="font-size: 0.65rem;">
                                                <i class="bi bi-exclamation-triangle-fill"></i> RUSAK ≥ 3 UNIT
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->department }}</div>
                                        <div class="small text-secondary"><i class="bi bi-person me-1"></i>PIC: <strong>{{ $item->pic }}</strong></div>
                                        @if($item->user)
                                            <div class="text-muted" style="font-size: 0.7rem;">Akun: {{ $item->user->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($item->details as $d)
                                                <li class="mb-1">
                                                    <i class="bi bi-check2 text-primary me-1"></i>
                                                    <strong>{{ $d->tool->nama_alat ?? 'Alat' }}</strong>
                                                    <span class="badge bg-light text-dark border ms-1 font-monospace">
                                                        {{ $d->tool->kode_alat ?? '-' }} : {{ $d->qty }} unit
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold text-primary">{{ $item->details->sum('qty') }} unit</div>
                                        @if($item->pengembalian)
                                            <div class="d-flex justify-content-center gap-1 mt-1 flex-wrap">
                                                @if($item->pengembalian->jumlah_baik > 0)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.7rem;">
                                                        Baik: {{ $item->pengembalian->jumlah_baik }}
                                                    </span>
                                                @endif
                                                @if($item->pengembalian->jumlah_rusak > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.7rem;">
                                                        Rusak: {{ $item->pengembalian->jumlah_rusak }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status === 'menunggu_cek_teknisi')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i>Menunggu Cek
                                            </span>
                                        @elseif($item->status === 'dalam_perbaikan')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                                <i class="bi bi-wrench me-1"></i>Dalam Perbaikan
                                            </span>
                                        @elseif($item->status === 'menunggu_konfirmasi')
                                            <span class="badge bg-danger text-white px-2 py-1">
                                                <i class="bi bi-shield-exclamation me-1"></i>Butuh Konfirmasi
                                            </span>
                                            <div class="small mt-1 text-muted" style="font-size: 0.7rem;">
                                                Admin: {!! $item->pengembalian?->konfirmasi_admin ? '<span class="text-success fw-bold">✓ Sudah</span>' : '<span class="text-danger fw-bold">✗ Belum</span>' !!} |
                                                User: {!! $item->pengembalian?->konfirmasi_user ? '<span class="text-success fw-bold">✓ Sudah</span>' : '<span class="text-danger fw-bold">✗ Belum</span>' !!}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        @if($item->status === 'menunggu_cek_teknisi')
                                            <a href="{{ route('teknisi.cek', $item->id) }}" class="btn btn-warning btn-sm fw-semibold rounded-pill px-3 shadow-sm text-dark">
                                                <i class="bi bi-clipboard2-check me-1"></i> Periksa Fisik
                                            </a>
                                        @elseif($item->status === 'dalam_perbaikan')
                                            <button type="button" class="btn btn-success btn-sm fw-semibold rounded-pill px-3 shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#modalBenarkan{{ $item->id }}">
                                                <i class="bi bi-wrench-adjustable-circle me-1"></i> Benarkan Alat
                                            </button>

                                            {{-- Modal Benarkan / Selesaikan Perbaikan --}}
                                            <div class="modal fade" id="modalBenarkan{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered text-start">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('teknisi.repair.selesai', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-success text-white">
                                                                <h6 class="modal-title fw-bold">
                                                                    <i class="bi bi-wrench-adjustable-circle me-2"></i>Selesaikan Perbaikan (Benarkan Alat)
                                                                </h6>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                                    <strong class="text-dark">Transaksi #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                                                    <span class="badge bg-secondary font-monospace">{{ $item->department }}</span>
                                                                </div>
                                                                <div class="alert alert-success-subtle border border-success-subtle p-3 rounded-3 small mb-3">
                                                                    <div class="fw-bold text-success mb-1">
                                                                        <i class="bi bi-check-circle-fill me-1"></i>Pemulihan ke Inventaris Siap Pakai
                                                                    </div>
                                                                    Unit rusak (<strong>{{ $item->pengembalian?->jumlah_rusak ?? $item->details->sum('qty') }} unit</strong>) yang berhasil Anda perbaiki akan <strong>otomatis dikembalikan ke stok inventaris</strong>.
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small text-dark">
                                                                        Catatan Teknis Tindakan Perbaikan & Kalibrasi: <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea name="catatan_perbaikan" rows="3" class="form-control form-control-sm" required
                                                                        placeholder="Contoh: Pembersihan debu pada gigi spindle, setting ulang zero point jarum dial, penggantian baterai digital, uji akurasi dengan block gauge. Hasil normal tanpa deviasi."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light py-2">
                                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-success btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                                                    <i class="bi bi-check2-circle me-1"></i> Simpan & Pulihkan Stok
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('user.pinjam.show', $item->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="bi bi-eye me-1"></i> Rincian
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $antrian->count() }} data pada antrian ini
                    </div>
                    <div>
                        {{ $antrian->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveInput = document.getElementById('liveSearchQueue');
        if (liveInput) {
            liveInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.queue-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
