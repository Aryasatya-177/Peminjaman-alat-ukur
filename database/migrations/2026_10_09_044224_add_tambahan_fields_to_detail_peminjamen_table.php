<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_peminjamen', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_peminjamen', 'is_tambahan')) {
                $table->boolean('is_tambahan')->default(false)->after('kondisi_detail')
                    ->comment('FALSE: Peminjaman Pertama/Utama, TRUE: Tambahan Peminjaman');
            }
            if (! Schema::hasColumn('detail_peminjamen', 'tahap')) {
                $table->unsignedTinyInteger('tahap')->default(1)->after('is_tambahan')
                    ->comment('1: Peminjaman Pertama, 2+: Tahap Tambahan');
            }
            if (! Schema::hasColumn('detail_peminjamen', 'catatan')) {
                $table->string('catatan', 500)->nullable()->after('tahap')
                    ->comment('Catatan keperluan peminjaman tambahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_peminjamen', function (Blueprint $table) {
            $table->dropColumn(['is_tambahan', 'tahap', 'catatan']);
        });
    }
};
