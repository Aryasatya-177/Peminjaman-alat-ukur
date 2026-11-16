<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalians';

    protected $fillable = [
        'peminjaman_id',
        'teknisi_id',
        'tanggal_dikembalikan',
        'kondisi_alat',
        'catatan_teknisi',
        'jumlah_baik',
        'jumlah_rusak',
        'tanda_kerusakan_berat',
        'bisa_direpair',
        'status_repair',
        'catatan_perbaikan',
        'konfirmasi_admin',
        'konfirmasi_admin_at',
        'konfirmasi_admin_by',
        'catatan_admin',
        'tindakan_admin',
        'konfirmasi_user',
        'konfirmasi_user_at',
        'catatan_user',
    ];

    protected $casts = [
        'tanggal_dikembalikan' => 'date',
        'tanda_kerusakan_berat' => 'boolean',
        'bisa_direpair' => 'boolean',
        'konfirmasi_admin' => 'boolean',
        'konfirmasi_admin_at' => 'datetime',
        'konfirmasi_user' => 'boolean',
        'konfirmasi_user_at' => 'datetime',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function adminKonfirmasi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'konfirmasi_admin_by');
    }
}
