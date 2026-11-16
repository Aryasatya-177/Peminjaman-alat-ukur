<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan dashboard peminjaman user
     */
    public function dashboard()
    {
        $user = Auth::user();

        $peminjaman = Peminjaman::where('user_id', $user->id)
            ->with(['details.tool', 'detailsPertama.tool', 'detailsTambahan.tool', 'pengembalian.teknisi', 'perpanjangans.admin'])
            ->latest()
            ->get();

        $availableTools = Tool::orderByRaw("CASE WHEN available_stock > 0 THEN 0 ELSE 1 END")
            ->orderBy('nama_alat')
            ->get();

        $stats = [
            'total' => $peminjaman->count(),
            'dipinjam' => $peminjaman->where('status', 'dipinjam')->count(),
            'menunggu_cek' => $peminjaman->where('status', 'menunggu_cek_teknisi')->count(),
            'dalam_perbaikan' => $peminjaman->where('status', 'dalam_perbaikan')->count(),
            'menunggu_konfirmasi' => $peminjaman->where('status', 'menunggu_konfirmasi')->count(),
            'selesai' => $peminjaman->where('status', 'selesai')->count(),
            'rusak' => $peminjaman->whereIn('status', ['rusak', 'menunggu_konfirmasi', 'rusak_dikonfirmasi'])->count(),
        ];

        // Daftar peminjaman yang membutuhkan tindakan konfirmasi dari user ini
        $needUserConfirmations = $peminjaman->filter(function ($item) {
            return $item->status === 'menunggu_konfirmasi' && ! ($item->pengembalian?->konfirmasi_user ?? false);
        });

        // Pengingat: Peminjaman jatuh tempo hari ini & peminjaman yang sudah terlambat
        $peminjamanHariIni = $peminjaman->filter(fn ($item) => $item->isJatuhTempoHariIni());
        $peminjamanTerlambat = $peminjaman->filter(fn ($item) => $item->isTerlambat());

        return view('user.dashboard', compact('peminjaman', 'stats', 'needUserConfirmations', 'availableTools', 'peminjamanHariIni', 'peminjamanTerlambat'));
    }

    /**
     * Tampilkan profil user
     */
    public function profile()
    {
        $user = Auth::user();

        return view('user.profile', compact('user'));
    }

    /**
     * Perbarui data profil user (Departemen, PIC, Nama, Password)
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'pic' => 'required|string|max:255',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        if ($request->filled('current_password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->department = $request->department;
        $user->pic = $request->pic;
        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profil dan data PIC berhasil diperbarui.');
    }
}
