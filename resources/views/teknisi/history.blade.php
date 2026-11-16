@extends('layouts.app')

@section('title', 'Riwayat Inspeksi & Kalibrasi Alat Ukur - Teknisi')
@section('page_title', 'Riwayat Inspeksi')

@push('styles')
<style>
    .history-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-sm);
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: var(--radius-chip);
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-secondary);
        transition: all 0.15s ease;
    }
    .filter-chip:hover {
        background: #f1f5f9;
        color: var(--primary);
        border-color: var(--border-hover);
    }
    .filter-chip.active {
        background: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }
</style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge-status badge-info px-2 py-1 small">
                    <i class="bi bi-clock-history me-1"></i>Arsip Pengujian Mutu
                </span>
                <span class="text-muted small">Total: <strong>{{ $riwayat->total() }} catatan inspeksi</strong></span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-journal-check text-primary me-2"></i>Riwayat Inspeksi & Perbaikan Teknisi
            </h1>
            <p class="text-muted mb-0">Arsip seluruh rekam jejak pemeriksaan fisik alat ukur presisi, penanganan perbaikan (repair), dan eskalasi kerusakan</p>
        </div>
        <div class="d-flex gap-2 no-print flex-wrap">
            <a href="{{ route('teknisi.antrian') }}" class="btn btn-outline-warning text-dark btn-sm px-3 fw-semibold">
                <i class="bi bi-inbox me-1"></i> Antrian Periksa
            </a>
            <a href="{{ route('teknisi.dashboard') }}" class="btn btn-ghost btn-sm px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Dashboard Teknisi
            </a>
        </div>
    </div>

    {{-- Quick Filter Chips --}}
    <div class="d-flex gap-2 mb-3 flex-wrap">
        @php
            $kondisiReq = request('kondisi', '');
            $beratReq = request('kerusakan_berat', '');
            $baseParams = request()->except(['kondisi', 'kerusakan_berat', 'page']);
        @endphp
        <a href="{{ route('teknisi.history', array_merge($baseParams, ['kondisi' => '', 'kerusakan_berat' => ''])) }}"
           class="filter-chip {{ empty($kondisiReq) && empty($beratReq) ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Semua Hasil Uji
        </a>
        <a href="{{ route('teknisi.history', array_merge($baseParams, ['kondisi' => 'baik', 'kerusakan_berat' => ''])) }}"
           class="filter-chip {{ $kondisiReq === 'baik' ? 'active' : '' }}">
            <i class="bi bi-check-circle-fill text-success"></i> Kondisi Baik Saja
        </a>
        <a href="{{ route('teknisi.history', array_merge($baseParams, ['kondisi' => 'rusak', 'kerusakan_berat' => ''])) }}"
           class="filter-chip {{ $kondisiReq === 'rusak' && empty($beratReq) ? 'active' : '' }}">
            <i class="bi bi-exclamation-triangle-fill text-danger"></i> Terdapat Kerusakan
        </a>
        <a href="{{ route('teknisi.history', array_merge($baseParams, ['kondisi' => 'rusak', 'kerusakan_berat' => '1'])) }}"
           class="filter-chip {{ $beratReq == '1' ? 'active' : '' }}">
            <i class="bi bi-shield-slash-fill text-danger"></i> Kerusakan Berat (≥ 3 Unit)
        </a>
    </div>

    {{-- Filter Bar Komprehensif --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('teknisi.history') }}" class="row g-2 align-items-center">
            <div class="col-lg-5 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari transaksi #ID, departemen, PIC, atau catatan..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">-- Semua Kondisi Uji --</option>
                    <option value="baik" {{ request('kondisi') === 'baik' ? 'selected' : '' }}>Hanya Kondisi Baik</option>
                    <option value="rusak" {{ request('kondisi') === 'rusak' ? 'selected' : '' }}>Hanya Kondisi Rusak</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="kerusakan_berat" id="chkKerusakanBerat" value="1"
                        {{ request('kerusakan_berat') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-danger small" for="chkKerusakanBerat">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Rusak ≥ 3 Unit
                    </label>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                    <i class="bi bi-filter me-1"></i> Saring
                </button>
                @if(request('search') || request('kondisi') || request('kerusakan_berat'))
                    <a href="{{ route('teknisi.history') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>Catatan Pemeriksaan & Tindakan Repair
                </h5>
                <small class="text-muted">Menampilkan {{ $riwayat->firstItem() ?? 0 }} - {{ $riwayat->lastItem() ?? 0 }} dari {{ $riwayat->total() }} riwayat</small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchHistory" class="form-control border-start-0 ps-0" placeholder="Cari di halaman ini...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($riwayat->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Tidak Ada Riwayat Pemeriksaan</h5>
                    <p class="small text-muted mb-3">Belum ditemukan data hasil inspeksi sesuai kriteria filter yang diterapkan.</p>
                    @if(request('search') || request('kondisi') || request('kerusakan_berat'))
                        <a href="{{ route('teknisi.history') }}" class="btn btn-ghost btn-sm px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Filter
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern" id="historyTable">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 13%;">No. Transaksi</th>
                                <th style="width: 18%;">Departemen & PIC</th>
                                <th style="width: 26%;">Alat Ukur yang Dicek</th>
                                <th style="width: 14%;">Kondisi Hasil Uji</th>
                                <th style="width: 15%;">Tindakan Repair</th>
                                <th style="width: 14%;" class="text-end pe-3">Catatan / Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat as $item)
                                <tr class="history-row">
                                    <td class="ps-3">
                                        <a href="{{ route('user.pinjam.show', $item->peminjaman_id) }}" class="fw-bold text-decoration-none">
                                             #{{ str_pad($item->peminjaman_id, 5, '0', STR_PAD_LEFT) }}
                                        </a>
                                        <div class="small text-muted" style="font-size: 0.72rem;">
                                            {{ \Carbon\Carbon::parse($item->tanggal_dikembalikan)->translatedFormat('d M Y, H:i') }}
                                        </div>
                                        <div class="small text-secondary" style="font-size: 0.7rem;">
                                            Teknisi: <strong>{{ $item->teknisi->name ?? 'Teknisi' }}</strong>
                                        </div>
                                        @if($item->tanda_kerusakan_berat)
                                            <span class="badge bg-danger text-white mt-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-exclamation-triangle-fill"></i> RUSAK ≥ 3
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->peminjaman->department ?? '-' }}</div>
                                        <div class="small text-muted"><i class="bi bi-person me-1"></i>PIC: {{ $item->peminjaman->pic ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @if($item->peminjaman && $item->peminjaman->details)
                                                @foreach($item->peminjaman->details as $detail)
                                                    <li class="mb-1">
                                                        <i class="bi bi-check2 text-success me-1"></i>
                                                        <strong>{{ $detail->tool->nama_alat ?? 'Alat' }}</strong>
                                                        <span class="badge bg-light text-dark border ms-1 font-monospace">
                                                            {{ $detail->tool->kode_alat ?? '-' }} ({{ $detail->qty }} unit)
                                                        </span>
                                                    </li>
                                                @endforeach
                                            @else
                                                <li class="text-muted">-</li>
                                            @endif
                                        </ul>
                                    </td>
                                    <td>
                                        @if($item->kondisi_alat === 'baik')
                                            <span class="badge-status badge-success px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Semua Baik
                                            </span>
                                            <div class="small text-muted mt-1" style="font-size: 0.7rem;">Seluruh unit normal</div>
                                        @else
                                            <span class="badge-status badge-danger px-2 py-1">
                                                <i class="bi bi-exclamation-octagon me-1"></i>Rusak ({{ $item->jumlah_rusak }} unit)
                                            </span>
                                            @if($item->jumlah_baik > 0)
                                                <div class="small text-success mt-1" style="font-size: 0.7rem;">Baik: {{ $item->jumlah_baik }} unit</div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->status_repair === 'selesai_direpair')
                                            <span class="badge-status badge-success px-2 py-1">
                                                <i class="bi bi-check-all me-1"></i>Telah Dibenarkan
                                            </span>
                                        @elseif($item->status_repair === 'dalam_perbaikan')
                                            <span class="badge-status badge-info px-2 py-1">
                                                <i class="bi bi-wrench me-1"></i>Dalam Perbaikan
                                            </span>
                                        @elseif($item->status_repair === 'tidak_bisa_direpair')
                                            <span class="badge-status badge-danger px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i>Tidak Bisa Repair
                                            </span>
                                            <div class="small text-muted mt-1" style="font-size: 0.68rem;">
                                                Admin: {!! $item->konfirmasi_admin ? '<span class="text-success fw-bold">✓</span>' : '<span class="text-danger fw-bold">✗</span>' !!} |
                                                User: {!! $item->konfirmasi_user ? '<span class="text-success fw-bold">✓</span>' : '<span class="text-danger fw-bold">✗</span>' !!}
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('user.pinjam.show', $item->peminjaman_id) }}" class="btn btn-ghost btn-sm px-3">
                                            <i class="bi bi-eye me-1"></i> Bukti
                                        </a>
                                        @if($item->catatan_teknisi)
                                            <div class="small text-muted mt-1 text-truncate" style="max-width: 160px; margin-left: auto;" title="{{ $item->catatan_teknisi }}">
                                                <em>"{{ $item->catatan_teknisi }}"</em>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $riwayat->count() }} data riwayat pada halaman ini
                    </div>
                    <div>
                        {{ $riwayat->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveInput = document.getElementById('liveSearchHistory');
        if (liveInput) {
            liveInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.history-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
