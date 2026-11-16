<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi kolom status di tabel peminjamen agar dapat menampung status baru:
        // 'dipinjam', 'menunggu_cek_teknisi', 'dalam_perbaikan', 'menunggu_konfirmasi', 'rusak_dikonfirmasi', 'selesai', 'rusak'
        try {
            DB::statement("ALTER TABLE `peminjamen` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'dipinjam'");
        } catch (Throwable $e) {
            // Fallback jika bukan MySQL atau jika ALTER TABLE standar
            Schema::table('peminjamen', function (Blueprint $table) {
                $table->string('status', 50)->default('dipinjam')->change();
            });
        }

        // 2. Tambahkan kolom pendukung di tabel pengembalians
        Schema::table('pengembalians', function (Blueprint $table) {
            if (! Schema::hasColumn('pengembalians', 'jumlah_baik')) {
                $table->integer('jumlah_baik')->default(0)->after('kondisi_alat');
            }
            if (! Schema::hasColumn('pengembalians', 'jumlah_rusak')) {
                $table->integer('jumlah_rusak')->default(0)->after('jumlah_baik');
            }
            if (! Schema::hasColumn('pengembalians', 'tanda_kerusakan_berat')) {
                $table->boolean('tanda_kerusakan_berat')->default(false)->after('jumlah_rusak')
                    ->comment('Otomatis TRUE jika jumlah rusak >= 3 unit');
            }
            if (! Schema::hasColumn('pengembalians', 'bisa_direpair')) {
                $table->boolean('bisa_direpair')->nullable()->after('tanda_kerusakan_berat')
                    ->comment('TRUE: bisa direpair, FALSE: tidak bisa direpair');
            }
            if (! Schema::hasColumn('pengembalians', 'status_repair')) {
                $table->string('status_repair', 50)->default('tidak_perlu')->after('bisa_direpair')
                    ->comment('tidak_perlu, dalam_perbaikan, selesai_direpair, tidak_bisa_direpair');
            }
            if (! Schema::hasColumn('pengembalians', 'catatan_perbaikan')) {
                $table->text('catatan_perbaikan')->nullable()->after('catatan_teknisi');
            }

            // Kolom Konfirmasi Admin
            if (! Schema::hasColumn('pengembalians', 'konfirmasi_admin')) {
                $table->boolean('konfirmasi_admin')->default(false)->after('catatan_perbaikan');
            }
            if (! Schema::hasColumn('pengembalians', 'konfirmasi_admin_at')) {
                $table->timestamp('konfirmasi_admin_at')->nullable()->after('konfirmasi_admin');
            }
            if (! Schema::hasColumn('pengembalians', 'konfirmasi_admin_by')) {
                $table->foreignId('konfirmasi_admin_by')->nullable()->after('konfirmasi_admin_at')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('pengembalians', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('konfirmasi_admin_by');
            }
            if (! Schema::hasColumn('pengembalians', 'tindakan_admin')) {
                $table->string('tindakan_admin', 50)->nullable()->after('catatan_admin')
                    ->comment('afkir_aset, bebankan_ganti_rugi, toleransi, lainnya');
            }

            // Kolom Konfirmasi User Terkait
            if (! Schema::hasColumn('pengembalians', 'konfirmasi_user')) {
                $table->boolean('konfirmasi_user')->default(false)->after('tindakan_admin');
            }
            if (! Schema::hasColumn('pengembalians', 'konfirmasi_user_at')) {
                $table->timestamp('konfirmasi_user_at')->nullable()->after('konfirmasi_user');
            }
            if (! Schema::hasColumn('pengembalians', 'catatan_user')) {
                $table->text('catatan_user')->nullable()->after('konfirmasi_user_at');
            }
        });

        // 3. Tambahkan kolom kondisi detail per item alat jika ada
        Schema::table('detail_peminjamen', function (Blueprint $table) {
            if (! Schema::hasColumn('detail_peminjamen', 'qty_baik')) {
                $table->integer('qty_baik')->nullable()->after('qty');
            }
            if (! Schema::hasColumn('detail_peminjamen', 'qty_rusak')) {
                $table->integer('qty_rusak')->nullable()->after('qty_baik');
            }
            if (! Schema::hasColumn('detail_peminjamen', 'kondisi_detail')) {
                $table->string('kondisi_detail', 50)->default('baik')->after('qty_rusak');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_peminjamen', function (Blueprint $table) {
            $table->dropColumn(['qty_baik', 'qty_rusak', 'kondisi_detail']);
        });

        Schema::table('pengembalians', function (Blueprint $table) {
            $table->dropForeign(['konfirmasi_admin_by']);
            $table->dropColumn([
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
            ]);
        });
    }
};
