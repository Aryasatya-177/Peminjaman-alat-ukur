<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
<meta charset="utf-8">
<title>Laporan Peminjaman Alat Ukur Presisi - {{ $periodText }}</title>
<!--[if gte mso 9]>
<xml>
<w:WordDocument>
<w:View>Print</w:View>
<w:Zoom>100</w:Zoom>
<w:DoNotOptimizeForBrowser/>
</w:WordDocument>
</xml>
<![endif]-->
<style>
body {
    font-family: 'Calibri', 'Arial', sans-serif;
    font-size: 11pt;
    line-height: 1.3;
}
.header-kop {
    text-align: center;
    border-bottom: 2pt double #000000;
    padding-bottom: 10pt;
    margin-bottom: 15pt;
}
.company-name {
    font-size: 16pt;
    font-weight: bold;
    text-transform: uppercase;
}
.division-name {
    font-size: 12pt;
    font-weight: bold;
}
.company-address {
    font-size: 9pt;
    color: #444444;
}
.report-title {
    text-align: center;
    font-size: 13pt;
    font-weight: bold;
    text-transform: uppercase;
    text-decoration: underline;
    margin-top: 10pt;
    margin-bottom: 5pt;
}
.period {
    text-align: center;
    font-size: 11pt;
    margin-bottom: 15pt;
}
table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10pt;
    margin-bottom: 15pt;
    font-size: 10pt;
}
table.data-table th, table.data-table td {
    border: 1pt solid #000000;
    padding: 6pt;
}
table.data-table th {
    background-color: #EAEAEA;
    font-weight: bold;
    text-align: center;
}
.summary-box {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15pt;
    font-size: 10pt;
}
.summary-box td {
    border: 1pt solid #000000;
    padding: 8pt;
    text-align: center;
    background-color: #F8F9FA;
}
.signature-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30pt;
    font-size: 10pt;
}
.signature-table td {
    width: 33.33%;
    text-align: center;
    vertical-align: top;
    border: none;
}
</style>
</head>
<body>

{{-- KOP PERUSAHAAN --}}
<div class="header-kop">
    <div class="company-name">GAUGE HUB &bull; PT. PRESISI TEKNIK MANUFAKTUR</div>
    <div class="division-name">DIVISI QUALITY CONTROL & INVENTORY TOOL CRIB</div>
    <div class="company-address">Kawasan Industri Manufaktur Blok B-12, Cikarang - Jawa Barat | Telp: (021) 8989-001 | Email: qc-tooling@presisimanufaktur.co.id</div>
</div>

{{-- JUDUL LAPORAN --}}
<div class="report-title">{{ $reportTitle ?? 'LAPORAN SIRKULASI & PEMINJAMAN ALAT UKUR PRESISI' }}</div>
<div class="period">
    Periode: <strong>{{ $periodText }}</strong>
    @if($selectedDepartment !== 'all')
        | Departemen: <strong>{{ $selectedDepartment }}</strong>
    @endif
    @if($selectedStatus !== 'all')
        | Status: <strong>{{ strtoupper(str_replace('_', ' ', $selectedStatus)) }}</strong>
    @endif
</div>

{{-- KOTAK RINGKASAN --}}
<table class="summary-box">
    <tr>
        <td>
            <strong>TOTAL TRANSAKSI</strong><br>
            <span style="font-size: 14pt; font-weight: bold;">{{ $totalTransaksi }}</span> Transaksi
        </td>
        <td>
            <strong>TOTAL UNIT ALAT</strong><br>
            <span style="font-size: 14pt; font-weight: bold;">{{ $totalUnitAlat }}</span> Unit
        </td>
        <td>
            <strong>DIKEMBALIKAN (BAIK)</strong><br>
            <span style="font-size: 14pt; font-weight: bold; color: green;">{{ $selesaiBaik }}</span> Selesai
        </td>
        <td>
            <strong>KERUSAKAN / CACAT</strong><br>
            <span style="font-size: 14pt; font-weight: bold; color: red;">{{ $rusak }}</span> Kasus
        </td>
    </tr>
</table>

{{-- TABEL DATA TRANSAKSI --}}
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 12%;">No. ID</th>
            <th style="width: 20%;">Departemen & PIC</th>
            <th style="width: 30%;">Rincian Alat Ukur Presisi</th>
            <th style="width: 8%;">Total Qty</th>
            <th style="width: 13%;">Rentang Tanggal</th>
            <th style="width: 12%;">Kondisi Akhir</th>
        </tr>
    </thead>
    <tbody>
        @forelse($peminjaman as $p)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td style="text-align: center; font-weight: bold;">#{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>
                    <strong>{{ $p->department }}</strong><br>
                    PIC: {{ $p->pic }}
                </td>
                <td>
                    @foreach($p->details as $d)
                        <div>- [{{ $d->tool->kode_alat ?? '-' }}] {{ $d->tool->nama_alat ?? '-' }} ({{ $d->qty }} unit)</div>
                    @endforeach
                </td>
                <td style="text-align: center; font-weight: bold;">{{ $p->details->sum('qty') }} unit</td>
                <td>
                    {{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}<br>
                    s/d {{ \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') }}
                </td>
                <td style="text-align: center;">
                    @if($p->status === 'selesai')
                        <strong style="color: green;">BAIK</strong>
                    @elseif($p->status === 'rusak')
                        <strong style="color: red;">RUSAK</strong>
                    @elseif($p->status === 'dipinjam')
                        <span>DIPINJAM</span>
                    @elseif($p->status === 'menunggu_cek_teknisi')
                        <span>CEK TEKNISI</span>
                    @endif

                    @if($p->pengembalian && $p->pengembalian->catatan_teknisi)
                        <br><small><em>({{ $p->pengembalian->catatan_teknisi }})</em></small>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 15pt;">
                    Tidak ada transaksi peminjaman alat ukur pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- TABEL PENGESAHAN TIGA PIHAK --}}
<table class="signature-table">
    <tr>
        <td>
            Dibuat Oleh,<br>
            <strong>Admin Tool Crib</strong>
            <br><br><br><br><br>
            <u>( .................................................. )</u><br>
            Staff Inventory & Tooling
        </td>
        <td>
            Diperiksa Oleh,<br>
            <strong>Teknisi Kalibrasi & QC</strong>
            <br><br><br><br><br>
            <u>( .................................................. )</u><br>
            Teknisi Laboratorium Kalibrasi
        </td>
        <td>
            Mengetahui & Menyetujui,<br>
            <strong>Manager QA / QC</strong>
            <br><br><br><br><br>
            <u>( .................................................. )</u><br>
            Head of Quality Assurance
        </td>
    </tr>
</table>

</body>
</html>
