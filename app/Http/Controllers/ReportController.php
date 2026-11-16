<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman filter dan preview laporan peminjaman alat ukur
     */
    public function index(Request $request): View
    {
        $filter = $this->resolveFilterParams($request);
        $data = $this->getReportData($filter);
        $departments = Peminjaman::select('department')->whereNotNull('department')->distinct()->pluck('department');

        return view('admin.reports.index', array_merge($data, $filter, [
            'departments' => $departments,
        ]));
    }

    /**
     * Ekspor laporan ke format PDF (Tampilan Siap Cetak Dokumen Resmi)
     */
    public function exportPdf(Request $request): View
    {
        $filter = $this->resolveFilterParams($request);
        $data = $this->getReportData($filter);

        return view('admin.reports.pdf', array_merge($data, $filter));
    }

    /**
     * Ekspor laporan ke format Microsoft Word (.doc / .docx)
     */
    public function exportWord(Request $request): Response
    {
        $filter = $this->resolveFilterParams($request);
        $data = $this->getReportData($filter);

        $content = view('admin.reports.word', array_merge($data, $filter))->render();

        $cleanPeriod = preg_replace('/[^A-Za-z0-9_-]/', '_', str_replace([' ', '/', '\\'], '_', $filter['periodText']));
        $filename = "Laporan_Peminjaman_Alat_Ukur_{$cleanPeriod}.doc";

        return response($content, 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-cache, must-revalidate',
            'Expires' => '0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * Helper untuk memproses dan menormalkan parameter filter (Harian, Rentang Hari, Bulanan)
     *
     * @return array<string, mixed>
     */
    private function resolveFilterParams(Request $request): array
    {
        $mode = $request->get('filter_mode');
        $days = $request->filled('days') ? (int) $request->get('days') : null;
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        // Deteksi mode filter jika belum ditentukan secara eksplisit
        if (! $mode) {
            if ($days !== null || $request->filled('start_date') || $request->filled('end_date')) {
                $mode = 'range';
            } else {
                $mode = 'monthly';
            }
        }

        $selectedMonth = (int) $request->get('month', now()->month);
        $selectedYear = (int) $request->get('year', now()->year);
        $selectedDepartment = $request->get('department', 'all') ?? 'all';
        $selectedStatus = $request->get('status', 'all') ?? 'all';

        $startDate = null;
        $endDate = null;
        $periodText = '';
        $reportTitle = 'LAPORAN SIRKULASI & PEMINJAMAN ALAT UKUR PRESISI';

        if ($mode === 'range') {
            if ($days !== null && $days > 0) {
                $endDate = now()->toDateString();
                $startDate = now()->subDays($days - 1)->toDateString();
            } else {
                try {
                    $startDate = $startDateInput ? Carbon::parse($startDateInput)->toDateString() : now()->toDateString();
                } catch (\Exception) {
                    $startDate = now()->toDateString();
                }

                try {
                    $endDate = $endDateInput ? Carbon::parse($endDateInput)->toDateString() : $startDate;
                } catch (\Exception) {
                    $endDate = $startDate;
                }

                // Pastikan urutan tanggal valid jika terbalik
                if ($startDate > $endDate) {
                    [$startDate, $endDate] = [$endDate, $startDate];
                }
            }

            $carbonStart = Carbon::parse($startDate)->locale('id');
            $carbonEnd = Carbon::parse($endDate)->locale('id');

            if ($startDate === $endDate) {
                if ($carbonStart->isToday()) {
                    $periodText = 'Hari Ini (' . $carbonStart->translatedFormat('d F Y') . ')';
                } elseif ($carbonStart->isYesterday()) {
                    $periodText = 'Kemarin (' . $carbonStart->translatedFormat('d F Y') . ')';
                } else {
                    $periodText = $carbonStart->translatedFormat('d F Y') . ' (1 Hari)';
                }
                $reportTitle = 'LAPORAN HARIAN SIRKULASI ALAT UKUR PRESISI';
            } else {
                $diffDays = $carbonStart->diffInDays($carbonEnd) + 1;
                $periodText = $carbonStart->translatedFormat('d F Y') . ' s/d ' . $carbonEnd->translatedFormat('d F Y') . " ({$diffDays} Hari)";
                $reportTitle = "LAPORAN SIRKULASI ALAT UKUR PRESISI ({$diffDays} HARI)";
            }
        } else {
            $mode = 'monthly';
            $monthObj = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id');
            $startDate = $monthObj->copy()->startOfMonth()->toDateString();
            $endDate = $monthObj->copy()->endOfMonth()->toDateString();
            $monthName = $monthObj->translatedFormat('F');
            $periodText = "Bulan {$monthName} {$selectedYear}";
            $reportTitle = 'LAPORAN BULANAN SIRKULASI & PEMINJAMAN ALAT UKUR PRESISI';
        }

        $monthName = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->locale('id')->translatedFormat('F');

        return [
            'filterMode' => $mode,
            'selectedDays' => $days,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'selectedDepartment' => $selectedDepartment,
            'selectedStatus' => $selectedStatus,
            'periodText' => $periodText,
            'reportTitle' => $reportTitle,
            'monthName' => $monthName,
        ];
    }

    /**
     * Helper untuk mengambil dan mengolah data agregasi laporan
     *
     * @param array<string, mixed> $filter
     * @return array<string, mixed>
     */
    private function getReportData(array $filter): array
    {
        $query = Peminjaman::with(['user', 'details.tool', 'pengembalian.teknisi']);

        if ($filter['filterMode'] === 'range') {
            $query->whereBetween('tanggal_pinjam', [$filter['startDate'], $filter['endDate']]);
        } else {
            $query->whereMonth('tanggal_pinjam', $filter['selectedMonth'])
                ->whereYear('tanggal_pinjam', $filter['selectedYear']);
        }

        if ($filter['selectedDepartment'] !== 'all' && ! empty($filter['selectedDepartment'])) {
            $query->where('department', $filter['selectedDepartment']);
        }

        if ($filter['selectedStatus'] !== 'all' && ! empty($filter['selectedStatus'])) {
            $query->where('status', $filter['selectedStatus']);
        }

        $peminjaman = $query->latest('tanggal_pinjam')->get();

        $totalTransaksi = $peminjaman->count();
        $totalUnitAlat = $peminjaman->flatMap->details->sum('qty');
        $selesaiBaik = $peminjaman->where('status', 'selesai')->count();
        $rusak = $peminjaman->whereIn('status', ['rusak', 'menunggu_konfirmasi', 'rusak_dikonfirmasi'])->count();
        $dalamPerbaikan = $peminjaman->where('status', 'dalam_perbaikan')->count();
        $masihDipinjam = $peminjaman->whereIn('status', ['dipinjam', 'menunggu_cek_teknisi', 'dalam_perbaikan'])->count();

        // Rekapitulasi per jenis alat ukur
        $alatStats = DetailPeminjaman::whereIn('peminjaman_id', $peminjaman->pluck('id'))
            ->join('tools', 'detail_peminjamen.tool_id', '=', 'tools.id')
            ->select('tools.nama_alat', 'tools.kode_alat', DB::raw('SUM(detail_peminjamen.qty) as total_qty'), DB::raw('COUNT(detail_peminjamen.id) as total_dipinjam_kali'))
            ->groupBy('tools.id', 'tools.nama_alat', 'tools.kode_alat')
            ->orderByDesc('total_qty')
            ->get();

        // Rekapitulasi per departemen
        $deptStats = $peminjaman->groupBy('department')->map(function ($items) {
            return [
                'total_transaksi' => $items->count(),
                'total_unit' => $items->flatMap->details->sum('qty'),
                'selesai' => $items->where('status', 'selesai')->count(),
                'rusak' => $items->whereIn('status', ['rusak', 'menunggu_konfirmasi', 'rusak_dikonfirmasi'])->count(),
                'dalam_perbaikan' => $items->where('status', 'dalam_perbaikan')->count(),
                'aktif' => $items->whereIn('status', ['dipinjam', 'menunggu_cek_teknisi', 'dalam_perbaikan'])->count(),
            ];
        });

        return [
            'peminjaman' => $peminjaman,
            'totalTransaksi' => $totalTransaksi,
            'totalUnitAlat' => $totalUnitAlat,
            'selesaiBaik' => $selesaiBaik,
            'rusak' => $rusak,
            'dalamPerbaikan' => $dalamPerbaikan,
            'masihDipinjam' => $masihDipinjam,
            'alatStats' => $alatStats,
            'deptStats' => $deptStats,
        ];
    }
}
