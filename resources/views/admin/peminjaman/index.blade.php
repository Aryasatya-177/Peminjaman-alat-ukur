@extends('layouts.app')

@section('title', 'Monitoring Transaksi Peminjaman Alat Ukur - Admin')
@section('page_title', 'Monitoring Peminjaman')

@push('styles')
<style>
    .filter-chip-bar {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: thin;
    }
    .filter-chip-bar::-webkit-scrollbar { height: 4px; }
    .filter-chip-bar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-secondary);
        white-space: nowrap;
        transition: all 0.15s ease;
    }
    .status-chip:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #f8fafc;
    }
    .status-chip.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }
    .status-chip .chip-count {
        background: rgba(0,0,0,0.06);
        padding: 2px 7px;
        border-radius: 4px;
        font-size: 0.72rem;
    }
    .status-chip.active .chip-count {
        background: rgba(255,255,255,0.2);
        color: #ffffff;
    }
    .pic-avatar {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: var(--primary);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .overdue-pulse {
        animation: pulseDanger 1.8s infinite;
    }
    @keyframes pulseDanger {
        0% { transform: scale(1); }
        50% { transform: scale(1.04); }
        100% { transform: scale(1); }
    }
</style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-broadcast me-1"></i>Live Monitoring Tool Crib
                </span>
                <span class="text-muted small">Total: <strong>{{ $peminjaman->total() }} transaksi tercatat</strong></span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-card-checklist text-primary me-2"></i>Monitoring Transaksi Peminjaman
            </h1>
            <p class="text-muted mb-0">Pantau peredaran seluruh alat ukur presisi lintas departemen, status jatuh tempo pengembalian, dan eskalasi kalibrasi</p>
        </div>
        <div class="d-flex gap-2 no-print flex-wrap">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-file-earmark-bar-graph me-1"></i> Laporan Bulanan
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Admin
            </a>
        </div>
    </div>

    {{-- Quick Status Filter Chips --}}
    <div class="filter-chip-bar mb-3">
        @php
            $activeStatus = request('status', '');
            $baseParams = request()->except(['status', 'page']);
        @endphp
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => ''])) }}"
           class="status-chip {{ $activeStatus === '' ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Semua Status
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'dipinjam'])) }}"
           class="status-chip {{ $activeStatus === 'dipinjam' ? 'active' : '' }}">
            <i class="bi bi-clock-history text-warning"></i> Sedang Dipinjam
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'menunggu_cek_teknisi'])) }}"
           class="status-chip {{ $activeStatus === 'menunggu_cek_teknisi' ? 'active' : '' }}">
            <i class="bi bi-hourglass-split text-info"></i> Menunggu Cek Teknisi
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'dalam_perbaikan'])) }}"
           class="status-chip {{ $activeStatus === 'dalam_perbaikan' ? 'active' : '' }}">
            <i class="bi bi-wrench text-primary"></i> Dalam Perbaikan
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'menunggu_konfirmasi'])) }}"
           class="status-chip {{ $activeStatus === 'menunggu_konfirmasi' ? 'active' : '' }}">
            <i class="bi bi-shield-exclamation text-danger"></i> Butuh Konfirmasi
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'selesai'])) }}"
           class="status-chip {{ $activeStatus === 'selesai' ? 'active' : '' }}">
            <i class="bi bi-check2-circle text-success"></i> Selesai (Baik)
        </a>
        <a href="{{ route('admin.peminjaman.index', array_merge($baseParams, ['status' => 'rusak'])) }}"
           class="status-chip {{ $activeStatus === 'rusak' ? 'active' : '' }}">
            <i class="bi bi-x-octagon text-danger"></i> Rusak / Afkir
        </a>
    </div>

    {{-- Filter Bar Komprehensif --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('admin.peminjaman.index') }}" class="row g-2 align-items-end">
            {{-- Keep existing status filter if clicked via chip --}}
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="col-lg-4 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-search me-1"></i>Pencarian Bebas</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="No. ID, PIC, Nama Alat, Kode..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-building me-1"></i>Departemen</label>
                <select name="department" class="form-select form-select-sm">
                    <option value="">-- Semua Departemen --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted mb-1"><i class="bi bi-calendar3 me-1"></i>Tanggal Pinjam (Mulai)</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>

            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Saring
                </button>
                @if(request()->hasAny(['search', 'status', 'department', 'start_date']))
                    <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Semua Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Monitoring Peminjaman --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-journal-text text-primary"></i>Daftar Transaksi Peminjaman
                </h5>
                <small class="text-muted">Menampilkan {{ $peminjaman->firstItem() ?? 0 }} - {{ $peminjaman->lastItem() ?? 0 }} dari {{ $peminjaman->total() }} data</small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchLoans" class="form-control border-start-0 ps-0" placeholder="Cari di halaman ini...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($peminjaman->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Tidak Ada Data Peminjaman</h5>
                    <p class="small text-muted mb-3">Tidak ditemukan catatan transaksi sesuai filter yang diterapkan.</p>
                    @if(request()->hasAny(['search', 'status', 'department', 'start_date']))
                        <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Seluruh Filter
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern" id="loansTable">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 11%;">No. ID</th>
                                <th style="width: 20%;">Peminjam & Departemen</th>
                                <th style="width: 27%;">Alat Ukur Presisi</th>
                                <th class="text-center" style="width: 8%;">Qty</th>
                                <th style="width: 18%;">Jadwal Pinjam & Batas</th>
                                <th class="text-center" style="width: 11%;">Status</th>
                                <th class="text-end pe-3" style="width: 5%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $p)
                                @php
                                    $tglPinjam = \Carbon\Carbon::parse($p->tanggal_pinjam);
                                    $tglKembali = \Carbon\Carbon::parse($p->tanggal_kembali)->endOfDay();
                                    $now = now();
                                    $isOverdue = $p->status === 'dipinjam' && $now->gt($tglKembali);
                                    $isDueToday = $p->status === 'dipinjam' && $tglKembali->isToday();
                                    $daysRemaining = $now->diffInDays($tglKembali, false);
                                @endphp
                                <tr class="loan-row">
                                    <td class="ps-3">
                                        <a href="{{ route('admin.peminjaman.show', $p->id) }}" class="fw-bold text-decoration-none" style="font-size: 0.95rem;">
                                            #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="small text-muted font-monospace" style="font-size: 0.7rem;">
                                            {{ $p->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="pic-avatar" title="{{ $p->pic }}">
                                                {{ strtoupper(substr($p->pic, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $p->department }}</div>
                                                <div class="small text-secondary"><i class="bi bi-person me-1"></i>PIC: <strong>{{ $p->pic }}</strong></div>
                                                @if($p->user)
                                                    <div class="text-muted" style="font-size: 0.7rem;">Akun: {{ $p->user->name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($p->details as $d)
                                                <li class="mb-1">
                                                    <i class="bi bi-check2 text-primary me-1"></i>
                                                    <strong>{{ $d->tool->nama_alat ?? 'Alat' }}</strong>
                                                    <span class="badge bg-light text-dark border ms-1 font-monospace">
                                                        {{ $d->tool->kode_alat ?? '-' }} ({{ $d->qty }} unit)
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center fw-bold text-primary fs-6">
                                        {{ $p->details->sum('qty') }} unit
                                    </td>
                                    <td>
                                        <div class="small text-success fw-semibold">
                                            <i class="bi bi-calendar-check me-1"></i>Pinjam: {{ $tglPinjam->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="small {{ $isOverdue ? 'text-danger fw-bold' : 'text-danger' }}">
                                            <i class="bi bi-calendar-x me-1"></i>Batas: {{ $tglKembali->translatedFormat('d M Y') }}
                                        </div>

                                        {{-- Indikator Jatuh Tempo --}}
                                        @if($isOverdue)
                                            <span class="badge bg-danger text-white overdue-pulse mt-1" style="font-size: 0.68rem;">
                                                <i class="bi bi-exclamation-octagon-fill me-1"></i>TERLAMBAT {{ abs($daysRemaining) }} HARI
                                            </span>
                                        @elseif($isDueToday)
                                            <span class="badge bg-warning text-dark mt-1" style="font-size: 0.68rem;">
                                                <i class="bi bi-clock-fill me-1"></i>JATUH TEMPO HARI INI
                                            </span>
                                        @elseif($p->status === 'dipinjam')
                                            <span class="badge bg-light text-muted border mt-1" style="font-size: 0.68rem;">
                                                Sisa {{ max(1, $daysRemaining) }} hari
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($p->status === 'dipinjam')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                <i class="bi bi-arrow-repeat me-1"></i>Dipinjam
                                            </span>
                                        @elseif($p->status === 'menunggu_cek_teknisi')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i>Cek Teknisi
                                            </span>
                                        @elseif($p->status === 'dalam_perbaikan')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                                <i class="bi bi-wrench me-1"></i>Perbaikan
                                            </span>
                                        @elseif($p->status === 'menunggu_konfirmasi')
                                            <span class="badge bg-danger text-white px-2 py-1">
                                                <i class="bi bi-shield-exclamation me-1"></i>Konfirmasi
                                            </span>
                                        @elseif($p->status === 'rusak_dikonfirmasi')
                                            <span class="badge bg-secondary text-white px-2 py-1">
                                                <i class="bi bi-check2-all me-1"></i>Rusak Afkir
                                            </span>
                                        @elseif($p->status === 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Selesai (Baik)
                                            </span>
                                        @elseif($p->status === 'rusak')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i>Rusak
                                            </span>
                                        @endif

                                        @if($p->pengembalian?->tanda_kerusakan_berat)
                                            <div>
                                                <span class="badge bg-danger text-white mt-1" style="font-size: 0.65rem;">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> RUSAK ≥ 3
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.peminjaman.show', $p->id) }}"
                                           class="btn btn-sm btn-outline-primary rounded-circle"
                                           style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                           title="Lihat Rincian / Tanda Terima">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $peminjaman->count() }} data pada halaman ini
                    </div>
                    <div>
                        {{ $peminjaman->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveInput = document.getElementById('liveSearchLoans');
        if (liveInput) {
            liveInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.loan-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
