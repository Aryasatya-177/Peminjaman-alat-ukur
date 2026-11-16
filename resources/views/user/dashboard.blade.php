@extends('layouts.app')

@section('title', 'Dashboard Peminjaman Saya - Gauge Hub')
@section('page_title', 'Dashboard')

@section('content')
    {{-- Welcome Banner --}}
    <div class="card mb-4 p-4 text-white" style="background: var(--primary); border: 1px solid var(--sidebar-border); border-radius: 10px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="letter-spacing: -0.01em;">Selamat Datang, {{ Str::words(auth()->user()->name, 2) }}!</h4>
                <div class="small text-white-50 d-flex align-items-center flex-wrap gap-2">
                    <span><i class="bi bi-building me-1"></i> Departemen: <strong class="text-white">{{ auth()->user()->department ?? '-' }}</strong></span>
                    <span>&bull;</span>
                    <span><i class="bi bi-person-badge me-1"></i> PIC: <strong class="text-white">{{ auth()->user()->pic ?? auth()->user()->name }}</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('user.pinjam.create') }}" class="btn btn-warning fw-semibold px-4 py-2"
                   style="background: #f59e0b; border: 1px solid #d97706; color: #0f172a; border-radius: 8px; font-size: 0.875rem;">
                    <i class="bi bi-plus-circle me-1"></i> Pinjam Alat Baru
                </a>
            </div>
        </div>
    </div>

    {{-- ALERT PEMBERITAHUAN KERUSAKAN MENUNGGU KONFIRMASI USER --}}
    @if(isset($needUserConfirmations) && $needUserConfirmations->isNotEmpty())
        <div class="alert alert-danger border shadow-sm p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-octagon-fill text-danger fs-4"></i>
                <div>
                    <strong class="text-danger d-block">Konfirmasi Kerusakan Diperlukan</strong>
                    <span class="small text-muted">{{ $needUserConfirmations->count() }} transaksi memiliki alat yang tidak dapat direpair teknisi.</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @foreach($needUserConfirmations as $nuc)
                    <a href="{{ route('user.pinjam.show', $nuc->id) }}" class="btn btn-danger btn-sm fw-bold">
                        Tanggapi #{{ str_pad($nuc->id, 5, '0', STR_PAD_LEFT) }} &raquo;
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- PENGINGAT PENGEMBALIAN ALAT (JATUH TEMPO HARI INI ATAU TERLAMBAT) --}}
    @php
        $dueItems = collect();
        if (isset($peminjamanTerlambat)) {
            $dueItems = $dueItems->merge($peminjamanTerlambat);
        }
        if (isset($peminjamanHariIni)) {
            $dueItems = $dueItems->merge($peminjamanHariIni);
        }
    @endphp

    @if($dueItems->isNotEmpty())
        <div class="card mb-4 shadow-sm" id="reminder-panel"
             style="border: 1px solid {{ (isset($peminjamanTerlambat) && $peminjamanTerlambat->isNotEmpty()) ? '#fecaca' : '#fde68a' }}; border-left: 5px solid {{ (isset($peminjamanTerlambat) && $peminjamanTerlambat->isNotEmpty()) ? '#dc2626' : '#d97706' }}; background: #ffffff; border-radius: 10px;">
            <div class="p-3">
                {{-- Header Pengingat --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ (isset($peminjamanTerlambat) && $peminjamanTerlambat->isNotEmpty()) ? 'bi-exclamation-octagon-fill text-danger' : 'bi-alarm-fill text-warning' }} fs-5"></i>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Pengingat Pengembalian Alat</h6>
                            <small class="text-secondary">Batas serah terima ke teknisi hari ini pukul 16:30 WIB</small>
                        </div>
                    </div>
                    <span class="badge {{ (isset($peminjamanTerlambat) && $peminjamanTerlambat->isNotEmpty()) ? 'bg-danger text-white' : 'bg-warning text-dark border border-warning' }}" style="font-size: 0.75rem; font-weight: 600;">
                        {{ $dueItems->count() }} Transaksi
                    </span>
                </div>

                {{-- Daftar Transaksi yang Harus Dikumpulkan Hari Ini --}}
                <div class="d-flex flex-column gap-3">
                    @foreach($dueItems as $item)
                        @php
                            $isOverdue = $item->isTerlambat();
                            $daysOverdue = abs($item->sisaHariPinjam());
                        @endphp
                        <div class="card p-3" style="background: {{ $isOverdue ? '#fff5f5' : '#fffbeb' }}; border: 1px solid {{ $isOverdue ? '#fca5a5' : '#fcd34d' }}; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold font-monospace fs-6" style="color: var(--primary);">
                                            #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="badge bg-danger text-white" style="font-size: 0.72rem;">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat {{ $daysOverdue }} Hari
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.72rem;">
                                                <i class="bi bi-alarm-fill me-1"></i>Jatuh Tempo Hari Ini
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-secondary small mt-0.5" style="font-size: 0.78rem;">
                                        Departemen: <strong class="text-dark">{{ $item->department }}</strong> &bull; PIC: <strong class="text-dark">{{ $item->pic }}</strong>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-white text-dark border px-2 py-1" style="font-size: 0.75rem;">
                                        Total: <strong>{{ $item->details->sum('qty') }} unit</strong>
                                    </span>
                                </div>
                            </div>

                            {{-- Rincian Alat yang Harus Dikumpulkan --}}
                            <div class="bg-white p-2.5 rounded border mb-3">
                                <div class="small fw-bold text-secondary text-uppercase mb-1.5" style="font-size: 0.7rem; letter-spacing: 0.04em;">
                                    <i class="bi bi-card-checklist me-1 text-primary"></i>Daftar Alat Ukur:
                                </div>
                                <div class="d-flex flex-column gap-1.5">
                                    {{-- Alat Utama / Pertama --}}
                                    @foreach($item->detailsPertama as $dp)
                                        <div class="d-flex justify-content-between align-items-center py-1 px-2 rounded bg-light border-start border-3 border-primary" style="font-size: 0.78rem;">
                                            <div class="text-truncate me-2">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size: 0.65rem;">Awal</span>
                                                <strong>{{ $dp->tool->nama_alat ?? 'Alat' }}</strong>
                                                <span class="text-muted font-monospace">({{ $dp->tool->kode_alat ?? '-' }})</span>
                                            </div>
                                            <span class="badge bg-white text-dark border flex-shrink-0" style="font-size: 0.72rem;">{{ $dp->qty }} unit</span>
                                        </div>
                                    @endforeach

                                    {{-- Alat Tambahan --}}
                                    @if($item->hasTambahan())
                                        @foreach($item->detailsTambahan as $dt)
                                            <div class="d-flex justify-content-between align-items-center py-1 px-2 rounded bg-light border-start border-3 border-purple" style="border-left-color: #8b5cf6 !important; font-size: 0.78rem;">
                                                <div class="text-truncate me-2">
                                                    <span class="badge" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; font-size: 0.65rem;">Tambahan T{{ $dt->tahap }}</span>
                                                    <strong>{{ $dt->tool->nama_alat ?? 'Alat' }}</strong>
                                                    <span class="text-muted font-monospace">({{ $dt->tool->kode_alat ?? '-' }})</span>
                                                </div>
                                                <span class="badge bg-white text-dark border flex-shrink-0" style="font-size: 0.72rem;">{{ $dt->qty }} unit</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">
                                <a href="{{ route('user.pinjam.show', $item->id) }}" class="btn btn-sm btn-outline-secondary fw-semibold" style="border-radius: 6px; font-size: 0.8rem; padding: 6px 12px;">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail
                                </a>

                                <form action="{{ route('user.return', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Konfirmasi pengembalian seluruh alat ukur untuk transaksi #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $isOverdue ? 'btn-danger' : 'btn-warning text-dark' }} fw-bold px-3 py-1.5"
                                            style="border-radius: 6px; font-size: 0.82rem; {{ !$isOverdue ? 'background: #f59e0b; border: 1px solid #d97706;' : '' }}">
                                        <i class="bi bi-box-arrow-in-down me-1"></i> Kembalikan Alat
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Transaksi</div>
                <div class="stat-bg-icon"><i class="bi bi-journal-text"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-warning">
                <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                <div class="stat-value">{{ $stats['dipinjam'] }}</div>
                <div class="stat-label">Sedang Dipinjam</div>
                <div class="stat-bg-icon"><i class="bi bi-clock-history"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-info">
                <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="stat-value">{{ $stats['menunggu_cek'] }}</div>
                <div class="stat-label">Menunggu Cek Teknisi</div>
                <div class="stat-bg-icon"><i class="bi bi-hourglass-split"></i></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card stat-card-success">
                <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
                <div class="stat-value">{{ $stats['selesai'] }}</div>
                <div class="stat-label">Selesai</div>
                <div class="stat-bg-icon"><i class="bi bi-check-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- Peminjaman History Table --}}
    <div class="card">
        <div class="px-4 py-3 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid var(--border-color);">
            <h6 class="fw-700 mb-0" style="font-weight: 700;">
                <i class="bi bi-list-check me-2" style="color: var(--primary);"></i>Riwayat Peminjaman
            </h6>
            <span style="background: var(--body-bg); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary);">
                {{ $peminjaman->count() }} data
            </span>
        </div>

        @if($peminjaman->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon"><i class="bi bi-inbox"></i></div>
                <h6 class="fw-700 mb-1" style="font-weight: 700; color: var(--text-primary);">Belum Ada Riwayat Peminjaman</h6>
                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 20px;">
                    Anda belum pernah mengajukan peminjaman alat ukur presisi.
                </p>
                <a href="{{ route('user.pinjam.create') }}" class="btn-primary-modern btn">
                    <i class="bi bi-plus-circle me-1"></i> Mulai Pinjam Sekarang
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Transaksi</th>
                            <th>Alat Ukur</th>
                            <th class="text-center">Qty</th>
                            <th>Departemen / PIC</th>
                            <th>Periode Pinjam</th>
                            <th class="text-center">Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjaman as $p)
                            <tr style="{{ $p->isJatuhTempoHariIni() ? 'background-color: #fffdf5;' : ($p->isTerlambat() ? 'background-color: #fff5f5;' : '') }}">
                                <td class="ps-4">
                                    <a href="{{ route('user.pinjam.show', $p->id) }}"
                                       class="fw-700 text-decoration-none"
                                       style="font-weight: 700; color: var(--primary);">
                                        #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                    @if($p->hasTambahan())
                                        <div>
                                            <span class="badge" style="background: #f3e8ff; color: #7e22ce; font-size: 0.65rem; border: 1px solid #e9d5ff;">
                                                <i class="bi bi-layers-fill me-1"></i>Ada Tambahan
                                            </span>
                                        </div>
                                    @endif
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">
                                        {{ \Carbon\Carbon::parse($p->created_at)->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <div style="min-width: 250px; max-width: 320px;">
                                        {{-- 1. Peminjaman Pertama (Utama) --}}
                                        <div class="mb-2 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem; padding: 2px 6px;">
                                                    <i class="bi bi-1-circle-fill me-1"></i>Peminjaman Pertama
                                                </span>
                                                <span class="text-muted" style="font-size: 0.7rem; font-weight: 600;">{{ $p->totalQtyPertama() }} unit</span>
                                            </div>
                                            @foreach($p->detailsPertama as $d1)
                                                <div class="d-flex align-items-center justify-content-between text-dark mb-0.5" style="font-size: 0.8rem; line-height: 1.3;">
                                                    <span class="text-truncate me-1" title="{{ $d1->tool->nama_alat ?? 'Alat' }}">
                                                        &bull; {{ $d1->tool->nama_alat ?? 'Alat' }}
                                                    </span>
                                                    <span class="badge bg-white text-secondary border flex-shrink-0" style="font-size: 0.68rem; padding: 1px 5px;">{{ $d1->qty }} pcs</span>
                                                </div>
                                            @endforeach
                                            @php
                                                $catatan1 = $p->getCatatanPertama();
                                            @endphp
                                            @if($catatan1)
                                                <div class="mt-1 pt-1 border-top text-truncate" style="font-size: 0.72rem; color: #475569;" title="{{ $catatan1 }}">
                                                    <i class="bi bi-chat-left-quote-fill text-primary me-1"></i><strong>Ket:</strong> "{{ $catatan1 }}"
                                                </div>
                                            @endif
                                        </div>

                                        {{-- 2. Tambahan Peminjaman (Jika Ada) --}}
                                        @if($p->hasTambahan())
                                            <div class="p-2 rounded" style="background: #faf5ff; border: 1px solid #e9d5ff;">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="badge" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; font-size: 0.65rem; padding: 2px 6px;">
                                                        <i class="bi bi-layers-fill me-1"></i>Tambahan Peminjaman
                                                    </span>
                                                    <span style="font-size: 0.7rem; font-weight: 600; color: #7e22ce;">{{ $p->totalQtyTambahan() }} unit</span>
                                                </div>
                                                @foreach($p->detailsTambahan as $dt)
                                                    <div class="d-flex align-items-center justify-content-between text-dark mb-0.5" style="font-size: 0.8rem; line-height: 1.3;">
                                                        <span class="text-truncate me-1" title="{{ $dt->tool->nama_alat ?? 'Alat' }}">
                                                            &bull; {{ $dt->tool->nama_alat ?? 'Alat' }}
                                                        </span>
                                                        <span class="badge bg-white text-purple border flex-shrink-0" style="color: #7e22ce; font-size: 0.68rem; padding: 1px 5px;">{{ $dt->qty }} pcs</span>
                                                    </div>
                                                    @if($dt->catatan)
                                                        <div class="mt-0.5 text-truncate" style="font-size: 0.71rem; color: #6b21a8; padding-left: 6px;" title="{{ $dt->catatan }}">
                                                            <i class="bi bi-chat-dots-fill me-1"></i><strong>Ket (Tahap {{ $dt->tahap }}):</strong> "{{ $dt->catatan }}"
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-700" style="font-weight: 700; font-size: 1rem;">{{ $p->details->sum('qty') }}</span>
                                    <div style="font-size: 0.7rem; color: var(--text-secondary);">
                                        @if($p->hasTambahan())
                                            ({{ $p->totalQtyPertama() }}+{{ $p->totalQtyTambahan() }}) unit
                                        @else
                                            unit
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold" style="font-size: 0.85rem;">{{ $p->department }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        <i class="bi bi-person me-1"></i>{{ $p->pic }}
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1" style="font-size: 0.82rem;">
                                        <i class="bi bi-calendar-event" style="color: #059669;"></i>
                                        <strong>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->translatedFormat('d M Y') }}</strong>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                        s/d {{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('d M Y') }}
                                        @php
                                            $daysLeft = $p->sisaHariPinjam();
                                        @endphp
                                        @if($p->status === 'dipinjam' && $daysLeft < 0)
                                            <div class="mt-0.5">
                                                <span class="badge bg-danger text-white" style="font-size: 0.68rem;">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Terlambat {{ abs($daysLeft) }} hari!
                                                </span>
                                            </div>
                                        @elseif($p->status === 'dipinjam' && $daysLeft === 0)
                                            <div class="mt-0.5">
                                                <span class="badge bg-warning text-dark border border-warning" style="font-size: 0.68rem; font-weight: 600;">
                                                    <i class="bi bi-alarm-fill me-1"></i>Jatuh Tempo Hari Ini
                                                </span>
                                            </div>
                                        @elseif($p->status === 'dipinjam' && $daysLeft <= 2)
                                            <span style="color: #d97706; font-weight: 600;"> ({{ $daysLeft }} hari lagi)</span>
                                        @endif

                                        @if($p->hasPendingPerpanjangan())
                                            @php
                                                $pendingPerpanjangan = $p->perpanjangans->firstWhere('status', 'menunggu');
                                            @endphp
                                            @if($pendingPerpanjangan)
                                                <div class="mt-1">
                                                    <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.65rem;"
                                                          title="Alasan: {{ $pendingPerpanjangan->alasan }}">
                                                        <i class="bi bi-hourglass-split me-1"></i>Diajukan s/d {{ \Carbon\Carbon::parse($pendingPerpanjangan->tanggal_kembali_baru)->translatedFormat('d M Y') }}
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($p->status === 'dipinjam')
                                        <span class="badge-status badge-dipinjam">
                                            <i class="bi bi-clock me-1"></i>Dipinjam
                                        </span>
                                    @elseif($p->status === 'menunggu_cek_teknisi')
                                        <span class="badge-status badge-menunggu">
                                            <i class="bi bi-hourglass-split me-1"></i>Cek Teknisi
                                        </span>
                                    @elseif($p->status === 'dalam_perbaikan')
                                        <span class="badge-status badge-perbaikan">
                                            <i class="bi bi-wrench me-1"></i>Perbaikan
                                        </span>
                                    @elseif($p->status === 'menunggu_konfirmasi')
                                        <span class="badge-status badge-konfirmasi">
                                            <i class="bi bi-shield-exclamation me-1"></i>Butuh Konfirmasi
                                        </span>
                                    @elseif($p->status === 'rusak_dikonfirmasi')
                                        <span class="badge-status badge-dikonfirmasi">
                                            <i class="bi bi-check2-all me-1"></i>Rusak Dikonfirmasi
                                        </span>
                                    @elseif($p->status === 'selesai')
                                        <span class="badge-status badge-selesai">
                                            <i class="bi bi-check-all me-1"></i>Selesai
                                        </span>
                                    @elseif($p->status === 'rusak')
                                        <span class="badge-status badge-rusak">
                                            <i class="bi bi-exclamation-octagon me-1"></i>Rusak
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1 align-items-center flex-wrap justify-content-end">
                                        <a href="{{ route('user.pinjam.show', $p->id) }}"
                                           class="btn-ghost btn btn-sm" title="Lihat Detail Tanda Terima">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if($p->status === 'dipinjam')
                                            {{-- FITUR PERPANJANGAN & TAMBAH ALAT (WAJIB KONFIRMASI ADMIN) --}}
                                            @if($p->hasPendingPerpanjangan())
                                                <span class="badge" style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 0.72rem; padding: 5px 8px;"
                                                      title="Pengajuan perpanjangan / tambahan alat sedang menunggu konfirmasi Administrator">
                                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu Konfirmasi Admin
                                                </span>
                                            @else
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary fw-semibold"
                                                        style="border-radius: 6px; padding: 5px 10px; font-size: 0.78rem;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalTambahAlat_{{ $p->id }}"
                                                        title="Ajukan penambahan alat ukur ke transaksi peminjaman ini (Perlu Konfirmasi Admin)">
                                                    <i class="bi bi-cart-plus me-1"></i> Tambah Alat
                                                </button>

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-warning text-dark fw-semibold"
                                                        style="border-radius: 6px; padding: 5px 10px; font-size: 0.78rem;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalPerpanjang_{{ $p->id }}"
                                                        title="Ajukan perpanjangan tanggal pengembalian alat (Perlu Konfirmasi Admin)">
                                                    <i class="bi bi-calendar-plus me-1"></i> Perpanjang
                                                </button>
                                            @endif

                                            <form action="{{ route('user.return', $p->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Konfirmasi pengembalian alat ukur? Seluruh alat dalam transaksi ini (termasuk alat tambahan jika ada) akan diserahkan ke Teknisi untuk pemeriksaan kondisi.');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $p->isJatuhTempoHariIni() ? 'btn-warning text-dark fw-bold' : ($p->isTerlambat() ? 'btn-danger fw-bold' : 'btn-success fw-semibold') }}"
                                                        style="border-radius: 6px; padding: 5px 12px; font-size: 0.78rem; {{ $p->isJatuhTempoHariIni() ? 'background: #f59e0b; border: 1px solid #d97706;' : '' }}">
                                                    <i class="bi bi-arrow-return-left me-1"></i> Kembalikan
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH ALAT LANGSUNG DARI DASHBOARD (UNTUK PEMINJAMAN STATUS DIPINJAM) --}}
            @if(isset($availableTools))
                @foreach($peminjaman as $p)
                    @if($p->status === 'dipinjam')
                        <div class="modal fade" id="modalTambahAlat_{{ $p->id }}" tabindex="-1" aria-labelledby="modalLabel_{{ $p->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" style="max-height: calc(100vh - 60px);">
                                <form action="{{ route('user.pinjam.store') }}" method="POST" class="modal-content border-0 shadow" style="max-height: calc(100vh - 60px); display: flex; flex-direction: column;">
                                    @csrf
                                    <input type="hidden" name="peminjaman_id" value="{{ $p->id }}">

                                        <div class="modal-header text-white flex-shrink-0" style="background: var(--primary); border-bottom: 2px solid var(--accent); padding: 0.75rem 1.25rem;">
                                            <div>
                                                <h5 class="modal-title fw-bold mb-0" id="modalLabel_{{ $p->id }}">
                                                    <i class="bi bi-cart-plus me-2"></i>Tambah Alat Ukur - Transaksi #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                                                </h5>
                                                <small class="text-white-50">
                                                    Departemen: <strong>{{ $p->department }}</strong> &bull; PIC: <strong>{{ $p->pic }}</strong>
                                                </small>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body p-3 p-md-4" style="overflow-y: auto; max-height: calc(100vh - 210px);">
                                            {{-- Status & Syarat Badge --}}
                                            <div class="alert alert-warning border-warning-subtle bg-warning-subtle p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                                                    <div>
                                                        <strong class="text-warning-emphasis">Memerlukan Konfirmasi & Persetujuan Administrator</strong>
                                                        <div class="small text-secondary">
                                                            Alat tambahan yang diajukan akan ditinjau oleh Admin terlebih dahulu. Stok baru akan dipotong setelah Admin menyetujui pengajuan.
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="badge bg-warning text-dark px-2.5 py-1.5 fs-7">
                                                    <i class="bi bi-hourglass-split me-1"></i>Verifikasi Admin
                                                </span>
                                            </div>

                                            {{-- Ringkasan Peminjaman Pertama & Keterangan --}}
                                            <div class="card p-3 mb-3 bg-light border">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <strong class="text-primary small text-uppercase">
                                                        <i class="bi bi-1-circle-fill me-1"></i>Alat pada Peminjaman Pertama (Awal):
                                                    </strong>
                                                    <span class="badge bg-secondary-subtle text-secondary border">{{ $p->totalQtyPertama() }} unit</span>
                                                </div>
                                                <div class="small text-dark mb-2">
                                                    @foreach($p->detailsPertama as $dp)
                                                        <span class="badge bg-white text-dark border me-1 mb-1">
                                                            {{ $dp->tool->nama_alat ?? 'Alat' }} <strong>({{ $dp->qty }} pcs)</strong>
                                                        </span>
                                                    @endforeach
                                                </div>
                                                @if($p->getCatatanPertama())
                                                    <div class="small text-secondary pt-2 border-top">
                                                        <i class="bi bi-chat-left-quote-fill text-primary me-1"></i><strong>Keterangan Peminjaman Pertama:</strong>
                                                        <span class="fst-italic text-dark">"{{ $p->getCatatanPertama() }}"</span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Pilihan Alat Tambahan --}}
                                            <h6 class="fw-bold text-dark mb-2">
                                                <i class="bi bi-tools text-primary me-1"></i>Pilih Alat Tambahan yang Ingin Dipinjam:
                                            </h6>

                                            @if($availableTools->isEmpty())
                                                <div class="alert alert-warning small">
                                                    <i class="bi bi-exclamation-triangle me-1"></i> Tidak ada stok alat ukur yang tersedia saat ini.
                                                </div>
                                            @else
                                                <div class="mb-3">
                                                    <div class="row g-2 align-items-center">
                                                        <div class="col-md-8 col-7">
                                                            <label class="form-label small fw-semibold text-secondary mb-1">Nama Alat Ukur Presisi <span class="text-danger">*</span></label>
                                                            <select name="tool_id[]" class="form-select form-select-sm" required>
                                                                <option value="">-- Pilih Alat Ukur Tambahan --</option>
                                                                @foreach($availableTools as $tool)
                                                                    <option value="{{ $tool->id }}" {{ $tool->available_stock <= 0 ? 'disabled' : '' }}>
                                                                        [{{ $tool->kode_alat }}] {{ $tool->nama_alat }} ({{ $tool->available_stock > 0 ? 'Tersedia: '.$tool->available_stock : 'Stok: 0 - Habis' }})
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 col-5">
                                                            <label class="form-label small fw-semibold text-secondary mb-1">Kuantitas (Qty) <span class="text-danger">*</span></label>
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" name="qty[]" class="form-control form-control-sm text-center" min="1" value="1" required>
                                                                <span class="input-group-text">unit</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                                                        Pilih alat yang Anda butuhkan untuk melengkapi transaksi peminjaman ini.
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Keterangan / Catatan Tambahan (Dipisahkan dari Pertama) --}}
                                            <div class="mb-3">
                                                <label for="catatan_tambahan_{{ $p->id }}" class="form-label small fw-semibold text-secondary">
                                                    <i class="bi bi-chat-right-text me-1 text-primary"></i>Keterangan Keperluan Tambahan Alat:
                                                </label>
                                                <textarea name="catatan_tambahan" id="catatan_tambahan_{{ $p->id }}" rows="2" class="form-control form-control-sm"
                                                    placeholder="Contoh: Kebutuhan tambahan alat ukur untuk verifikasi batch 2 tooling"></textarea>
                                                <div class="form-text" style="font-size: 0.72rem; color: #6b21a8;">
                                                    <i class="bi bi-info-circle me-1"></i>Keterangan ini dicatat terpisah khusus untuk peminjaman tambahan Anda.
                                                </div>
                                            </div>

                                            {{-- Rencana Tanggal Pengembalian --}}
                                            <div class="mb-2">
                                                <label for="tanggal_kembali_{{ $p->id }}" class="form-label small fw-semibold text-secondary">
                                                    Rencana Batas Pengembalian
                                                </label>
                                                <input type="date" name="tanggal_kembali" id="tanggal_kembali_{{ $p->id }}" class="form-control form-control-sm"
                                                    value="{{ $p->tanggal_kembali->format('Y-m-d') }}"
                                                    min="{{ $p->tanggal_pinjam->format('Y-m-d') }}">
                                                <small class="text-muted" style="font-size: 0.72rem;">
                                                    Anda dapat memperbarui tanggal kembali jika penambahan alat memerlukan waktu lebih lama.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light px-3 py-2 d-flex justify-content-between align-items-center flex-shrink-0">
                                            <a href="{{ route('user.pinjam.create', ['peminjaman_id' => $p->id]) }}" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-grid-fill me-1"></i> Buka di Katalog
                                            </a>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-circle me-1"></i> Batal
                                                </button>
                                                <button type="submit" class="btn btn-warning btn-sm text-dark px-3 fw-bold shadow-sm" style="background: #f59e0b; border: 1px solid #d97706;">
                                                    <i class="bi bi-send-check me-1"></i> Ajukan Tambahan Alat ke Admin
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                @endforeach
            @endif

            {{-- MODAL PENGAJUAN PERPANJANGAN PEMINJAMAN ALAT (USER -> ADMIN) --}}
            @foreach($peminjaman as $p)
                @if($p->status === 'dipinjam')
                    <div class="modal fade" id="modalPerpanjang_{{ $p->id }}" tabindex="-1" aria-labelledby="modalPerpanjangLabel_{{ $p->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-height: calc(100vh - 60px);">
                            <form action="{{ route('user.pinjam.perpanjang', $p->id) }}" method="POST" class="modal-content border-0 shadow" style="max-height: calc(100vh - 60px); display: flex; flex-direction: column;">
                                @csrf
                                <div class="modal-header text-white flex-shrink-0" style="background: var(--primary); border-bottom: 2px solid var(--accent); padding: 0.75rem 1.25rem;">
                                    <div>
                                        <h5 class="modal-title fw-bold mb-0" id="modalPerpanjangLabel_{{ $p->id }}">
                                            <i class="bi bi-calendar-plus me-2"></i>Ajukan Perpanjangan Peminjaman
                                        </h5>
                                        <small class="text-white-50">
                                            Transaksi #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }} &bull; {{ $p->department }}
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
                                                {{ \Carbon\Carbon::parse($p->tanggal_kembali)->translatedFormat('l, d F Y') }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="small text-secondary">Status Durasi:</span>
                                            @php $sisaHari = $p->sisaHariPinjam(); @endphp
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
                                        <label for="tanggal_kembali_baru_{{ $p->id }}" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">
                                            Tanggal Pengembalian Baru yang Diajukan <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                               name="tanggal_kembali_baru"
                                               id="tanggal_kembali_baru_{{ $p->id }}"
                                               class="form-control"
                                               min="{{ \Carbon\Carbon::parse($p->tanggal_kembali)->addDay()->format('Y-m-d') }}"
                                               required>
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">
                                            Pilih tanggal setelah batas kembali saat ini (minimal: {{ \Carbon\Carbon::parse($p->tanggal_kembali)->addDay()->translatedFormat('d F Y') }}).
                                        </div>
                                    </div>

                                    {{-- Pilihan Alat Tambahan Sekaligus (Opsional) --}}
                                    @if(isset($availableTools) && $availableTools->isNotEmpty())
                                        <div class="mb-3 pt-2 border-top">
                                            <label class="form-label fw-bold text-dark mb-1" style="font-size: 0.85rem;">
                                                <i class="bi bi-cart-plus text-primary me-1"></i>Ingin Menambah Alat Ukur Sekaligus? (Opsional)
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
                                                Pilih jika ingin menambah alat ukur lain sekaligus memperpanjang batas pengembalian.
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Input Catatan / Alasan --}}
                                    <div class="mb-3">
                                        <label for="alasan_{{ $p->id }}" class="form-label fw-bold text-dark" style="font-size: 0.85rem;">
                                            Catatan / Alasan Perpanjangan untuk Admin <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="alasan"
                                                  id="alasan_{{ $p->id }}"
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
            @endforeach
@endsection