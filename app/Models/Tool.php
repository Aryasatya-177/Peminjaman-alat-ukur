<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_alat',
        'kode_alat',
        'total_stock',
        'available_stock',
    ];

    public function detailPeminjamen(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class);
    }
}
