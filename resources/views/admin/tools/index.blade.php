@extends('layouts.app')

@section('title', 'Katalog & Stok Alat Ukur Presisi - Gauge Hub')
@section('page_title', 'Stok Alat Ukur')

@push('styles')
<style>
    .filter-chip-bar {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 4px;
    }
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
    .stock-meter {
        height: 6px;
        border-radius: 3px;
        background-color: #f1f5f9;
        overflow: hidden;
    }
    .stock-meter-fill {
        height: 100%;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-semibold">
                    <i class="bi bi-rulers me-1"></i>Inventaris Master Data
                </span>
                <span class="text-muted small">Total: <strong>{{ $tools->total() }} master alat terdaftar</strong></span>
            </div>
            <h1 class="h3 fw-bold mb-1 text-dark">
                <i class="bi bi-tools text-primary me-2"></i>Katalog & Stok Alat Ukur Presisi
            </h1>
            <p class="text-muted mb-0">Kelola master data, penomoran kode alat, kuantitas unit fisik inventaris, dan pantau stok siap pinjam</p>
        </div>
        <div class="d-flex gap-2 no-print flex-wrap">
            <a href="{{ route('admin.tools.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i> Tambah Alat Ukur Baru
            </a>
        </div>
    </div>

    {{-- Kartu Ringkasan Stok --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-tools"></i></div>
                <div class="stat-value">{{ $stats['total_jenis'] }}</div>
                <div class="stat-label">Total Jenis Alat</div>
                <div class="stat-sub">Item varian alat presisi</div>
                <div class="stat-bg-icon"><i class="bi bi-tools"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-boxes"></i></div>
                <div class="stat-value">{{ $stats['total_unit'] }}</div>
                <div class="stat-label">Total Unit Fisik</div>
                <div class="stat-sub">Unit aset terdaftar</div>
                <div class="stat-bg-icon"><i class="bi bi-boxes"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-check2-circle"></i></div>
                <div class="stat-value">{{ $stats['tersedia'] }}</div>
                <div class="stat-label">Stok Tersedia</div>
                <div class="stat-sub">Siap dipinjam sekarang</div>
                <div class="stat-bg-icon"><i class="bi bi-check2-circle"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
                <div class="stat-value">{{ $stats['dipinjam'] }}</div>
                <div class="stat-label">Sedang Dipinjam</div>
                <div class="stat-sub">Unit aktif di lini kerja</div>
                <div class="stat-bg-icon"><i class="bi bi-arrow-left-right"></i></div>
            </div>
        </div>
    </div>

    {{-- Quick Filter Chips --}}
    <div class="filter-chip-bar mb-3">
        @php
            $statusFilter = request('status', '');
            $baseParams = request()->except(['status', 'page']);
        @endphp
        <a href="{{ route('admin.tools.index', array_merge($baseParams, ['status' => ''])) }}"
           class="status-chip {{ empty($statusFilter) ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Semua Alat Ukur
        </a>
        <a href="{{ route('admin.tools.index', array_merge($baseParams, ['status' => 'available'])) }}"
           class="status-chip {{ $statusFilter === 'available' ? 'active' : '' }}">
            <i class="bi bi-check-circle-fill text-success"></i> Stok Tersedia (> 0)
        </a>
        <a href="{{ route('admin.tools.index', array_merge($baseParams, ['status' => 'empty'])) }}"
           class="status-chip {{ $statusFilter === 'empty' ? 'active' : '' }}">
            <i class="bi bi-x-circle-fill text-danger"></i> Stok Habis (0)
        </a>
    </div>

    {{-- Filter & Pencarian --}}
    <div class="card p-3 mb-4 shadow-sm border-0">
        <form method="GET" action="{{ route('admin.tools.index') }}" class="row g-2 align-items-center">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="col-lg-6 col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control"
                        placeholder="Cari nama alat (Dial Gauge, Ball Gauge, Micrometer...) atau kode alat..."
                        value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Ketersediaan Stok --</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Stok Tersedia (> 0)</option>
                    <option value="empty" {{ request('status') === 'empty' ? 'selected' : '' }}>Stok Habis (0)</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1 fw-semibold">
                    <i class="bi bi-filter me-1"></i> Saring
                </button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Inventaris Alat --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-list-ul text-primary"></i>Daftar Alat Ukur Presisi
                </h5>
                <small class="text-muted">Menampilkan {{ $tools->firstItem() ?? 0 }} - {{ $tools->lastItem() ?? 0 }} dari {{ $tools->total() }} item terdaftar</small>
            </div>
            <div class="d-flex align-items-center gap-2 no-print">
                <div class="input-group input-group-sm" style="max-width: 220px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="liveSearchTools" class="form-control border-start-0 ps-0" placeholder="Cari di halaman ini...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if($tools->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox display-4 d-block mb-3 text-secondary"></i>
                    <h5 class="fw-bold text-dark">Alat Ukur Tidak Ditemukan</h5>
                    <p class="small text-muted mb-3">Silakan sesuaikan kata kunci pencarian atau daftarkan master alat baru.</p>
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.tools.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset Seluruh Filter
                        </a>
                    @endif
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern" id="toolsTable">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width: 5%;">No</th>
                                <th style="width: 15%;">Kode Alat</th>
                                <th style="width: 33%;">Nama Alat Ukur Presisi</th>
                                <th class="text-center" style="width: 12%;">Total Stok</th>
                                <th style="width: 18%;">Ketersediaan</th>
                                <th class="text-center" style="width: 10%;">Status</th>
                                <th class="text-end pe-3" style="width: 7%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tools as $tool)
                                @php
                                    $pct = $tool->total_stock > 0 ? round(($tool->available_stock / $tool->total_stock) * 100) : 0;
                                @endphp
                                <tr class="tool-row">
                                    <td class="ps-3 text-muted small">{{ $loop->iteration + ($tools->currentPage() - 1) * $tools->perPage() }}</td>
                                    <td>
                                        <span class="badge bg-dark font-monospace px-2 py-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                            {{ $tool->kode_alat }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $tool->nama_alat }}</div>
                                        <small class="text-muted font-monospace" style="font-size: 0.72rem;">ID Sistem: #{{ $tool->id }}</small>
                                    </td>
                                    <td class="text-center fw-semibold text-secondary">
                                        {{ $tool->total_stock }} unit
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <span class="fw-bold {{ $tool->available_stock > 0 ? 'text-success' : 'text-danger' }}" style="font-size: 0.85rem;">
                                                {{ $tool->available_stock }} unit
                                            </span>
                                            <small class="text-muted" style="font-size: 0.7rem;">{{ $pct }}%</small>
                                        </div>
                                        <div class="stock-meter">
                                            <div class="stock-meter-fill {{ $tool->available_stock > 0 ? 'bg-success' : 'bg-danger' }}" style="width: {{ $pct }}%;"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($tool->available_stock > 0)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i>Tersedia
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i>Habis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('admin.tools.edit', $tool->id) }}"
                                               class="btn btn-sm btn-outline-primary rounded-circle"
                                               style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                               title="Edit Data & Sesuaikan Stok">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.tools.destroy', $tool->id) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus alat ukur {{ $tool->nama_alat }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-circle"
                                                    style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"
                                                    title="Hapus Alat">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Menampilkan {{ $tools->count() }} data alat pada halaman ini
                    </div>
                    <div>
                        {{ $tools->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const liveInput = document.getElementById('liveSearchTools');
        if (liveInput) {
            liveInput.addEventListener('input', function () {
                const keyword = this.value.toLowerCase();
                const rows = document.querySelectorAll('.tool-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>
@endpush
