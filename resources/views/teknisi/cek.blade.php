@extends('layouts.app')

@section('title', 'Pemeriksaan Alat Transaksi #' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT))

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card p-4 p-md-5 shadow-sm">
                {{-- Header Form Pengecekan --}}
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-circle">
                            <i class="bi bi-clipboard2-check fs-2"></i>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark mb-1">PROSEDUR INSPEKSI FISIK & KALIBRASI</span>
                            <h3 class="fw-bold mb-0">Uji Kelayakan Alat Ukur Presisi</h3>
                            <small class="text-muted">Transaksi Peminjaman #{{ str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) }}</small>
                        </div>
                    </div>
                    <a href="{{ route('teknisi.antrian') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrian
                    </a>
                </div>

                {{-- Informasi Peminjam & Jadwal --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="text-secondary small fw-bold mb-2 text-uppercase">Informasi Peminjam</div>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Departemen:</td>
                                    <td class="fw-bold text-primary">{{ $peminjaman->department }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">PIC Pengembali:</td>
                                    <td class="fw-semibold">{{ $peminjaman->pic }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pemohon Akun:</td>
                                    <td>{{ $peminjaman->user->name ?? '-' }} ({{ $peminjaman->user->email ?? '-' }})</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <div class="text-secondary small fw-bold mb-2 text-uppercase">Durasi Peminjaman</div>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Tanggal Pinjam:</td>
                                    <td class="fw-semibold text-success">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Batas Pengembalian:</td>
                                    <td class="fw-semibold text-danger">
                                        {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d F Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Waktu Pengembalian:</td>
                                    <td class="fw-semibold text-dark">{{ now()->translatedFormat('d F Y (Hari Ini)') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Daftar Alat Ukur yang Sedang Diperiksa --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-rulers text-primary me-2"></i>Daftar Alat Ukur yang Diserahkan
                    </h5>
                    <span class="badge bg-primary fs-6 px-3 py-1">Total Unit: {{ $totalQty }} unit</span>
                </div>
                {{-- Info Tambahan Peminjaman untuk Teknisi --}}
                @if($peminjaman->hasTambahan())
                    <div class="alert alert-info border-2 mb-3 py-2 px-3 small rounded-3">
                        <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                        <strong>Catatan Transaksi:</strong> Peminjaman ini memiliki alat tambahan yang digabungkan dalam 1 proses transaksi.
                        Total: <strong>{{ $totalQty }} unit</strong> (Pertama: {{ $peminjaman->totalQtyPertama() }} unit, Tambahan: {{ $peminjaman->totalQtyTambahan() }} unit). Seluruh alat diperiksa bersamaan.
                    </div>
                @endif

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 5%;">No</th>
                                <th style="width: 18%;">Tahap</th>
                                <th style="width: 20%;">Kode Alat</th>
                                <th style="width: 42%;">Nama Alat Ukur Presisi</th>
                                <th class="text-center" style="width: 15%;">Jumlah (Qty)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman->details as $d)
                                <tr class="{{ $d->is_tambahan ? 'bg-light-subtle' : '' }}">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>
                                        @if($d->is_tambahan)
                                            <span class="badge text-white px-2 py-1" style="background: #7e22ce; font-size: 0.72rem;">
                                                <i class="bi bi-plus-circle-fill me-1"></i>Tambahan (Tahap {{ $d->tahap }})
                                            </span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-1-circle-fill me-1"></i>Peminjaman Pertama
                                            </span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary font-monospace">{{ $d->tool->kode_alat ?? '-' }}</span></td>
                                    <td>
                                        <div class="fw-semibold">{{ $d->tool->nama_alat ?? 'Alat' }}</div>
                                        @if($d->catatan)
                                            <small class="text-secondary fst-italic d-block mt-0.5" style="font-size: 0.75rem;">
                                                Catatan: "{{ $d->catatan }}"
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-primary">{{ $d->qty }} unit</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Seluruh Unit yang Dipinjam:</th>
                                <th class="text-center text-primary fs-6 fw-bold">{{ $totalQty }} unit</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Form Hasil Uji Teknisi --}}
                <form action="{{ route('teknisi.store', $peminjaman->id) }}" method="POST" id="formCekTeknisi">
                    @csrf

                    {{-- Panduan Uji Standar Presisi --}}
                    <div class="card border-info bg-light p-3 mb-4">
                        <h6 class="fw-bold text-info-emphasis mb-2">
                            <i class="bi bi-info-circle-fill me-1"></i> Standar Pengecekan Alat Ukur Presisi:
                        </h6>
                        <div class="row g-2 small text-secondary">
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded border h-100">
                                    <strong>1. Uji Visual Fisik:</strong><br>
                                    Cek goresan, benturan bodi, kebersihan kaca dial, atau retak pada casing pelindung.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded border h-100">
                                    <strong>2. Uji Gerak Spindle:</strong><br>
                                    Spindle / contact point bergerak halus tanpa hambatan dan kembali ke posisi semula.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 bg-white rounded border h-100">
                                    <strong>3. Uji Kalibrasi & Titik Nol:</strong><br>
                                    Jarum indikator kembali stabil ke titik referensi nol (zero error tolerance).
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pilihan Keputusan Kondisi Alat --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-5">
                            Keputusan Hasil Inspeksi <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="card p-3 border-2 h-100 cursor-pointer condition-card" id="cardBaik" style="cursor: pointer;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kondisi_alat" id="kondisiBaik"
                                            value="baik" {{ old('kondisi_alat', 'baik') === 'baik' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-success fs-5 ms-1" for="kondisiBaik">
                                            <i class="bi bi-check-circle-fill me-1"></i> Semua Kondisi BAIK & Normal
                                        </label>
                                    </div>
                                    <p class="small text-muted mt-2 ps-4 mb-0">
                                        Seluruh unit ({{ $totalQty }} unit) dalam kondisi fisik mulus, spindle lancar, dan kalibrasi akurat.
                                        <strong>Seluruh unit stok akan otomatis dikembalikan ke inventaris.</strong>
                                    </p>
                                </label>
                            </div>

                            <div class="col-md-6">
                                <label class="card p-3 border-2 h-100 cursor-pointer condition-card" id="cardRusak" style="cursor: pointer;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="kondisi_alat" id="kondisiRusak"
                                            value="rusak" {{ old('kondisi_alat') === 'rusak' ? 'checked' : '' }} required>
                                        <label class="form-check-label fw-bold text-danger fs-5 ms-1" for="kondisiRusak">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i> Terdapat Kerusakan / Masalah
                                        </label>
                                    </div>
                                    <p class="small text-muted mt-2 ps-4 mb-0">
                                        Terdapat alat yang mengalami benturan bodi, spindle seret/macet, jarum oleng, atau deviasi kepresisian.
                                    </p>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- BLOK KHUSUS: Evaluasi Kerusakan, Ambang Batas 3 Unit, & Pilihan Perbaikan --}}
                    <div id="sectionKerusakan" style="display: none;" class="p-4 rounded-3 border border-danger mb-4 bg-white shadow-sm">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <i class="bi bi-tools text-danger fs-4"></i>
                            <div>
                                <h5 class="fw-bold text-danger mb-0">Evaluasi Rincian Kerusakan & Kelaikan Perbaikan (Repair)</h5>
                                <small class="text-muted">Masukkan jumlah unit yang rusak dan tentukan tindakan teknisi</small>
                            </div>
                        </div>

                        {{-- Input Jumlah Unit Rusak & Kalkulasi --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="jumlah_rusak" class="form-label fw-bold text-dark">
                                    Jumlah Unit Rusak <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="jumlah_rusak" id="jumlah_rusak"
                                        class="form-control form-control-lg fw-bold text-danger text-center @error('jumlah_rusak') is-invalid @enderror"
                                        min="1" max="{{ $totalQty }}" value="{{ old('jumlah_rusak', 1) }}">
                                    <span class="input-group-text fw-bold">Unit</span>
                                </div>
                                <div class="form-text small">Dari total <strong>{{ $totalQty }} unit</strong> yang dipinjam.</div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted">Ringkasan Unit Kondisi Baik</label>
                                <div class="p-2 rounded bg-success-subtle border border-success text-center">
                                    <span class="fs-4 fw-bold text-success" id="labelUnitBaik">{{ max(0, $totalQty - 1) }}</span>
                                    <span class="text-success fw-semibold"> Unit</span>
                                    <div class="small text-muted">Otomatis dikembalikan ke inventaris</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold text-muted">Ringkasan Unit Rusak</label>
                                <div class="p-2 rounded bg-danger-subtle border border-danger text-center">
                                    <span class="fs-4 fw-bold text-danger" id="labelUnitRusak">1</span>
                                    <span class="text-danger fw-semibold"> Unit</span>
                                    <div class="small text-muted" id="statusStokRusakKeterangan">Menunggu keputusan teknisi</div>
                                </div>
                            </div>
                        </div>

                        {{-- ALERT OTOMATIS: JIKA RUSAK 3 ATAU LEBIH (≥ 3 UNIT) --}}
                        <div id="alertKerusakanBerat" class="alert alert-danger border-2 p-3 mb-4 d-flex align-items-center gap-3 shadow-sm" style="display: none;">
                            <div class="p-2 bg-danger text-white rounded-circle flex-shrink-0">
                                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong class="fs-6 text-uppercase d-block text-danger">
                                    <i class="bi bi-shield-fill-exclamation me-1"></i> Tanda Khusus: Kerusakan Berat (3 atau Lebih Unit Rusak Terdeteksi)
                                </strong>
                                <span class="small text-dark">
                                    Peminjaman ini memiliki <strong id="highlightJumlahRusak" class="text-danger fs-6">3</strong> unit rusak dari total {{ $totalQty }} unit yang dipinjam.
                                    Jika alat masih bisa diperbaiki, segera <strong>benarkan / repair</strong>. Namun jika <strong>tidak bisa diperbaiki</strong>, sistem mewajibkan penerusan konfirmasi ke <strong>Hak Akses Admin</strong> dan <strong>User peminjam</strong>.
                                </span>
                            </div>
                        </div>

                        {{-- Keputusan Kelaikan Repair --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark fs-6">
                                Apakah Alat yang Rusak Masih Bisa Di-repair (Dapat Diperbaiki)? <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                {{-- Opsi: Bisa Direpair --}}
                                <div class="col-md-6">
                                    <label class="card p-3 border-2 h-100 cursor-pointer repair-choice-card" id="cardRepairBisa" style="cursor: pointer;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bisa_direpair" id="repairBisa"
                                                value="bisa" {{ old('bisa_direpair', 'bisa') === 'bisa' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-primary fs-6 ms-1" for="repairBisa">
                                                <i class="bi bi-wrench-adjustable me-1"></i> MASIH BISA DI-REPAIR (Benarkan)
                                            </label>
                                        </div>
                                        <p class="small text-muted mt-2 ps-4 mb-0">
                                            Komponen mekanis masih dapat disetel/dilumasi, contact point dapat disesuaikan, atau jarum dapat dikalibrasi ulang oleh Teknisi.
                                        </p>
                                    </label>
                                </div>

                                {{-- Opsi: Tidak Bisa Direpair --}}
                                <div class="col-md-6">
                                    <label class="card p-3 border-2 h-100 cursor-pointer repair-choice-card" id="cardRepairTidakBisa" style="cursor: pointer;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bisa_direpair" id="repairTidakBisa"
                                                value="tidak_bisa" {{ old('bisa_direpair') === 'tidak_bisa' ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-danger fs-6 ms-1" for="repairTidakBisa">
                                                <i class="bi bi-x-octagon-fill me-1"></i> TIDAK BISA DI-REPAIR (Konfirmasi Admin & User)
                                            </label>
                                        </div>
                                        <p class="small text-muted mt-2 ps-4 mb-0">
                                            Kerusakan fatal/permanen (sensor optik pecah, spindle patah bodi deformasi). 
                                            <strong>Wajib dikonfirmasikan ke Hak Akses Admin dan User terkait.</strong>
                                        </p>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- SUB-BLOK: Jika BISA DIREPAIR (Pilihan Benarkan Sekarang vs Dalam Perbaikan) --}}
                        <div id="sectionSubRepairBisa" class="p-3 bg-light rounded border mb-4">
                            <label class="form-label fw-bold text-primary">
                                <i class="bi bi-gear-wide-connected me-1"></i> Pilihan Tindakan Perbaikan Teknisi: <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check p-3 bg-white rounded border">
                                        <input class="form-check-input ms-1" type="radio" name="tindakan_repair" id="repairLangsung"
                                            value="langsung_selesai" {{ old('tindakan_repair', 'langsung_selesai') === 'langsung_selesai' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-2" for="repairLangsung">
                                            <strong>Langsung Dibenarkan Sekarang (Selesai)</strong>
                                            <div class="small text-muted">
                                                Teknisi telah berhasil membenarkan & mengkalibrasi alat. Seluruh stok unit langsung dipulihkan ke inventaris.
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check p-3 bg-white rounded border">
                                        <input class="form-check-input ms-1" type="radio" name="tindakan_repair" id="repairAntrian"
                                            value="dalam_perbaikan" {{ old('tindakan_repair') === 'dalam_perbaikan' ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark ms-2" for="repairAntrian">
                                            <strong>Masuk Status "Dalam Perbaikan"</strong>
                                            <div class="small text-muted">
                                                Alat masuk antrian bengkel perbaikan teknisi. Teknisi dapat membenarkannya nanti di tab Perbaikan.
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="catatan_perbaikan" class="form-label fw-bold small text-secondary">
                                    Catatan Tindakan Perbaikan / Langkah Kalibrasi:
                                </label>
                                <textarea name="catatan_perbaikan" id="catatan_perbaikan" rows="2" class="form-control"
                                    placeholder="Contoh: Pembersihan spindle, pelumasan bearing, penyesuaian baut contact point, kalibrasi ulang dial gauge ke toleransi 0.01mm">{{ old('catatan_perbaikan') }}</textarea>
                            </div>
                        </div>

                        {{-- SUB-BLOK: Jika TIDAK BISA DIREPAIR (Pemberitahuan Konfirmasi ke Admin dan User) --}}
                        <div id="sectionSubRepairTidakBisa" class="p-3 bg-danger-subtle rounded border border-danger mb-4" style="display: none;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-shield-exclamation text-danger fs-4"></i>
                                <div>
                                    <h6 class="fw-bold text-danger mb-1">Eskalasi Konfirmasi Hak Akses Admin & User Terkait</h6>
                                    <p class="small text-danger-emphasis mb-0">
                                        Karena alat dinyatakan <strong>TIDAK BISA DIREPAIR</strong>, sistem akan membuat status 
                                        <strong>"Menunggu Konfirmasi Kerusakan"</strong>. Notifikasi dan tanda konfirmasi akan dikirimkan ke:
                                    </p>
                                    <ul class="small text-danger-emphasis mb-1 mt-1 ps-3">
                                        <li><strong>Hak Akses Admin:</strong> Untuk keputusan pemotongan total aset (afkir) atau penagihan pertanggungjawaban.</li>
                                        <li><strong>Hak Akses User Peminjam (PIC: {{ $peminjaman->pic }}):</strong> Untuk konfirmasi dan pemberian tanggapan resmi.</li>
                                    </ul>
                                    <div class="small text-muted fst-italic">
                                        * Unit yang rusak permanen tidak akan dikembalikan ke inventaris siap pakai.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan / Temuan Teknisi --}}
                    <div class="mb-4">
                        <label for="catatan_teknisi" class="form-label fw-bold">
                            Catatan Teknisi / Uraian Temuan Kerusakan
                            <span class="text-danger" id="catatanRequiredStar" style="display: none;">* (Wajib diisi jika ada kerusakan)</span>
                        </label>
                        <textarea name="catatan_teknisi" id="catatan_teknisi" rows="4"
                            class="form-control @error('catatan_teknisi') is-invalid @enderror"
                            placeholder="Contoh: Bodi mulus, pergerakan spindle lancar, contact point bebas aus, uji titik nol presisi">{{ old('catatan_teknisi') }}</textarea>
                        <div class="form-text">
                            Berikan uraian detail mengenai kondisi fisik alat dan catatan kalibrasi jika ditemukan hal penting.
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('teknisi.antrian') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan & Selesaikan Pengecekan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalQty = {{ $totalQty }};
            const radioBaik = document.getElementById('kondisiBaik');
            const radioRusak = document.getElementById('kondisiRusak');
            const cardBaik = document.getElementById('cardBaik');
            const cardRusak = document.getElementById('cardRusak');
            const sectionKerusakan = document.getElementById('sectionKerusakan');
            const inputJumlahRusak = document.getElementById('jumlah_rusak');
            const labelUnitBaik = document.getElementById('labelUnitBaik');
            const labelUnitRusak = document.getElementById('labelUnitRusak');
            const alertKerusakanBerat = document.getElementById('alertKerusakanBerat');
            const highlightJumlahRusak = document.getElementById('highlightJumlahRusak');
            const statusStokRusakKeterangan = document.getElementById('statusStokRusakKeterangan');

            const repairBisa = document.getElementById('repairBisa');
            const repairTidakBisa = document.getElementById('repairTidakBisa');
            const cardRepairBisa = document.getElementById('cardRepairBisa');
            const cardRepairTidakBisa = document.getElementById('cardRepairTidakBisa');
            const sectionSubRepairBisa = document.getElementById('sectionSubRepairBisa');
            const sectionSubRepairTidakBisa = document.getElementById('sectionSubRepairTidakBisa');

            const catatan = document.getElementById('catatan_teknisi');
            const star = document.getElementById('catatanRequiredStar');

            function updateDisplay() {
                if (radioBaik.checked) {
                    cardBaik.classList.add('border-success', 'bg-success-subtle');
                    cardRusak.classList.remove('border-danger', 'bg-danger-subtle');
                    sectionKerusakan.style.display = 'none';
                    star.style.display = 'none';
                    catatan.placeholder = 'Contoh: Bodi mulus, spindle lancar, jarum kembali tepat ke nol.';
                } else if (radioRusak.checked) {
                    cardRusak.classList.add('border-danger', 'bg-danger-subtle');
                    cardBaik.classList.remove('border-success', 'bg-success-subtle');
                    sectionKerusakan.style.display = 'block';
                    star.style.display = 'inline';
                    catatan.placeholder = 'Jelaskan rincian kerusakan atau masalah kepresisian alat ukur...';
                }

                // Kalkulasi unit rusak & baik
                let rusak = parseInt(inputJumlahRusak.value) || 0;
                if (rusak < 1) rusak = 1;
                if (rusak > totalQty) rusak = totalQty;
                inputJumlahRusak.value = rusak;

                let baik = Math.max(0, totalQty - rusak);
                labelUnitBaik.textContent = baik;
                labelUnitRusak.textContent = rusak;

                // Tanda jika barang rusak 3 atau lebih
                if (rusak >= 3) {
                    alertKerusakanBerat.style.display = 'flex';
                    highlightJumlahRusak.textContent = rusak;
                } else {
                    alertKerusakanBerat.style.display = 'none';
                }

                // Keputusan Repair
                if (repairBisa && repairBisa.checked) {
                    cardRepairBisa.classList.add('border-primary', 'bg-primary-subtle');
                    cardRepairTidakBisa.classList.remove('border-danger', 'bg-danger-subtle');
                    sectionSubRepairBisa.style.display = 'block';
                    sectionSubRepairTidakBisa.style.display = 'none';
                    statusStokRusakKeterangan.textContent = 'Dapat dibenarkan teknisi';
                } else if (repairTidakBisa && repairTidakBisa.checked) {
                    cardRepairTidakBisa.classList.add('border-danger', 'bg-danger-subtle');
                    cardRepairBisa.classList.remove('border-primary', 'bg-primary-subtle');
                    sectionSubRepairBisa.style.display = 'none';
                    sectionSubRepairTidakBisa.style.display = 'block';
                    statusStokRusakKeterangan.textContent = 'Menunggu konfirmasi Admin & User';
                }
            }

            radioBaik.addEventListener('change', updateDisplay);
            radioRusak.addEventListener('change', updateDisplay);
            inputJumlahRusak.addEventListener('input', updateDisplay);

            if (repairBisa) repairBisa.addEventListener('change', updateDisplay);
            if (repairTidakBisa) repairTidakBisa.addEventListener('change', updateDisplay);

            updateDisplay();
        });
    </script>
@endpush
