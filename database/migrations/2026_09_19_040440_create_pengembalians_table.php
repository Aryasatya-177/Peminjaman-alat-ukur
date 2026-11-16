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
        Schema::create('pengembalians', function (Blueprint $table) {
            $table->id();  // ← SATU saja!
            $table->foreignId('peminjaman_id')->constrained('peminjamen');
            $table->foreignId('teknisi_id')->nullable()->constrained('users');
            $table->date('tanggal_dikembalikan');
            $table->enum('kondisi_alat', ['baik', 'rusak'])->default('baik');
            $table->text('catatan_teknisi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalians');
    }
};
