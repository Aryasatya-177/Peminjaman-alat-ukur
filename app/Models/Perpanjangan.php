<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perpanjangan extends Model
{
    use HasFactory;

    protected $table = 'perpanjangans';

    protected $fillable = [
        'peminjaman_id',
        'jenis',
        'tanggal_kembali_lama',
        'tanggal_kembali_baru',
        'alasan',
        'status',
        'catatan_admin',
        'admin_id',
        'confirmed_at',
        'alat_tambahan',
    ];

    protected $casts = [
        'tanggal_kembali_lama' => 'date',
        'tanggal_kembali_baru' => 'date',
        'confirmed_at' => 'datetime',
        'alat_tambahan' => 'array',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PerpanjanganDetail::class, 'perpanjangan_id');
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    /**
     * Memeriksa apakah pengajuan ini memuat perpanjangan batas tanggal pengembalian.
     */
    public function isPerpanjanganTanggal(): bool
    {
        if (! $this->tanggal_kembali_baru || ! $this->tanggal_kembali_lama) {
            return false;
        }

        return $this->tanggal_kembali_baru->gt($this->tanggal_kembali_lama);
    }

    /**
     * Memeriksa apakah pengajuan ini memuat permohonan alat ukur tambahan.
     */
    public function hasAlatTambahan(): bool
    {
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            return true;
        }

        if (! empty($this->alat_tambahan) && is_array($this->alat_tambahan) && count($this->alat_tambahan) > 0) {
            return true;
        }

        return false;
    }

    public function isPerpanjanganOnly(): bool
    {
        return $this->isPerpanjanganTanggal() && ! $this->hasAlatTambahan();
    }

    public function isTambahAlatOnly(): bool
    {
        return $this->hasAlatTambahan() && ! $this->isPerpanjanganTanggal();
    }

    public function isPerpanjanganDanTambahAlat(): bool
    {
        return $this->isPerpanjanganTanggal() && $this->hasAlatTambahan();
    }

    /**
     * Mendapatkan label jenis pengajuan yang mudah dipahami.
     */
    public function getLabelJenisAttribute(): string
    {
        if ($this->isPerpanjanganDanTambahAlat()) {
            return 'Perpanjangan & Tambah Alat';
        }
        if ($this->isTambahAlatOnly()) {
            return 'Tambah Alat Ukur';
        }

        return 'Perpanjangan Waktu';
    }

    /**
     * Mengambil daftar alat tambahan (baik dari relasi details maupun snapshot JSON).
     *
     * @return array<int, array{tool_id: int, nama_alat: string, kode_alat: string, qty: int, catatan: ?string, available_stock: int}>
     */
    public function getDaftarAlatTambahan(): array
    {
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            return $this->details->map(function ($d) {
                return [
                    'tool_id' => $d->tool_id,
                    'nama_alat' => $d->tool->nama_alat ?? 'Alat Ukur',
                    'kode_alat' => $d->tool->kode_alat ?? '-',
                    'qty' => (int) $d->qty,
                    'catatan' => $d->catatan,
                    'available_stock' => $d->tool->available_stock ?? 0,
                ];
            })->all();
        }

        if (! empty($this->alat_tambahan) && is_array($this->alat_tambahan)) {
            return $this->alat_tambahan;
        }

        return [];
    }

    /**
     * Menghitung total unit fisik alat ukur tambahan yang diajukan.
     */
    public function totalQtyAlatTambahan(): int
    {
        $items = $this->getDaftarAlatTambahan();

        return (int) array_sum(array_column($items, 'qty'));
    }

    /**
     * Menghitung berapa hari durasi tambahan perpanjangan yang diajukan.
     */
    public function tambahanHari(): int
    {
        if (! $this->tanggal_kembali_baru || ! $this->tanggal_kembali_lama) {
            return 0;
        }

        return max(0, (int) $this->tanggal_kembali_lama->startOfDay()->diffInDays($this->tanggal_kembali_baru->startOfDay(), false));
    }
}
