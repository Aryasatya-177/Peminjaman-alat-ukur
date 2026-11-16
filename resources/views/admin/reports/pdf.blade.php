<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman Alat Ukur - {{ $periodText }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000000;
            background-color: #ffffff;
            font-size: 11pt;
            padding: 20px;
        }

        .header-kop {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }

        .table-laporan th,
        .table-laporan td {
            border: 1px solid #000000;
            padding: 5px 8px;
        }

        .table-laporan th {
            background-color: #f2f2f2 !important;
            text-align: center;
            font-weight: bold;
        }

        .box-summary {
            border: 1px solid #000;
            padding: 8px 12px;
            margin-bottom: 20px;
            background-color: #fcfcfc;
        }

        .signature-table {
            width: 100%;
            margin-top: 35px;
            text-align: center;
        }

        .signature-table td {
            width: 33.33%;
            vertical-align: top;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
        }
    </style>
</head>

<body>
    {{-- Tombol Tindakan Cetak / Tutup --}}
    <div class="no-print mb-4 p-3 bg-light border rounded d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-info-circle-fill text-primary me-2"></i>
            <strong>Pratinjau Dokumen PDF Resmi</strong> - Klik tombol di samping untuk mencetak atau menyimpan dokumen sebagai file PDF.
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm fw-bold">
                <i class="bi bi-printer me-1"></i> Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                Tutup
            </button>
        </div>
    </div>

    {{-- KOP SURAT PERUSAHAAN --}}
    <div class="header-kop d-flex align-items-center justify-content-between">
        <div style="width: 15%; text-align: center;">
            <div style="font-size: 32pt; font-weight: bold;">&#9881;</div>
        </div>
        <div style="width: 70%; text-align: center;">
            <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px;">GAUGE HUB &bull; PT. PRESISI TEKNIK MANUFAKTUR</h4>
            <h6 class="mb-1">DIVISI QUALITY CONTROL & INVENTORY TOOL CRIB</h6>
            <small class="text-muted">Kawasan Industri Manufaktur Blok B-12, Cikarang - Jawa Barat | Telp: (021) 8989-001 | Email: qc-tooling@presisimanufaktur.co.id</small>
        </div>
        <div style="width: 15%; text-align: right;">
            <small class="border p-1 d-inline-block font-monospace">FORM: QC-TL-04<br>REV: 02</small>
        </div>
    </div>

    {{-- JUDUL LAPORAN --}}
    <div class="header-title">
        <h5 class="fw-bold text-uppercase mb-1" style="text-decoration: underline;">
            {{ $reportTitle ?? 'LAPORAN SIRKULASI & PEMINJAMAN ALAT UKUR PRESISI' }}
        </h5>
        <div>Periode: <strong>{{ $periodText }}</strong></div>
        @if($selectedDepartment !== 'all')
            <small>Departemen: <strong>{{ $selectedDepartment }}</strong></small>
        @endif
        @if($selectedStatus !== 'all')
            <small class="ms-2">| Status: <strong>{{ strtoupper(str_replace('_', ' ', $selectedStatus)) }}</strong></small>
        @endif
    </div>

    {{-- RINGKASAN REKAPITULASI --}}
    <div class="box-summary">
        <div class="row text-center">
            <div class="col-3 border-end">
                <div class="small fw-bold text-muted">TOTAL TRANSAKSI</div>
                <div class="fs-5 fw-bold">{{ $totalTransaksi }} Transaksi</div>
            </div>
            <div class="col-3 border-end">
                <div class="small fw-bold text-muted">TOTAL UNIT ALAT</div>
                <div class="fs-5 fw-bold">{{ $totalUnitAlat }} Unit</div>
            </div>
            <div class="col-3 border-end">
                <div class="small fw-bold text-muted">DIKEMBALIKAN (BAIK)</div>
                <div class="fs-5 fw-bold text-success">{{ $selesaiBaik }} Transaksi</div>
            </div>
            <div class="col-3">
                <div class="small fw-bold text-muted">KERUSAKAN / CACAT</div>
                <div class="fs-5 fw-bold text-danger">{{ $rusak }} Kasus</div>
            </div>
        </div>
    </div>

    {{-- TABEL RINCIAN TRANSAKSI --}}
    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">No. Transaksi</th>
                <th style="width: 18%;">Departemen & PIC</th>
                <th style="width: 28%;">Nama Alat Ukur Presisi & Kode</th>
                <th style="width: 6%;">Qty</th>
                <th style="width: 16%;">Rentang Tanggal</th>
                <th style="width: 18%;">Hasil Cek Teknisi & Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($peminjaman as $p)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td>
                        <strong>{{ $p->department }}</strong><br>
                        <small>PIC: {{ $p->pic }}</small>
                    </td>
                    <td>
                        @foreach($p->details as $d)
                            <div>&bull; [{{ $d->tool->kode_alat ?? '-' }}] {{ $d->tool->nama_alat ?? '-' }} ({{ $d->qty }} unit)</div>
                        @endforeach
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $p->details->sum('qty') }}
                    </td>
                    <td>
                        <small>
                            Pinjam: {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}<br>
                            Kembali: {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                        </small>
                    </td>
                    <td>
                        @if($p->status === 'selesai')
                            <strong class="text-success">&#10004; BAIK</strong>
                        @elseif($p->status === 'rusak')
                            <strong class="text-danger">&#10008; RUSAK</strong>
                        @elseif($p->status === 'dipinjam')
                            <span>DIPINJAM</span>
                        @elseif($p->status === 'menunggu_cek_teknisi')
                            <span>MENUNGGU CEK</span>
                        @endif

                        @if($p->pengembalian)
                            <br><small>Teknisi: {{ $p->pengembalian->teknisi->name ?? '-' }}</small>
                            @if($p->pengembalian->catatan_teknisi)
                                <br><small><em>"{{ $p->pengembalian->catatan_teknisi }}"</em></small>
                            @endif
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        Tidak ada transaksi peminjaman alat ukur pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- KOLOM PENGESAHAN TIGA PIHAK --}}
    <table class="signature-table">
        <tr>
            <td>
                Dibuat Oleh,<br>
                <strong>Admin Tool Crib</strong>
                <br><br><br><br>
                <u>( ........................................ )</u><br>
                <small>Staff Inventory & Tooling</small>
            </td>
            <td>
                Diperiksa Oleh,<br>
                <strong>Teknisi Kalibrasi & QC</strong>
                <br><br><br><br>
                <u>( ........................................ )</u><br>
                <small>Teknisi Lab Kalibrasi</small>
            </td>
            <td>
                Mengetahui & Menyetujui,<br>
                <strong>Manager QA / QC</strong>
                <br><br><br><br>
                <u>( ........................................ )</u><br>
                <small>Head of Quality Assurance</small>
            </td>
        </tr>
    </table>
</body>

</html>
