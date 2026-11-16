<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjamen';

    protected $fillable = [
        'user_id',
        'department',
        'pic',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id');
    }

    public function detailsPertama(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id')->where('is_tambahan', false);
    }

    public function detailsTambahan(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'peminjaman_id')->where('is_tambahan', true);
    }

    public function hasTambahan(): bool
    {
        return $this->details->where('is_tambahan', true)->isNotEmpty();
    }

    public function totalQtyPertama(): int
    {
        return (int) $this->details->where('is_tambahan', false)->sum('qty');
    }

    public function totalQtyTambahan(): int
    {
        return (int) $this->details->where('is_tambahan', true)->sum('qty');
    }

    public function totalQtyAll(): int
    {
        return (int) $this->details->sum('qty');
    }

    /**
     * Memeriksa apakah peminjaman masih dalam rentang waktu yang sah untuk menambah alat ukur.
     * Syarat: status harus 'dipinjam' dan hari ini belum melewati batas tanggal kembali (<= tanggal_kembali).
     */
    public function isMasihDalamWaktuPinjam(): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        return now()->startOfDay()->lte($this->tanggal_kembali->endOfDay());
    }

    /**
     * Memeriksa apakah peminjaman telah terlambat melewati batas tanggal kembali.
     */
    public function isTerlambat(): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        return now()->startOfDay()->gt($this->tanggal_kembali->endOfDay());
    }

    /**
     * Memeriksa apakah hari ini adalah tanggal pengembalian alat (jatuh tempo hari ini).
     */
    public function isJatuhTempoHariIni(): bool
    {
        if ($this->status !== 'dipinjam') {
            return false;
        }

        return now()->startOfDay()->equalTo($this->tanggal_kembali->startOfDay());
    }

    /**
     * Memeriksa apakah peminjaman memerlukan pengembalian segera (jatuh tempo hari ini atau terlambat).
     */
    public function isPerluDikembalikanSegera(): bool
    {
        return $this->isJatuhTempoHariIni() || $this->isTerlambat();
    }

    /**
     * Menghitung sisa hari peminjaman (positif jika masih ada waktu, 0 jika hari ini, negatif jika terlambat).
     */
    public function sisaHariPinjam(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->tanggal_kembali->startOfDay(), false);
    }

    /**
     * Mengambil keterangan/catatan dari peminjaman pertama/utama.
     */
    public function getCatatanPertama(): ?string
    {
        return $this->detailsPertama->firstWhere('catatan', '!==', null)?->catatan;
    }

    /**
     * Mengambil seluruh catatan keperluan dari peminjaman tambahan (unik).
     */
    public function getCatatanTambahan(): array
    {
        return $this->detailsTambahan
            ->pluck('catatan')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function pengembalian(): HasOne
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }

    public function perpanjangans(): HasMany
    {
        return $this->hasMany(Perpanjangan::class, 'peminjaman_id')->latest();
    }

    public function latestPerpanjangan(): HasOne
    {
        return $this->hasOne(Perpanjangan::class, 'peminjaman_id')->latestOfMany();
    }

    public function pendingPerpanjangan(): HasOne
    {
        return $this->hasOne(Perpanjangan::class, 'peminjaman_id')->where('status', 'menunggu');
    }

    public function hasPendingPerpanjangan(): bool
    {
        if ($this->relationLoaded('perpanjangans')) {
            return $this->perpanjangans->where('status', 'menunggu')->isNotEmpty();
        }

        return $this->perpanjangans()->where('status', 'menunggu')->exists();
    }

    public function bisaDiajukanPerpanjangan(): bool
    {
        return $this->status === 'dipinjam' && ! $this->hasPendingPerpanjangan();
    }

    public function isSedangDipinjam(): bool
    {
        return $this->status === 'dipinjam';
    }

    public function isMenungguCekTeknisi(): bool
    {
        return $this->status === 'menunggu_cek_teknisi';
    }

    public function isDalamPerbaikan(): bool
    {
        return $this->status === 'dalam_perbaikan';
    }

    public function isMenungguKonfirmasi(): bool
    {
        return $this->status === 'menunggu_konfirmasi';
    }

    public function isRusakDikonfirmasi(): bool
    {
        return $this->status === 'rusak_dikonfirmasi';
    }

    public function isSelesai(): bool
    {
        return $this->status === 'selesai';
    }

    public function isRusak(): bool
    {
        return in_array($this->status, ['rusak', 'menunggu_konfirmasi', 'rusak_dikonfirmasi'], true);
    }

    public function hasKerusakanBerat(): bool
    {
        return (bool) ($this->pengembalian?->tanda_kerusakan_berat ?? false);
    }

    public function needsAdminConfirmation(): bool
    {
        return $this->status === 'menunggu_konfirmasi' && ! ($this->pengembalian?->konfirmasi_admin ?? false);
    }

    public function needsUserConfirmation(): bool
    {
        return $this->status === 'menunggu_konfirmasi' && ! ($this->pengembalian?->konfirmasi_user ?? false);
    }
}
