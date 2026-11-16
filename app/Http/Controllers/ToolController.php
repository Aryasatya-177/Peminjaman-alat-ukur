<?php

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ToolController extends Controller
{
    /**
     * Tampilkan daftar alat ukur presisi dan ketersediaan stok
     */
    public function index(Request $request): View
    {
        $query = Tool::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('kode_alat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'available') {
                $query->where('available_stock', '>', 0);
            } elseif ($request->status === 'empty') {
                $query->where('available_stock', '<=', 0);
            }
        }

        $tools = $query->orderBy('nama_alat')->paginate(10)->withQueryString();

        $stats = [
            'total_jenis' => Tool::count(),
            'total_unit' => Tool::sum('total_stock'),
            'tersedia' => Tool::sum('available_stock'),
            'dipinjam' => Tool::sum('total_stock') - Tool::sum('available_stock'),
        ];

        return view('admin.tools.index', compact('tools', 'stats'));
    }

    /**
     * Tampilkan formulir penambahan alat ukur baru
     */
    public function create(): View
    {
        return view('admin.tools.create');
    }

    /**
     * Simpan data alat ukur baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kode_alat' => 'required|string|max:50|unique:tools,kode_alat',
            'total_stock' => 'required|integer|min:1',
        ], [
            'nama_alat.required' => 'Nama alat ukur presisi wajib diisi.',
            'kode_alat.required' => 'Kode alat / ID registrasi wajib diisi.',
            'kode_alat.unique' => 'Kode alat sudah digunakan untuk alat lain.',
            'total_stock.required' => 'Total stok wajib diisi minimal 1 unit.',
            'total_stock.min' => 'Total stok minimal 1 unit.',
        ]);

        Tool::create([
            'nama_alat' => $validated['nama_alat'],
            'kode_alat' => strtoupper(trim($validated['kode_alat'])),
            'total_stock' => (int) $validated['total_stock'],
            'available_stock' => (int) $validated['total_stock'],
        ]);

        return redirect()->route('admin.tools.index')
            ->with('success', 'Alat ukur presisi baru berhasil ditambahkan ke inventaris.');
    }

    /**
     * Tampilkan formulir edit alat ukur
     */
    public function edit(int $id): View
    {
        $tool = Tool::findOrFail($id);

        return view('admin.tools.edit', compact('tool'));
    }

    /**
     * Perbarui data alat ukur
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $tool = Tool::findOrFail($id);

        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kode_alat' => 'required|string|max:50|unique:tools,kode_alat,'.$tool->id,
            'total_stock' => 'required|integer|min:1',
            'available_stock' => 'required|integer|min:0|lte:total_stock',
        ], [
            'nama_alat.required' => 'Nama alat ukur wajib diisi.',
            'kode_alat.required' => 'Kode alat wajib diisi.',
            'kode_alat.unique' => 'Kode alat sudah dipakai oleh alat lain.',
            'total_stock.min' => 'Total stok minimal 1 unit.',
            'available_stock.lte' => 'Stok tersedia tidak boleh melebihi total stok.',
        ]);

        $tool->update([
            'nama_alat' => $validated['nama_alat'],
            'kode_alat' => strtoupper(trim($validated['kode_alat'])),
            'total_stock' => (int) $validated['total_stock'],
            'available_stock' => (int) $validated['available_stock'],
        ]);

        return redirect()->route('admin.tools.index')
            ->with('success', 'Data alat ukur presisi berhasil diperbarui.');
    }

    /**
     * Hapus alat ukur dari sistem (jika belum pernah dipinjam aktif)
     */
    public function destroy(int $id): RedirectResponse
    {
        $tool = Tool::findOrFail($id);

        $hasActiveLoans = DetailPeminjaman::where('tool_id', $tool->id)
            ->whereHas('peminjaman', function ($q) {
                $q->whereIn('status', ['dipinjam', 'menunggu_cek_teknisi']);
            })->exists();

        if ($hasActiveLoans) {
            return redirect()->route('admin.tools.index')
                ->with('error', 'Alat tidak dapat dihapus karena saat ini sedang aktif dalam transaksi peminjaman!');
        }

        $tool->delete();

        return redirect()->route('admin.tools.index')
            ->with('success', 'Alat ukur berhasil dihapus dari sistem inventaris.');
    }
}
