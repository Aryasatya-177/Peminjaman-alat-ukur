<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeknisiController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Halaman utama: redirect dinamis sesuai role status login
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'teknisi') {
            return redirect()->route('teknisi.dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    return redirect()->route('login');
});

// Autentikasi (Tamu / Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout & Profil & Rincian Bukti Peminjaman (Semua User Terotentikasi)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    // Rincian / Tanda Terima Peminjaman (Dapat diakses oleh User pemohon, Teknisi, dan Admin)
    Route::get('/user/pinjam/{id}', [PeminjamanController::class, 'show'])->whereNumber('id')->name('user.pinjam.show');
    Route::get('/pinjam/{id}', [PeminjamanController::class, 'show'])->whereNumber('id');
});

// ==========================================
// AREA HAK AKSES: USER
// ==========================================
Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/pinjam/create', [PeminjamanController::class, 'create'])->name('user.pinjam.create');
    Route::post('/pinjam', [PeminjamanController::class, 'store'])->name('user.pinjam.store');
    Route::post('/kembalikan/{id}', [PeminjamanController::class, 'returnTool'])->whereNumber('id')->name('user.return');
    Route::post('/pinjam/{id}/konfirmasi-kerusakan', [PeminjamanController::class, 'konfirmasiKerusakanUser'])->whereNumber('id')->name('user.pinjam.konfirmasi');
    Route::post('/pinjam/{id}/perpanjang', [PeminjamanController::class, 'ajukanPerpanjangan'])->whereNumber('id')->name('user.pinjam.perpanjang');
});

// ==========================================
// AREA HAK AKSES: TEKNISI
// ==========================================
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->group(function () {
    Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('teknisi.dashboard');
    Route::get('/antrian', [TeknisiController::class, 'antrian'])->name('teknisi.antrian');
    Route::get('/cek/{id}', [TeknisiController::class, 'checkForm'])->whereNumber('id')->name('teknisi.cek');
    Route::post('/cek/{id}', [TeknisiController::class, 'storeCheck'])->whereNumber('id')->name('teknisi.store');
    Route::post('/repair/{id}/selesai', [TeknisiController::class, 'selesaikanRepair'])->whereNumber('id')->name('teknisi.repair.selesai');
    Route::get('/history', [TeknisiController::class, 'history'])->name('teknisi.history');
});

// ==========================================
// AREA HAK AKSES: ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Manajemen Stok & Alat Ukur
    Route::get('/tools', [ToolController::class, 'index'])->name('admin.tools.index');
    Route::get('/tools/create', [ToolController::class, 'create'])->name('admin.tools.create');
    Route::post('/tools', [ToolController::class, 'store'])->name('admin.tools.store');
    Route::get('/tools/{id}/edit', [ToolController::class, 'edit'])->whereNumber('id')->name('admin.tools.edit');
    Route::put('/tools/{id}', [ToolController::class, 'update'])->whereNumber('id')->name('admin.tools.update');
    Route::delete('/tools/{id}', [ToolController::class, 'destroy'])->whereNumber('id')->name('admin.tools.destroy');

    // Monitoring Transaksi Peminjaman & Konfirmasi
    Route::get('/peminjaman', [AdminController::class, 'peminjaman'])->name('admin.peminjaman.index');
    Route::get('/peminjaman/{id}', [AdminController::class, 'showPeminjaman'])->whereNumber('id')->name('admin.peminjaman.show');
    Route::post('/peminjaman/{id}/konfirmasi', [AdminController::class, 'konfirmasiKerusakan'])->whereNumber('id')->name('admin.peminjaman.konfirmasi');

    // Konfirmasi Perpanjangan Peminjaman Alat
    Route::post('/perpanjangan/{id}/setujui', [AdminController::class, 'setujuiPerpanjangan'])->whereNumber('id')->name('admin.perpanjangan.setujui');
    Route::post('/perpanjangan/{id}/tolak', [AdminController::class, 'tolakPerpanjangan'])->whereNumber('id')->name('admin.perpanjangan.tolak');

    // Manajemen Pengguna & Hak Akses
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::put('/users/{id}/role', [AdminController::class, 'updateUserRole'])->whereNumber('id')->name('admin.users.updateRole');

    // Pengelolaan Laporan Bulanan (Preview, Word, PDF)
    Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('admin.reports.pdf');
    Route::get('/reports/word', [ReportController::class, 'exportWord'])->name('admin.reports.word');
});
