@extends('layouts.app')

@section('title', 'Laporan Bulanan Peminjaman Alat Ukur - Gauge Hub')
@section('page_title', 'Laporan Bulanan')

@push('styles')
<style>
    .report-filter-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }
    .period-chip {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-secondary);
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .period-chip:hover {
        background: #f8fafc;
        color: var(--primary);
        border-color: var(--border-dark);
    }
    .period-chip.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.1);
    }
    .chart-container-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        height: 100%;
    }
    .util-progress-bar {
        height: 6px;
        border-radius: 4px;
        background-color: #f1f5f9;
        overflow: hidden;
    }
    .util-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
    }
    @media print {
        .no-print, .btn, .period-chip, .pagination, form { display: none !important; }
        .card, .stat-card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .table { font-size: 11px !important; }
    }
</style>
@endpush

@section('content')
    {{-- Header Halaman --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 fw-bold mb-0 text-dark">
                <i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Laporan Sirkulasi Alat
            </h1>
            <small class="text-muted">Periode: <strong class="text-dark">{{ $periodText }}</strong></small>
        </div>
        <div class="d-flex gap-2 no-print">
            <button type="button" onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
            <a href="{{ route('admin.reports.word', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-file-earmark-word me-1"></i> Word
            </a>
            <a href="{{ route('admin.reports.pdf', request()->query()) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    {{-- Filter Periode & Departemen --}}
    <div class="report-filter-card p-3 mb-4">
        @php
            $curM = (int) now()->month;
            $curY = (int) now()->year;
            $prevM = (int) now()->subMonth()->month;
            $prevY = (int) now()->subMonth()->year;
            $todayStr = now()->toDateString();
            $isToday = ($filterMode === 'range' && $selectedDays == 1) || ($filterMode === 'range' && $startDate === $todayStr && $endDate === $todayStr);
            $is2Days = ($filterMode === 'range' && $selectedDays == 2);
            $is3Days = ($filterMode === 'range' && $selectedDays == 3);
            $is7Days = ($filterMode === 'range' && $selectedDays == 7);
            $is30Days = ($filterMode === 'range' && $selectedDays == 30);
            $isCurrentMonth = ($filterMode === 'monthly' && $selectedMonth == $curM && $selectedYear == $curY);
            $isPrevMonth = ($filterMode === 'monthly' && $selectedMonth == $prevM && $selectedYear == $prevY);
        @endphp

        {{-- Preset Cepat --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div class="d-flex gap-1 flex-wrap">
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'range', 'days' => 1, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $isToday ? 'active' : '' }}">Hari Ini</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'range', 'days' => 2, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $is2Days ? 'active' : '' }}">2 Hari</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'range', 'days' => 3, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $is3Days ? 'active' : '' }}">3 Hari</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'range', 'days' => 7, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $is7Days ? 'active' : '' }}">7 Hari</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'range', 'days' => 30, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $is30Days ? 'active' : '' }}">30 Hari</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'monthly', 'month' => $curM, 'year' => $curY, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $isCurrentMonth ? 'active' : '' }}">Bulan Ini</a>
                <a href="{{ route('admin.reports.index', ['filter_mode' => 'monthly', 'month' => $prevM, 'year' => $prevY, 'department' => $selectedDepartment, 'status' => $selectedStatus]) }}"
                   class="period-chip {{ $isPrevMonth ? 'active' : '' }}">Bulan Lalu</a>
            </div>
            @if(request()->hasAny(['filter_mode', 'days', 'start_date', 'end_date', 'month', 'year', 'department', 'status']))
                <a href="{{ route('admin.reports.index') }}" class="small text-secondary text-decoration-none">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </a>
            @endif
        </div>

        {{-- Form Filter Tanggal & Opsi --}}
        <form method="GET" action="{{ route('admin.reports.index') }}">
            <input type="hidden" name="filter_mode" value="range">
            <div class="row g-2 align-items-end">
                <div class="col-lg-3 col-md-6 col-6">
                    <label class="form-label small text-muted mb-1">Dari</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-lg-3 col-md-6 col-6">
                    <label class="form-label small text-muted mb-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-sm">
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <label class="form-label small text-muted mb-1">Departemen</label>
                    <select name="department" class="form-select form-select-sm">
                        <option value="all">Semua</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $selectedDepartment == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <label class="form-label small text-muted mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all">Semua</option>
                        <option value="dipinjam" {{ $selectedStatus == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="menunggu_cek_teknisi" {{ $selectedStatus == 'menunggu_cek_teknisi' ? 'selected' : '' }}>Menunggu Cek</option>
                        <option value="dalam_perbaikan" {{ $selectedStatus == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        <option value="selesai" {{ $selectedStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="rusak" {{ $selectedStatus == 'rusak' ? 'selected' : '' }}>Rusak</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-4 col-12">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-semibold">
                        <i class="bi bi-funnel me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Ringkasan Eksekutif KPI --}}
    @php
        $healthRate = $totalTransaksi > 0 ? round(($selesaiBaik / $totalTransaksi) * 100) : 100;
        $damageRate = $totalTransaksi > 0 ? round(($rusak / $totalTransaksi) * 100) : 0;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                <div class="stat-value">{{ $totalTransaksi }}</div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-sub">{{ $masihDipinjam }} aktif / sirkulasi periode ini</div>
                <div class="stat-bg-icon"><i class="bi bi-journal-text"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-rulers"></i></div>
                <div class="stat-value">{{ $totalUnitAlat }}</div>
                <div class="stat-label">Total Unit Dipinjam</div>
                <div class="stat-sub">Sirkulasi unit fisik di lapangan</div>
                <div class="stat-bg-icon"><i class="bi bi-rulers"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-shield-check"></i></div>
                <div class="stat-value">{{ $selesaiBaik }}</div>
                <div class="stat-label">Kondisi Baik (Selesai)</div>
                <div class="stat-sub">{{ $healthRate }}% tingkat kelaikan alat</div>
                <div class="stat-bg-icon"><i class="bi bi-shield-check"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card {{ $rusak > 0 ? 'stat-card-danger' : 'stat-card-purple' }}">
                <div class="stat-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <div class="stat-value">{{ $rusak }}</div>
                <div class="stat-label">Kerusakan / Cacat Fisik</div>
                <div class="stat-sub">{{ $damageRate }}% rasio temuan masalah</div>
                <div class="stat-bg-icon"><i class="bi bi-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>

    {{-- Visualisasi Grafis Interaktif (Chart.js) --}}
    @if($totalTransaksi > 0)
    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="chart-container-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Proporsi Kondisi Alat
                    </h6>
                    <span class="badge bg-light text-muted border">{{ $filterMode === 'monthly' ? 'Bulan Ini' : 'Periode Terpilih' }}</span>
                </div>
                <div style="position: relative; height: 230px;">
                    <canvas id="kondisiChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="chart-container-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-bar-chart-line text-primary me-2"></i>Distribusi Unit per Departemen
                    </h6>
                    <span class="badge bg-light text-muted border">Top Departemen</span>
                </div>
                <div style="position: relative; height: 230px;">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Rekapitulasi Berdasarkan Jenis Alat & Departemen --}}
    <div class="row g-4 mb-4">
        {{-- Rekapitulasi Berdasarkan Jenis Alat Ukur --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-tools text-primary me-2"></i>Utilitas per Jenis Alat Ukur Presisi
                    </h5>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $alatStats->count() }} Jenis</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern">
                            <thead>
                                <tr>
                                    <th class="ps-3">Nama & Kode Alat Ukur</th>
                                    <th class="text-center">Frekuensi Pinjam</th>
                                    <th class="text-center">Total Unit</th>
                                    <th style="width: 25%;">Utilitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxQty = $alatStats->max('total_qty') ?: 1;
                                @endphp
                                @forelse($alatStats as $index => $as)
                                    @php
                                        $percent = round(($as->total_qty / $maxQty) * 100);
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary font-monospace">{{ $as->kode_alat }}</span>
                                                <div>
                                                    <strong class="text-dark d-block">{{ $as->nama_alat }}</strong>
                                                    @if($index === 0)
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle" style="font-size: 0.65rem;">
                                                            <i class="bi bi-trophy-fill me-1"></i>Paling Banyak Dipinjam
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $as->total_dipinjam_kali }}x</span>
                                        </td>
                                        <td class="text-center fw-bold text-primary">{{ $as->total_qty }} unit</td>
                                        <td class="pe-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="util-progress-bar flex-grow-1">
                                                    <div class="util-progress-fill bg-primary" style="width: {{ $percent }}%;"></div>
                                                </div>
                                                <small class="text-muted fw-semibold" style="font-size: 0.7rem; width: 32px;">{{ $percent }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle me-1"></i> Tidak ada data peminjaman di periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rekapitulasi Berdasarkan Departemen --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-building text-primary me-2"></i>Distribusi per Departemen
                    </h5>
                    <span class="badge bg-info-subtle text-info border border-info-subtle">{{ count($deptStats) }} Dept</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-modern">
                            <thead>
                                <tr>
                                    <th class="ps-3">Departemen</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-center">Total Unit</th>
                                    <th class="text-center">Baik</th>
                                    <th class="text-center">Rusak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deptStats as $deptName => $ds)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="fw-bold text-dark d-block">{{ $deptName }}</span>
                                            <span class="small text-muted">{{ $ds['aktif'] }} transaksi aktif</span>
                                        </td>
                                        <td class="text-center fw-semibold">{{ $ds['total_transaksi'] }}</td>
                                        <td class="text-center fw-bold text-primary">{{ $ds['total_unit'] }} unit</td>
                                        <td class="text-center">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                {{ $ds['selesai'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($ds['rusak'] > 0)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold">
                                                    {{ $ds['rusak'] }}
                                                </span>
                                            @else
                                                <span class="text-muted small">0</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-info-circle me-1"></i> Tidak ada data peminjaman di periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Log Rincian Transaksi Bulan Ini dengan Live Search --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-table text-primary"></i>Rincian Log Transaksi Periode Ini
                </h5>
                <small class="text-muted">Total {{ $peminjaman->count() }} transaksi tercatat</small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <div class="input-group input-group-sm" style="max-width: 260px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchReport" class="form-control border-start-0 ps-0" placeholder="Cari di tabel ini...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($peminjaman->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-x display-4 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-secondary">Tidak Ada Data Peminjaman</h5>
                    <p class="small text-muted mb-0">Tidak ada catatan peminjaman alat ukur pada periode dan filter yang dipilih.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern" id="reportTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 4%;">No</th>
                                <th style="width: 12%;">No. Transaksi</th>
                                <th style="width: 18%;">Departemen & PIC</th>
                                <th style="width: 26%;">Alat Ukur Presisi</th>
                                <th class="text-center" style="width: 8%;">Qty</th>
                                <th style="width: 18%;">Rentang Waktu</th>
                                <th class="text-center" style="width: 14%;">Status & Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $p)
                                <tr class="report-row">
                                    <td class="text-center text-muted small">{{ $loop->iteration }}</td>
                                    <td class="fw-bold">
                                        <a href="{{ route('admin.peminjaman.show', $p->id) }}" class="text-decoration-none fw-semibold">
                                            #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="small text-muted font-monospace" style="font-size: 0.7rem;">
                                            {{ $p->created_at->format('d/m/Y H:i') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $p->department }}</div>
                                        <small class="text-muted"><i class="bi bi-person me-1"></i>PIC: <strong>{{ $p->pic }}</strong></small>
                                        @if($p->user)
                                            <div class="small text-secondary" style="font-size: 0.72rem;">User: {{ $p->user->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($p->details as $d)
                                                <li class="mb-1">
                                                    <i class="bi bi-caret-right-fill text-primary" style="font-size: 0.65rem;"></i>
                                                    <strong>{{ $d->tool->nama_alat ?? '-' }}</strong>
                                                    <span class="badge bg-light text-dark border ms-1">{{ $d->qty }} unit</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center fw-bold text-primary">{{ $p->details->sum('qty') }} unit</td>
                                    <td>
                                        <div class="small text-success fw-semibold">
                                            <i class="bi bi-calendar-check me-1"></i>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="small text-danger">
                                            <i class="bi bi-calendar-x me-1"></i>{{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d M Y') }}
                                        </div>
                                        <span class="badge bg-light text-muted border mt-1" style="font-size: 0.68rem;">
                                            {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($p->tanggal_kembali)) + 1 }} hari
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($p->status === 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Selesai (Baik)
                                            </span>
                                        @elseif($p->status === 'rusak' || $p->status === 'rusak_dikonfirmasi')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Kerusakan
                                            </span>
                                        @elseif($p->status === 'menunggu_konfirmasi')
                                            <span class="badge bg-danger text-white px-2 py-1">
                                                <i class="bi bi-shield-exclamation me-1"></i>Butuh Konfirmasi
                                            </span>
                                        @elseif($p->status === 'dalam_perbaikan')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                                <i class="bi bi-wrench me-1"></i>Perbaikan
                                            </span>
                                        @elseif($p->status === 'dipinjam')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                                <i class="bi bi-clock me-1"></i>Sedang Dipinjam
                                            </span>
                                        @elseif($p->status === 'menunggu_cek_teknisi')
                                            <span class="badge bg-info text-dark px-2 py-1">
                                                <i class="bi bi-hourglass-split me-1"></i>Menunggu Cek
                                            </span>
                                        @endif

                                        @if($p->pengembalian && $p->pengembalian->catatan_teknisi)
                                            <div class="small text-muted mt-1 fst-italic text-truncate" style="max-width: 150px; margin: 0 auto;" title="{{ $p->pengembalian->catatan_teknisi }}">
                                                "{{ $p->pengembalian->catatan_teknisi }}"
                                            </div>
                                        @endif
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Live search pada tabel log transaksi
        const searchInput = document.getElementById('liveSearchReport');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.report-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }

        @if($totalTransaksi > 0)
        // Chart 1: Donut Proporsi Kondisi
        const ctxKondisi = document.getElementById('kondisiChart');
        if (ctxKondisi) {
            new Chart(ctxKondisi, {
                type: 'doughnut',
                data: {
                    labels: ['Selesai Baik', 'Kerusakan / Cacat', 'Dalam Perbaikan', 'Masih Dipinjam'],
                    datasets: [{
                        data: [
                            {{ $selesaiBaik }},
                            {{ $rusak }},
                            {{ $dalamPerbaikan }},
                            {{ max(0, $masihDipinjam - $dalamPerbaikan) }}
                        ],
                        backgroundColor: [
                            '#10b981',
                            '#ef4444',
                            '#3b82f6',
                            '#f59e0b'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11, family: 'Inter' } }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        // Chart 2: Bar Unit per Departemen
        const ctxDept = document.getElementById('deptChart');
        if (ctxDept) {
            const deptLabels = {!! json_encode(array_slice(array_keys($deptStats->toArray()), 0, 6)) !!};
            const deptUnits = {!! json_encode(array_slice(array_column($deptStats->toArray(), 'total_unit'), 0, 6)) !!};

            new Chart(ctxDept, {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        label: 'Total Unit Alat',
                        data: deptUnits,
                        backgroundColor: '#4f46e5',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, font: { size: 11, family: 'Inter' } },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { size: 11, family: 'Inter' } },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
        @endif
    });
</script>
@endpush
