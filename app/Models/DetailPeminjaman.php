<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjamen';

    protected $fillable = [
        'peminjaman_id',
        'tool_id',
        'qty',
        'qty_baik',
        'qty_rusak',
        'kondisi_detail',
        'is_tambahan',
        'tahap',
        'catatan',
    ];

    protected $casts = [
        'is_tambahan' => 'boolean',
        'tahap' => 'integer',
    ];

    public function isTambahan(): bool
    {
        return (bool) $this->is_tambahan;
    }

    public function isPertama(): bool
    {
        return ! $this->is_tambahan;
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }
}
