<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\Perpanjangan;
use App\Models\PerpanjanganDetail;
use App\Models\Tool;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan formulir peminjaman alat
     */
    public function create(Request $request)
    {
        $tools = Tool::orderByRaw("CASE WHEN available_stock > 0 THEN 0 ELSE 1 END")->orderBy('nama_alat')->get();
        $user = Auth::user();

        // Cari transaksi peminjaman aktif milik user yang masih berstatus 'dipinjam'
        $activePeminjamen = Peminjaman::where('user_id', $user->id)
            ->where('status', 'dipinjam')
            ->with(['details.tool', 'detailsPertama.tool', 'detailsTambahan.tool', 'perpanjangans'])
            ->latest()
            ->get();

        $targetPeminjaman = null;
        if ($request->filled('peminjaman_id')) {
            $checkTarget = Peminjaman::where('user_id', $user->id)
                ->where('id', (int) $request->peminjaman_id)
                ->first();

            if ($checkTarget && $checkTarget->status === 'dipinjam') {
                $targetPeminjaman = $checkTarget;
            }
        }

        return view('user.peminjaman.create', compact('tools', 'user', 'activePeminjamen', 'targetPeminjaman'));
    }

    /**
     * Memproses penyimpanan data peminjaman (Peminjaman Baru atau Tambahan Peminjaman yang Butuh Konfirmasi Admin)
     */
    public function store(Request $request)
    {
        $isTambahan = $request->filled('peminjaman_id');
        $targetPeminjaman = null;

        if ($isTambahan) {
            $targetPeminjaman = Peminjaman::where('id', $request->peminjaman_id)
                ->where('user_id', Auth::id())
                ->first();

            if (! $targetPeminjaman) {
                return redirect()->back()->withInput()->with('error', 'Transaksi peminjaman aktif tidak ditemukan atau bukan milik Anda.');
            }

            if ($targetPeminjaman->status !== 'dipinjam') {
                return redirect()->route('user.pinjam.show', $targetPeminjaman->id)
                    ->with('error', 'Alat tambahan hanya dapat diajukan pada peminjaman yang berstatus sedang aktif dipinjam.');
            }

            if ($targetPeminjaman->hasPendingPerpanjangan()) {
                return redirect()->route('user.pinjam.show', $targetPeminjaman->id)
                    ->with('error', 'Transaksi ini sudah memiliki pengajuan perpanjangan / tambahan alat yang sedang menunggu konfirmasi Administrator.');
            }

            // Validasi untuk tambahan peminjaman
            $request->validate([
                'peminjaman_id' => 'required|exists:peminjamen,id',
                'tool_id' => 'required|array',
                'qty' => 'required|array',
                'catatan_tambahan' => 'nullable|string|max:500',
                'catatan' => 'nullable|string|max:500',
                'tanggal_kembali' => 'nullable|date|after_or_equal:'.$targetPeminjaman->tanggal_pinjam->format('Y-m-d'),
            ], [
                'tool_id.required' => 'Pilih minimal satu alat ukur tambahan untuk diajukan.',
                'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam awal.',
            ]);
        } else {
            // Validasi input peminjaman baru
            $request->validate([
                'department' => 'required|string|max:255',
                'pic' => 'required|string|max:255',
                'tanggal_pinjam' => 'required|date',
                'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
                'catatan_pertama' => 'nullable|string|max:500',
                'catatan' => 'nullable|string|max:500',
                'tool_id' => 'required|array',
                'qty' => 'required|array',
            ], [
                'department.required' => 'Nama departemen wajib diisi.',
                'pic.required' => 'Nama PIC wajib diisi.',
                'tanggal_pinjam.required' => 'Tanggal pinjam wajib ditentukan.',
                'tanggal_kembali.required' => 'Tanggal kembali wajib ditentukan.',
                'tanggal_kembali.after_or_equal' => 'Tanggal kembali tidak boleh sebelum tanggal pinjam.',
                'tool_id.required' => 'Pilih minimal satu alat ukur untuk dipinjam.',
            ]);
        }

        // 2. Saring hanya alat yang kuantitasnya > 0
        $selectedItems = [];
        foreach ($request->tool_id as $index => $toolId) {
            $qty = isset($request->qty[$index]) ? (int) $request->qty[$index] : 0;
            if ($qty > 0 && ! empty($toolId)) {
                if (isset($selectedItems[$toolId])) {
                    $selectedItems[$toolId] += $qty;
                } else {
                    $selectedItems[$toolId] = $qty;
                }
            }
        }

        if (empty($selectedItems)) {
            return redirect()->back()->withInput()->with('error', 'Silakan tentukan kuantitas minimal 1 untuk alat ukur yang ingin dipinjam.');
        }

        // 3. Cek ketersediaan stok
        foreach ($selectedItems as $toolId => $qtyRequested) {
            $tool = Tool::find($toolId);
            if (! $tool) {
                return redirect()->back()->withInput()->with('error', 'Alat ukur yang dipilih tidak valid.');
            }

            if ($tool->available_stock <= 0) {
                return redirect()->back()->withInput()->with('error', "Maaf, stok untuk alat '{$tool->nama_alat}' saat ini sedang habis (0 unit) dan tidak dapat dipinjam.");
            }

            if ($tool->available_stock < $qtyRequested) {
                return redirect()->back()->withInput()->with('error', "Stok untuk '{$tool->nama_alat}' tidak mencukupi! Tersedia: {$tool->available_stock} unit, diminta: {$qtyRequested} unit.");
            }
        }

        // 4. Jalankan Database Transaction
        DB::beginTransaction();
        try {
            if ($isTambahan && $targetPeminjaman) {
                // Penambahan alat pada peminjaman aktif: WAJIB KONFIRMASI ADMIN TERLEBIH DAHULU
                $peminjaman = $targetPeminjaman;
                $hasNewDate = $request->filled('tanggal_kembali') && $request->tanggal_kembali !== $peminjaman->tanggal_kembali->format('Y-m-d');
                $catatanTambahan = $request->catatan_tambahan ?? $request->catatan ?? 'Penambahan alat ukur pada peminjaman aktif';

                $jenis = $hasNewDate ? 'perpanjangan_dan_tambah_alat' : 'tambah_alat';

                $serializedTools = [];
                foreach ($selectedItems as $toolId => $qty) {
                    $tool = Tool::find($toolId);
                    $serializedTools[] = [
                        'tool_id' => $tool->id,
                        'nama_alat' => $tool->nama_alat,
                        'kode_alat' => $tool->kode_alat,
                        'qty' => $qty,
                        'catatan' => $catatanTambahan,
                        'available_stock' => $tool->available_stock,
                    ];
                }

                $perpanjangan = Perpanjangan::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jenis' => $jenis,
                    'tanggal_kembali_lama' => $peminjaman->tanggal_kembali,
                    'tanggal_kembali_baru' => $hasNewDate ? $request->tanggal_kembali : $peminjaman->tanggal_kembali,
                    'alasan' => $catatanTambahan,
                    'status' => 'menunggu',
                    'alat_tambahan' => $serializedTools,
                ]);

                foreach ($serializedTools as $st) {
                    PerpanjanganDetail::create([
                        'perpanjangan_id' => $perpanjangan->id,
                        'tool_id' => $st['tool_id'],
                        'qty' => $st['qty'],
                        'catatan' => $st['catatan'],
                    ]);
                }

                DB::commit();

                return redirect()->route('user.pinjam.show', $peminjaman->id)
                    ->with('success', 'Pengajuan tambahan alat untuk Transaksi #'.str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT).' berhasil diajukan! Menunggu konfirmasi dan persetujuan dari Administrator.');

            } else {
                // Simpan data peminjaman baru (Tahap 1)
                $peminjaman = Peminjaman::create([
                    'user_id' => Auth::id(),
                    'department' => $request->department,
                    'pic' => $request->pic,
                    'tanggal_pinjam' => $request->tanggal_pinjam,
                    'tanggal_kembali' => $request->tanggal_kembali,
                    'status' => 'dipinjam',
                ]);

                $catatanPertama = $request->catatan_pertama ?? $request->catatan;

                foreach ($selectedItems as $toolId => $qty) {
                    DetailPeminjaman::create([
                        'peminjaman_id' => $peminjaman->id,
                        'tool_id' => $toolId,
                        'qty' => $qty,
                        'is_tambahan' => false,
                        'tahap' => 1,
                        'catatan' => $catatanPertama,
                        'kondisi_detail' => 'baik',
                    ]);

                    $tool = Tool::lockForUpdate()->find($toolId);
                    $tool->available_stock -= $qty;
                    $tool->save();
                }

                DB::commit();

                return redirect()->route('user.pinjam.show', $peminjaman->id)
                    ->with('success', 'Peminjaman alat ukur berhasil diajukan! Catatan nomor transaksi: #'.str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT));
            }

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses peminjaman: '.$e->getMessage());
        }
    }

    /**
     * Menampilkan detail tanda terima / bukti peminjaman
     */
    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'details.tool',
            'detailsPertama.tool',
            'detailsTambahan.tool',
            'pengembalian.teknisi',
            'pengembalian.adminKonfirmasi',
            'perpanjangans.admin',
            'perpanjangans.details.tool',
        ])->findOrFail($id);

        if (Auth::user()->role === 'user' && $peminjaman->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat peminjaman ini.');
        }

        $availableTools = Tool::orderByRaw("CASE WHEN available_stock > 0 THEN 0 ELSE 1 END")->orderBy('nama_alat')->get();

        return view('user.peminjaman.show', compact('peminjaman', 'availableTools'));
    }

    /**
     * User mengajukan pengembalian alat ukur
     */
    public function returnTool($id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dalam status dipinjam.');
        }

        $peminjaman->status = 'menunggu_cek_teknisi';
        $peminjaman->save();

        return redirect()->back()->with('success', 'Pengajuan pengembalian berhasil dikirim! Alat ukur akan segera diperiksa kondisinya oleh Teknisi.');
    }

    /**
     * User (peminjam) mengonfirmasi laporan kerusakan dari teknisi
     */
    public function konfirmasiKerusakanUser(Request $request, $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('pengembalian')
            ->firstOrFail();

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return redirect()->back()->with('error', 'Transaksi peminjaman ini tidak sedang menunggu konfirmasi.');
        }

        $validated = $request->validate([
            'catatan_user' => 'required|string|max:1000',
        ], [
            'catatan_user.required' => 'Harap berikan tanggapan atau konfirmasi Anda terkait kerusakan alat.',
        ]);

        $pengembalian = $peminjaman->pengembalian;
        if ($pengembalian) {
            $pengembalian->update([
                'konfirmasi_user' => true,
                'konfirmasi_user_at' => now(),
                'catatan_user' => $validated['catatan_user'],
            ]);
        }

        return redirect()->back()->with('success', 'Konfirmasi dan tanggapan Anda berhasil dikirim ke Admin dan Teknisi.');
    }

    /**
     * User mengajukan perpanjangan waktu dan/atau penambahan alat ukur ke Administrator
     */
    public function ajukanPerpanjangan(Request $request, int $id)
    {
        $peminjaman = Peminjaman::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['perpanjangans'])
            ->firstOrFail();

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Pengajuan hanya dapat dilakukan untuk transaksi yang sedang aktif dipinjam.');
        }

        if ($peminjaman->hasPendingPerpanjangan()) {
            return redirect()->back()->with('error', 'Transaksi ini sudah memiliki pengajuan perpanjangan/tambahan alat yang sedang menunggu konfirmasi Admin.');
        }

        $hasNewDate = $request->filled('tanggal_kembali_baru') && $request->tanggal_kembali_baru !== $peminjaman->tanggal_kembali->format('Y-m-d');
        $hasTools = $request->has('tool_id') && is_array($request->tool_id);

        // Kumpulkan alat tambahan yang dipilih
        $selectedItems = [];
        if ($hasTools) {
            foreach ($request->tool_id as $index => $toolId) {
                $qty = isset($request->qty[$index]) ? (int) $request->qty[$index] : 0;
                if ($qty > 0 && ! empty($toolId)) {
                    if (isset($selectedItems[$toolId])) {
                        $selectedItems[$toolId] += $qty;
                    } else {
                        $selectedItems[$toolId] = $qty;
                    }
                }
            }
        }

        if (! $hasNewDate && empty($selectedItems)) {
            return redirect()->back()->withInput()->with('error', 'Tentukan tanggal perpanjangan baru atau pilih minimal satu alat ukur tambahan yang ingin dipinjam.');
        }

        $rules = [
            'alasan' => 'required|string|min:5|max:1000',
        ];

        if ($hasNewDate) {
            $rules['tanggal_kembali_baru'] = 'required|date|after:'.$peminjaman->tanggal_kembali->format('Y-m-d');
        }

        $request->validate($rules, [
            'alasan.required' => 'Harap berikan catatan/alasan kenapa Anda memerlukan perpanjangan atau penambahan alat.',
            'alasan.min' => 'Catatan/alasan pengajuan minimal 5 karakter.',
            'alasan.max' => 'Catatan/alasan pengajuan maksimal 1000 karakter.',
            'tanggal_kembali_baru.after' => 'Tanggal pengembalian baru harus lebih lama dari batas tanggal kembali saat ini ('.$peminjaman->tanggal_kembali->translatedFormat('d M Y').').',
        ]);

        // Cek stok ketersediaan untuk setiap alat yang dipilih
        foreach ($selectedItems as $toolId => $qtyRequested) {
            $tool = Tool::find($toolId);
            if (! $tool) {
                return redirect()->back()->withInput()->with('error', 'Alat ukur yang dipilih tidak valid.');
            }
            if ($tool->available_stock <= 0) {
                return redirect()->back()->withInput()->with('error', "Maaf, stok untuk alat tambahan '{$tool->nama_alat}' saat ini sedang habis (0 unit) dan tidak dapat diajukan.");
            }
            if ($tool->available_stock < $qtyRequested) {
                return redirect()->back()->withInput()->with('error', "Stok untuk {$tool->nama_alat} tidak mencukupi! Tersedia: {$tool->available_stock}, diminta: {$qtyRequested}.");
            }
        }

        // Tentukan jenis pengajuan
        $jenis = 'perpanjangan';
        if ($hasNewDate && ! empty($selectedItems)) {
            $jenis = 'perpanjangan_dan_tambah_alat';
        } elseif (! empty($selectedItems)) {
            $jenis = 'tambah_alat';
        }

        DB::beginTransaction();
        try {
            $serializedTools = [];
            foreach ($selectedItems as $toolId => $qty) {
                $tool = Tool::find($toolId);
                $serializedTools[] = [
                    'tool_id' => $tool->id,
                    'nama_alat' => $tool->nama_alat,
                    'kode_alat' => $tool->kode_alat,
                    'qty' => $qty,
                    'catatan' => $request->catatan_tambahan ?? $request->alasan,
                    'available_stock' => $tool->available_stock,
                ];
            }

            $perpanjangan = Perpanjangan::create([
                'peminjaman_id' => $peminjaman->id,
                'jenis' => $jenis,
                'tanggal_kembali_lama' => $peminjaman->tanggal_kembali,
                'tanggal_kembali_baru' => $hasNewDate ? $request->tanggal_kembali_baru : $peminjaman->tanggal_kembali,
                'alasan' => $request->alasan,
                'status' => 'menunggu',
                'alat_tambahan' => $serializedTools,
            ]);

            foreach ($serializedTools as $st) {
                PerpanjanganDetail::create([
                    'perpanjangan_id' => $perpanjangan->id,
                    'tool_id' => $st['tool_id'],
                    'qty' => $st['qty'],
                    'catatan' => $st['catatan'],
                ]);
            }

            DB::commit();

            $jenisLabel = $jenis === 'perpanjangan_dan_tambah_alat' ? 'perpanjangan waktu & tambahan alat' : ($jenis === 'tambah_alat' ? 'tambahan alat' : 'perpanjangan peminjaman');

            return redirect()->back()->with('success', "Pengajuan {$jenisLabel} berhasil dikirim! Menunggu konfirmasi dan persetujuan dari Administrator.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem saat memproses pengajuan: '.$e->getMessage());
        }
    }
}
