<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Perpanjangan;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard utama administrator
     */
    public function dashboard(): View
    {
        $stats = [
            'total_tools' => Tool::count(),
            'total_stock_units' => Tool::sum('total_stock'),
            'available_units' => Tool::sum('available_stock'),
            'borrowed_units' => Tool::sum('total_stock') - Tool::sum('available_stock'),

            'total_peminjaman' => Peminjaman::count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'menunggu_cek' => Peminjaman::where('status', 'menunggu_cek_teknisi')->count(),
            'dalam_perbaikan' => Peminjaman::where('status', 'dalam_perbaikan')->count(),
            'menunggu_konfirmasi' => Peminjaman::where('status', 'menunggu_konfirmasi')->count(),
            'rusak_dikonfirmasi' => Peminjaman::where('status', 'rusak_dikonfirmasi')->count(),
            'selesai' => Peminjaman::where('status', 'selesai')->count(),
            'rusak' => Peminjaman::whereIn('status', ['rusak', 'menunggu_konfirmasi', 'rusak_dikonfirmasi'])->count(),
            'rusak_berat_count' => Pengembalian::where('tanda_kerusakan_berat', true)->count(),
            'pending_perpanjangan' => Perpanjangan::where('status', 'menunggu')->count(),

            'total_users' => User::count(),
            'total_user_role' => User::where('role', 'user')->count(),
            'total_teknisi_role' => User::where('role', 'teknisi')->count(),
            'total_admin_role' => User::where('role', 'admin')->count(),
        ];

        // Daftar peminjaman rusak yang memerlukan konfirmasi Admin segera
        $pendingConfirmations = Peminjaman::where('status', 'menunggu_konfirmasi')
            ->with(['user', 'details.tool', 'pengembalian.teknisi'])
            ->latest('updated_at')
            ->get();

        // Daftar permohonan perpanjangan peminjaman yang menunggu persetujuan Admin
        $pendingPerpanjangans = Perpanjangan::where('status', 'menunggu')
            ->with(['peminjaman.user', 'peminjaman.details.tool', 'peminjaman.detailsPertama.tool', 'peminjaman.detailsTambahan.tool', 'details.tool'])
            ->latest()
            ->get();

        // Statistik peminjaman berdasarkan departemen
        $departmentStats = Peminjaman::select('department', DB::raw('count(*) as total'))
            ->groupBy('department')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Peminjaman terbaru
        $recentLoans = Peminjaman::with(['user', 'details.tool'])
            ->latest()
            ->take(6)
            ->get();

        // Riwayat pengecekan teknisi terbaru
        $recentInspections = Pengembalian::with(['peminjaman.user', 'teknisi'])
            ->latest('tanggal_dikembalikan')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingConfirmations', 'pendingPerpanjangans', 'departmentStats', 'recentLoans', 'recentInspections'));
    }

    /**
     * Monitoring seluruh transaksi peminjaman alat ukur lintas departemen
     */
    public function peminjaman(Request $request): View
    {
        $query = Peminjaman::with(['user', 'details.tool', 'pengembalian.teknisi'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', 'like', "%{$request->department}%");
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('details.tool', function ($tq) use ($search) {
                        $tq->where('nama_alat', 'like', "%{$search}%")
                            ->orWhere('kode_alat', 'like', "%{$search}%");
                    });
            });
        }

        $peminjaman = $query->paginate(15)->withQueryString();

        // Daftar departemen unik untuk filter dropdown
        $departments = Peminjaman::select('department')->distinct()->pluck('department');

        return view('admin.peminjaman.index', compact('peminjaman', 'departments'));
    }

    /**
     * Tampilkan detail tanda terima peminjaman dari sudut pandang Admin
     */
    public function showPeminjaman(int $id): View
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

        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    /**
     * Admin melakukan konfirmasi atas alat rusak yang tidak bisa diperbaiki teknisi
     */
    public function konfirmasiKerusakan(Request $request, int $id): RedirectResponse
    {
        $peminjaman = Peminjaman::with(['details.tool', 'pengembalian'])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Transaksi peminjaman ini tidak sedang menunggu konfirmasi admin.');
        }

        $validated = $request->validate([
            'tindakan_admin' => 'required|in:afkir_aset,bebankan_ganti_rugi,toleransi,lainnya',
            'catatan_admin' => 'required|string|max:1000',
            'potong_total_stock' => 'nullable|boolean',
        ], [
            'tindakan_admin.required' => 'Pilih keputusan administratif terkait alat rusak ini.',
            'catatan_admin.required' => 'Berikan catatan resmi atau arahan keputusan administrator.',
        ]);

        DB::beginTransaction();
        try {
            $pengembalian = $peminjaman->pengembalian;
            $jumlahRusak = $pengembalian ? (int) $pengembalian->jumlah_rusak : $peminjaman->details->sum('qty');

            // Jika diputuskan afkir aset dan opsi potong total_stock dipilih:
            if ($validated['tindakan_admin'] === 'afkir_aset' || $request->boolean('potong_total_stock')) {
                $sisaAfkir = $jumlahRusak;
                foreach ($peminjaman->details as $detail) {
                    $tool = Tool::lockForUpdate()->find($detail->tool_id);
                    if ($tool && $sisaAfkir > 0) {
                        $qtyAfkir = min($detail->qty, $sisaAfkir);
                        $tool->total_stock = max(0, $tool->total_stock - $qtyAfkir);
                        $tool->save();
                        $sisaAfkir -= $qtyAfkir;
                    }
                }
            }

            if ($pengembalian) {
                $pengembalian->update([
                    'konfirmasi_admin' => true,
                    'konfirmasi_admin_at' => now(),
                    'konfirmasi_admin_by' => auth()->id(),
                    'catatan_admin' => $validated['catatan_admin'],
                    'tindakan_admin' => $validated['tindakan_admin'],
                ]);
            }

            // Update status peminjaman menjadi rusak_dikonfirmasi
            $peminjaman->status = 'rusak_dikonfirmasi';
            $peminjaman->save();

            DB::commit();

            return redirect()->route('admin.peminjaman.show', $peminjaman->id)
                ->with('success', 'Konfirmasi keputusan Admin atas alat rusak (' . $jumlahRusak . ' unit) pada transaksi #' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) . ' berhasil diproses.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->with('error', 'Gagal memproses konfirmasi admin: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar seluruh pengguna dan pengaturan hak akses
     */
    public function users(Request $request): View
    {
        $query = User::query()->orderBy('role')->orderBy('name');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('pic', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Perbarui hak akses (role) atau departemen & PIC pengguna
     */
    public function updateUserRole(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|in:user,teknisi,admin',
            'department' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
        ]);

        // Mencegah admin mendemosi akunnya sendiri jika dia satu-satunya admin
        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mengubah hak akses akun Anda sendiri!');
        }

        $user->update([
            'role' => $validated['role'],
            'department' => $validated['department'],
            'pic' => $validated['pic'],
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Hak akses untuk {$user->name} berhasil diubah menjadi: " . strtoupper($validated['role']));
    }

    /**
     * Admin menyetujui pengajuan perpanjangan waktu dan/atau penambahan alat
     */
    public function setujuiPerpanjangan(Request $request, int $id): RedirectResponse
    {
        $perpanjangan = Perpanjangan::with(['peminjaman', 'details.tool'])->where('status', 'menunggu')->findOrFail($id);
        $peminjaman = $perpanjangan->peminjaman;

        if ($peminjaman->status !== 'dipinjam') {
            return back()->with('error', 'Transaksi peminjaman ini tidak dalam status aktif dipinjam.');
        }

        $validated = $request->validate([
            'catatan_admin' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // 1. Jika ada permohonan alat tambahan, verifikasi stok dan buat DetailPeminjaman
            $items = $perpanjangan->getDaftarAlatTambahan();
            if (!empty($items)) {
                foreach ($items as $item) {
                    $tool = Tool::lockForUpdate()->find($item['tool_id']);
                    if (!$tool) {
                        DB::rollBack();

                        return back()->with('error', 'Alat ukur ID ' . $item['tool_id'] . ' tidak ditemukan di sistem.');
                    }
                    if ($tool->available_stock < $item['qty']) {
                        DB::rollBack();

                        return back()->with('error', "Stok untuk {$tool->nama_alat} tidak mencukupi untuk disetujui! Tersedia saat ini: {$tool->available_stock}, diminta: {$item['qty']}.");
                    }
                }

                $currentMaxTahap = (int) $peminjaman->details()->max('tahap');
                $nextTahap = max(2, $currentMaxTahap + 1);

                foreach ($items as $item) {
                    \App\Models\DetailPeminjaman::create([
                        'peminjaman_id' => $peminjaman->id,
                        'tool_id' => $item['tool_id'],
                        'qty' => $item['qty'],
                        'is_tambahan' => true,
                        'tahap' => $nextTahap,
                        'catatan' => $item['catatan'] ?? $perpanjangan->alasan,
                        'kondisi_detail' => 'baik',
                    ]);

                    $tool = Tool::lockForUpdate()->find($item['tool_id']);
                    $tool->available_stock -= $item['qty'];
                    $tool->save();
                }
            }

            // 2. Jika ada perpanjangan tanggal kembali
            if ($perpanjangan->isPerpanjanganTanggal()) {
                $peminjaman->update([
                    'tanggal_kembali' => $perpanjangan->tanggal_kembali_baru,
                ]);
            }

            // 3. Update status perpanjangan
            $perpanjangan->update([
                'status' => 'disetujui',
                'catatan_admin' => $validated['catatan_admin'] ?? null,
                'admin_id' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            DB::commit();

            $msg = 'Pengajuan untuk transaksi #' . str_pad($peminjaman->id, 5, '0', STR_PAD_LEFT) . ' berhasil disetujui!';
            if ($perpanjangan->isPerpanjanganTanggal()) {
                $msg .= ' Batas tanggal kembali diperbarui menjadi ' . $perpanjangan->tanggal_kembali_baru->translatedFormat('d F Y') . '.';
            }
            if (!empty($items)) {
                $msg .= ' Tambahan ' . $perpanjangan->totalQtyAlatTambahan() . ' unit alat ukur resmi digabungkan ke transaksi dan stok inventaris telah disesuaikan.';
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal memproses persetujuan: ' . $e->getMessage());
        }
    }

    /**
     * Admin menolak pengajuan perpanjangan / tambahan alat peminjaman
     */
    public function tolakPerpanjangan(Request $request, int $id): RedirectResponse
    {
        $perpanjangan = Perpanjangan::where('status', 'menunggu')->findOrFail($id);

        $validated = $request->validate([
            'catatan_admin' => 'required|string|min:5|max:1000',
        ], [
            'catatan_admin.required' => 'Harap berikan alasan/catatan kenapa pengajuan perpanjangan / tambahan alat ini ditolak.',
            'catatan_admin.min' => 'Catatan penolakan minimal 5 karakter.',
            'catatan_admin.max' => 'Catatan penolakan maksimal 1000 karakter.',
        ]);

        $perpanjangan->update([
            'status' => 'ditolak',
            'catatan_admin' => $validated['catatan_admin'],
            'admin_id' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan untuk transaksi #' . str_pad($perpanjangan->peminjaman_id, 5, '0', STR_PAD_LEFT) . ' telah ditolak. Catatan evaluasi telah diteruskan ke peminjam.');
    }
}
