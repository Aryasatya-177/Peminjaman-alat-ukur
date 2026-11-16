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
        // 1. Tambah kolom pada tabel perpanjangans
        Schema::table('perpanjangans', function (Blueprint $table) {
            if (! Schema::hasColumn('perpanjangans', 'jenis')) {
                $table->string('jenis', 50)->default('perpanjangan')->after('peminjaman_id')
                    ->comment('Jenis pengajuan: perpanjangan, tambah_alat, perpanjangan_dan_tambah_alat');
            }
            if (! Schema::hasColumn('perpanjangans', 'alat_tambahan')) {
                $table->json('alat_tambahan')->nullable()->after('catatan_admin')
                    ->comment('Data snapshot alat tambahan yang diajukan (tool_id, nama, qty, catatan)');
            }
        });

        // 2. Buat tabel perpanjangan_details untuk relasi terstruktur alat tambahan
        if (! Schema::hasTable('perpanjangan_details')) {
            Schema::create('perpanjangan_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('perpanjangan_id')->constrained('perpanjangans')->cascadeOnDelete();
                $table->foreignId('tool_id')->constrained('tools')->cascadeOnDelete();
                $table->unsignedInteger('qty')->default(1);
                $table->string('catatan', 500)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perpanjangan_details');

        Schema::table('perpanjangans', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('perpanjangans', 'jenis')) {
                $columns[] = 'jenis';
            }
            if (Schema::hasColumn('perpanjangans', 'alat_tambahan')) {
                $columns[] = 'alat_tambahan';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

