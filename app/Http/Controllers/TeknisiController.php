<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeknisiController extends Controller
{
    /**
     * Tampilkan dashboard teknisi kalibrasi & inspeksi
     */
    public function dashboard(): View
    {
        $antrianCount = Peminjaman::where('status', 'menunggu_cek_teknisi')->count();
        $perbaikanCount = Peminjaman::where('status', 'dalam_perbaikan')->count();
        $konfirmasiCount = Peminjaman::where('status', 'menunggu_konfirmasi')->count();
        $dicekHariIni = Pengembalian::whereDate('tanggal_dikembalikan', today())->count();
        $totalBaik = Pengembalian::where('kondisi_alat', 'baik')->count();
        $totalRusak = Pengembalian::where('kondisi_alat', 'rusak')->count();
        $rusakBeratCount = Pengembalian::where('tanda_kerusakan_berat', true)->count();

        $antrianTerbaru = Peminjaman::where('status', 'menunggu_cek_teknisi')
            ->with(['user', 'details.tool'])
            ->oldest()
            ->take(5)
            ->get();

        $perbaikanTerbaru = Peminjaman::where('status', 'dalam_perbaikan')
            ->with(['user', 'details.tool', 'pengembalian'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $konfirmasiTerbaru = Peminjaman::where('status', 'menunggu_konfirmasi')
            ->with(['user', 'details.tool', 'pengembalian'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $inspeksiTerbaru = Pengembalian::with(['peminjaman.user', 'peminjaman.details.tool', 'teknisi'])
            ->latest('tanggal_dikembalikan')
            ->take(5)
            ->get();

        return view('teknisi.dashboard', compact(
            'antrianCount',
            'perbaikanCount',
            'konfirmasiCount',
            'rusakBeratCount',
            'dicekHariIni',
            'totalBaik',
            'totalRusak',
            'antrianTerbaru',
            'perbaikanTerbaru',
            'konfirmasiTerbaru',
            'inspeksiTerbaru'
        ));
    }

    /**
     * Tampilkan daftar antrian peminjaman yang menunggu pengecekan teknisi / dalam perbaikan
     */
    public function antrian(Request $request): View
    {
        $tab = $request->get('tab', 'antrian'); // 'antrian', 'perbaikan', 'konfirmasi'

        $query = Peminjaman::with(['user', 'details.tool', 'pengembalian']);

        if ($tab === 'perbaikan') {
            $query->where('status', 'dalam_perbaikan');
        } elseif ($tab === 'konfirmasi') {
            $query->where('status', 'menunggu_konfirmasi');
        } else {
            $query->where('status', 'menunggu_cek_teknisi');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('department', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('details.tool', function ($tq) use ($search) {
                        $tq->where('nama_alat', 'like', "%{$search}%")
                            ->orWhere('kode_alat', 'like', "%{$search}%");
                    });
            });
        }

        $antrian = $query->oldest('updated_at')->paginate(10)->withQueryString();

        $counts = [
            'antrian' => Peminjaman::where('status', 'menunggu_cek_teknisi')->count(),
            'perbaikan' => Peminjaman::where('status', 'dalam_perbaikan')->count(),
            'konfirmasi' => Peminjaman::where('status', 'menunggu_konfirmasi')->count(),
        ];

        return view('teknisi.antrian', compact('antrian', 'tab', 'counts'));
    }

    /**
     * Tampilkan formulir uji kondisi fisik & presisi alat ukur
     */
    public function checkForm(int $id): View
    {
        $peminjaman = Peminjaman::with(['user', 'details.tool', 'pengembalian'])
            ->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_cek_teknisi') {
            abort(400, 'Status peminjaman ini bukan menunggu pengecekan teknisi.');
        }

        $totalQty = $peminjaman->details->sum('qty');

        return view('teknisi.cek', compact('peminjaman', 'totalQty'));
    }

    /**
     * Simpan hasil pengecekan teknisi dan evaluasi perbaikan / konfirmasi admin & user
     */
    public function storeCheck(Request $request, int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::with('details.tool')->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_cek_teknisi') {
            return redirect()->route('teknisi.antrian')
                ->with('error', 'Transaksi peminjaman ini tidak dalam status menunggu pengecekan teknisi.');
        }

        $totalQty = (int) $peminjaman->details->sum('qty');

        $validated = $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak',
            'jumlah_rusak' => 'nullable|integer|min:0|max:'.$totalQty,
            'bisa_direpair' => 'nullable|required_if:kondisi_alat,rusak|in:bisa,tidak_bisa',
            'tindakan_repair' => 'nullable|required_if:bisa_direpair,bisa|in:langsung_selesai,dalam_perbaikan',
            'catatan_teknisi' => 'nullable|string|max:1000',
            'catatan_perbaikan' => 'nullable|string|max:1000',
            'detail_rusak' => 'nullable|array',
        ], [
            'kondisi_alat.required' => 'Pilih kondisi akhir alat ukur (Baik atau Rusak).',
            'kondisi_alat.in' => 'Kondisi alat tidak valid.',
            'bisa_direpair.required_if' => 'Tentukan apakah alat ukur yang rusak masih bisa diperbaiki/direpair.',
            'tindakan_repair.required_if' => 'Pilih tindakan perbaikan (Langsung Dibenarkan Sekarang atau Masuk Proses Perbaikan).',
        ]);

        if ($validated['kondisi_alat'] === 'rusak' && empty($validated['catatan_teknisi'])) {
            return back()->withInput()->withErrors([
                'catatan_teknisi' => 'Harap berikan catatan deskripsi kerusakan alat untuk keperluan tindak lanjut atau konfirmasi.',
            ]);
        }

        DB::beginTransaction();
        try {
            $isBaik = $validated['kondisi_alat'] === 'baik';
            $jumlahRusak = $isBaik ? 0 : (int) ($validated['jumlah_rusak'] ?? $totalQty);
            if (! $isBaik && $jumlahRusak <= 0) {
                $jumlahRusak = $totalQty; // default jika user memilih rusak tapi mengisi 0
            }
            $jumlahBaik = max(0, $totalQty - $jumlahRusak);

            // Deteksi penanda jika barang yang dipinjam rusak 3 atau lebih
            $tandaKerusakanBerat = ($jumlahRusak >= 3);

            $bisaDirepair = ! $isBaik ? ($validated['bisa_direpair'] === 'bisa') : null;
            $tindakanRepair = $validated['tindakan_repair'] ?? null;
            $catatanPerbaikan = $validated['catatan_perbaikan'] ?? null;

            // Tentukan status peminjaman & status repair
            $statusRepair = 'tidak_perlu';
            $statusPeminjaman = 'selesai';
            $successMsg = '';

            if ($isBaik) {
                // Semua alat baik -> kembalikan seluruh stok
                $statusPeminjaman = 'selesai';
                $statusRepair = 'tidak_perlu';

                foreach ($peminjaman->details as $detail) {
                    $tool = Tool::lockForUpdate()->find($detail->tool_id);
                    if ($tool) {
                        $tool->available_stock = min($tool->total_stock, $tool->available_stock + $detail->qty);
                        $tool->save();
                    }
                }

                $successMsg = 'Pengecekan selesai. Seluruh alat ('.$totalQty.' unit) dinyatakan BAIK dan stok telah dikembalikan ke inventaris.';

            } elseif ($bisaDirepair) {
                // Rusak tapi BISA DIREPAIR
                if ($tindakanRepair === 'langsung_selesai') {
                    // BENARKAN LANGSUNG: Seluruh unit (baik + unit yang berhasil dibenarkan) dikembalikan ke stok
                    $statusPeminjaman = 'selesai';
                    $statusRepair = 'selesai_direpair';

                    foreach ($peminjaman->details as $detail) {
                        $tool = Tool::lockForUpdate()->find($detail->tool_id);
                        if ($tool) {
                            $tool->available_stock = min($tool->total_stock, $tool->available_stock + $detail->qty);
                            $tool->save();
                        }
                    }

                    if (empty($catatanPerbaikan)) {
                        $catatanPerbaikan = 'Alat yang rusak ('.$jumlahRusak.' unit) telah langsung diperbaiki/dibenarkan teknisi dan dikalibrasi ulang.';
                    }

                    $successMsg = 'Pengecekan selesai. Alat yang rusak ('.$jumlahRusak.' unit'.($tandaKerusakanBerat ? ' - RUSAK ≥ 3 UNIT' : '').') BERHASIL DIBENARKAN/DIPERBAIKI teknisi dan seluruh stok telah dikembalikan normal ke inventaris.';

                } else {
                    // MASUK DALAM PERBAIKAN: Unit yang baik dikembalikan stoknya, unit rusak ditahan di bengkel teknisi
                    $statusPeminjaman = 'dalam_perbaikan';
                    $statusRepair = 'dalam_perbaikan';

                    // Kembalikan hanya unit yang baik ke stok jika ada
                    $sisaBaikUntukDikembalikan = $jumlahBaik;
                    foreach ($peminjaman->details as $detail) {
                        $tool = Tool::lockForUpdate()->find($detail->tool_id);
                        if ($tool && $sisaBaikUntukDikembalikan > 0) {
                            $kembalikan = min($detail->qty, $sisaBaikUntukDikembalikan);
                            $tool->available_stock = min($tool->total_stock, $tool->available_stock + $kembalikan);
                            $tool->save();
                            $sisaBaikUntukDikembalikan -= $kembalikan;
                        }
                    }

                    $successMsg = 'Alat rusak ('.$jumlahRusak.' unit'.($tandaKerusakanBerat ? ' - RUSAK ≥ 3 UNIT' : '').') dinyatakan BISA DIREPAIR dan statusnya diubah menjadi DALAM PERBAIKAN. Teknisi dapat membenarkan alat pada tab Perbaikan jika telah selesai.';
                }

            } else {
                // TIDAK BISA DIREPAIR: Kirim konfirmasi ke Hak Akses Admin dan User Terkait
                $statusPeminjaman = 'menunggu_konfirmasi';
                $statusRepair = 'tidak_bisa_direpair';

                // Kembalikan hanya unit yang baik ke inventaris
                $sisaBaikUntukDikembalikan = $jumlahBaik;
                foreach ($peminjaman->details as $detail) {
                    $tool = Tool::lockForUpdate()->find($detail->tool_id);
                    if ($tool && $sisaBaikUntukDikembalikan > 0) {
                        $kembalikan = min($detail->qty, $sisaBaikUntukDikembalikan);
                        $tool->available_stock = min($tool->total_stock, $tool->available_stock + $kembalikan);
                        $tool->save();
                        $sisaBaikUntukDikembalikan -= $kembalikan;
                    }
                }

                $badgeKerusakan = $tandaKerusakanBerat ? ' [PERINGATAN: KERUSAKAN BERAT ≥ 3 UNIT]' : '';
                $successMsg = 'Pengecekan selesai. Alat dinyatakan TIDAK BISA DIREPAIR'.$badgeKerusakan.' (Rusak: '.$jumlahRusak.' unit). Laporan konfirmasi telah otomatis dikirimkan ke Hak Akses Admin dan User terkait!';
            }

            // Simpan atau update rincian detail peminjaman jika ada
            if ($request->has('detail_rusak') && is_array($request->detail_rusak)) {
                foreach ($peminjaman->details as $detail) {
                    $itemRusak = isset($request->detail_rusak[$detail->id]) ? (int) $request->detail_rusak[$detail->id] : 0;
                    $itemBaik = max(0, $detail->qty - $itemRusak);
                    $detail->update([
                        'qty_rusak' => $itemRusak,
                        'qty_baik' => $itemBaik,
                        'kondisi_detail' => $itemRusak > 0 ? 'rusak' : 'baik',
                    ]);
                }
            }

            // Simpan riwayat pengecekan ke tabel pengembalians
            Pengembalian::updateOrCreate(
                ['peminjaman_id' => $peminjaman->id],
                [
                    'teknisi_id' => Auth::id(),
                    'tanggal_dikembalikan' => now()->toDateString(),
                    'kondisi_alat' => $validated['kondisi_alat'],
                    'catatan_teknisi' => $validated['catatan_teknisi'] ?? null,
                    'jumlah_baik' => $jumlahBaik,
                    'jumlah_rusak' => $jumlahRusak,
                    'tanda_kerusakan_berat' => $tandaKerusakanBerat,
                    'bisa_direpair' => $bisaDirepair,
                    'status_repair' => $statusRepair,
                    'catatan_perbaikan' => $catatanPerbaikan,
                    'konfirmasi_admin' => false,
                    'konfirmasi_user' => false,
                ]
            );

            // Perbarui status transaksi peminjaman
            $peminjaman->status = $statusPeminjaman;
            $peminjaman->save();

            DB::commit();

            return redirect()->route('teknisi.antrian', ['tab' => $statusPeminjaman === 'dalam_perbaikan' ? 'perbaikan' : ($statusPeminjaman === 'menunggu_konfirmasi' ? 'konfirmasi' : 'antrian')])
                ->with('success', $successMsg);

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses hasil inspeksi teknisi: '.$e->getMessage());
        }
    }

    /**
     * Teknisi membenarkan alat yang sebelumnya berstatus dalam_perbaikan dan mengembalikannya ke inventaris
     */
    public function selesaikanRepair(Request $request, int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::with(['details.tool', 'pengembalian'])->findOrFail($id);

        if ($peminjaman->status !== 'dalam_perbaikan') {
            return back()->with('error', 'Transaksi peminjaman ini tidak dalam status perbaikan.');
        }

        $validated = $request->validate([
            'catatan_perbaikan' => 'required|string|max:1000',
        ], [
            'catatan_perbaikan.required' => 'Wajib memberikan catatan teknis perbaikan yang telah dilakukan.',
        ]);

        DB::beginTransaction();
        try {
            $pengembalian = $peminjaman->pengembalian;
            $jumlahRusakSebelumnya = $pengembalian ? (int) $pengembalian->jumlah_rusak : $peminjaman->details->sum('qty');

            // Kembalikan sisa stok unit yang diperbaiki ke available_stock
            $sisaKembali = $jumlahRusakSebelumnya;
            foreach ($peminjaman->details as $detail) {
                $tool = Tool::lockForUpdate()->find($detail->tool_id);
                if ($tool && $sisaKembali > 0) {
                    $qtyToReturn = min($detail->qty, $sisaKembali);
                    $tool->available_stock = min($tool->total_stock, $tool->available_stock + $qtyToReturn);
                    $tool->save();
                    $sisaKembali -= $qtyToReturn;
                }
            }

            // Update status pengembalian
            if ($pengembalian) {
                $catatanLama = $pengembalian->catatan_perbaikan ? $pengembalian->catatan_perbaikan."\n---\n" : '';
                $pengembalian->update([
                    'status_repair' => 'selesai_direpair',
                    'catatan_perbaikan' => $catatanLama.'[Dibenarkan pada '.now()->translatedFormat('d F Y, H:i').' oleh '.Auth::user()->name.']: '.$validated['catatan_perbaikan'],
                ]);
            }

            // Ubah status peminjaman menjadi selesai
            $peminjaman->status = 'selesai';
            $peminjaman->save();

            DB::commit();

            return redirect()->route('teknisi.antrian', ['tab' => 'perbaikan'])
                ->with('success', 'Alat berhasil DIBENARKAN/DIPERBAIKI! Stok telah dipulihkan ke inventaris dan status peminjaman telah SELESAI.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses penyelesaian perbaikan: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan riwayat seluruh inspeksi teknisi
     */
    public function history(Request $request): View
    {
        $query = Pengembalian::with(['peminjaman.user', 'peminjaman.details.tool', 'teknisi'])
            ->latest('tanggal_dikembalikan');

        if ($request->filled('kondisi')) {
            $query->where('kondisi_alat', $request->kondisi);
        }

        if ($request->filled('kerusakan_berat')) {
            $query->where('tanda_kerusakan_berat', true);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('peminjaman', function ($pq) use ($search) {
                $pq->where('department', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $riwayat = $query->paginate(15)->withQueryString();

        return view('teknisi.history', compact('riwayat'));
    }
}
