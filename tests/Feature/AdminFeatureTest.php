<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Tool;
use App\Models\User;
use Database\Seeders\ToolSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(ToolSeeder::class);
    }

    public function test_admin_can_access_dashboard_with_all_metrics(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user = User::where('role', 'user')->first();
        $teknisi = User::where('role', 'teknisi')->first();
        $tool = Tool::first();

        // Create sample loan and pengembalian with tanda_kerusakan_berat
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => 'Quality Control',
            'pic' => 'Budi',
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'menunggu_konfirmasi',
        ]);

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'teknisi_id' => $teknisi->id,
            'tanggal_dikembalikan' => now(),
            'kondisi_alat' => 'rusak',
            'jumlah_baik' => 0,
            'jumlah_rusak' => 3,
            'tanda_kerusakan_berat' => true,
            'bisa_direpair' => false,
            'status_repair' => 'tidak_bisa_direpair',
            'catatan_teknisi' => 'Sensor retak berat',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Perhatian: Alat Rusak');
    }

    public function test_admin_can_view_tools_list(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.tools.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_peminjaman_list(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.peminjaman.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_users_list(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_reports(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_filter_reports_by_days(): void
    {
        $admin = User::where('role', 'admin')->first();

        // 1 hari terakhir
        $response1 = $this->actingAs($admin)->get(route('admin.reports.index', ['filter_mode' => 'range', 'days' => 1]));
        $response1->assertStatus(200);
        $response1->assertSee('Hari Ini');

        // 3 hari terakhir
        $response3 = $this->actingAs($admin)->get(route('admin.reports.index', ['filter_mode' => 'range', 'days' => 3]));
        $response3->assertStatus(200);
        $response3->assertSee('3 Hari');
    }

    public function test_admin_can_filter_reports_by_custom_date_range(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.reports.index', [
            'filter_mode' => 'range',
            'start_date' => now()->subDays(5)->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee('6 Hari');
    }

    public function test_admin_can_export_reports_to_pdf_and_word_with_flexible_dates(): void
    {
        $admin = User::where('role', 'admin')->first();

        // PDF export
        $responsePdf = $this->actingAs($admin)->get(route('admin.reports.pdf', ['filter_mode' => 'range', 'days' => 3]));
        $responsePdf->assertStatus(200);
        $responsePdf->assertSee('LAPORAN SIRKULASI ALAT UKUR PRESISI (3 HARI)');

        // Word export
        $responseWord = $this->actingAs($admin)->get(route('admin.reports.word', ['filter_mode' => 'range', 'days' => 2]));
        $responseWord->assertStatus(200);
        $responseWord->assertHeader('Content-Type', 'application/msword; charset=UTF-8');
        $this->assertStringContainsString('LAPORAN SIRKULASI ALAT UKUR PRESISI (2 HARI)', $responseWord->getContent());
    }
}
