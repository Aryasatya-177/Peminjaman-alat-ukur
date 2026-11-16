<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerpanjanganDetail extends Model
{
    use HasFactory;

    protected $table = 'perpanjangan_details';

    protected $fillable = [
        'perpanjangan_id',
        'tool_id',
        'qty',
        'catatan',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function perpanjangan(): BelongsTo
    {
        return $this->belongsTo(Perpanjangan::class, 'perpanjangan_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id');
    }
}

