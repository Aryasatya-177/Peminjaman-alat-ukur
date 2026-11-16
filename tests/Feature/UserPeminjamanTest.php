<?php

namespace Tests\Feature;

use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use App\Models\Perpanjangan;
use App\Models\Tool;
use App\Models\User;
use Database\Seeders\ToolSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(ToolSeeder::class);
    }

    public function test_guest_is_redirected_to_login_page(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_user_can_register_with_department_and_pic()
    {
        $userData = [
            'name' => 'Doni Saputra',
            'email' => 'doni@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'department' => 'Tooling & Mold',
            'pic' => 'Doni Saputra (Tooling Lead)',
        ];

        $response = $this->post('/register', $userData);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => 'doni@test.com',
            'department' => 'Tooling & Mold',
            'pic' => 'Doni Saputra (Tooling Lead)',
            'role' => 'user',
        ]);
    }

    public function test_user_can_login_and_access_dashboard()
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticatedAs($user);

        $dashboardResponse = $this->actingAs($user)->get(route('user.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Dashboard Peminjaman Saya');
    }

    public function test_user_can_access_create_peminjaman_page()
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $response = $this->actingAs($user)->get(route('user.pinjam.create'));
        $response->assertStatus(200);
    }

    public function test_user_can_borrow_tools_and_stock_is_decremented()
    {
        $user = User::where('email', 'user@example.com')->first();
        $tool = Tool::first();
        $initialStock = $tool->available_stock;

        $response = $this->actingAs($user)->post(route('user.pinjam.store'), [
            'department' => 'Quality Control',
            'pic' => 'Budi Santoso',
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(3)->format('Y-m-d'),
            'tool_id' => [$tool->id],
            'qty' => [2],
        ]);

        $response->assertSessionHasNoErrors();
        $peminjaman = Peminjaman::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($peminjaman);
        $this->assertEquals('dipinjam', $peminjaman->status);

        // Check stock decremented
        $tool->refresh();
        $this->assertEquals($initialStock - 2, $tool->available_stock);

        // Check detail_peminjamen
        $this->assertDatabaseHas('detail_peminjamen', [
            'peminjaman_id' => $peminjaman->id,
            'tool_id' => $tool->id,
            'qty' => 2,
        ]);

        // User can view loan receipt
        $showResponse = $this->actingAs($user)->get(route('user.pinjam.show', $peminjaman->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('BUKTI PEMINJAMAN ALAT UKUR PRESISI');
    }

    public function test_user_can_submit_return_and_status_changes_to_waiting_technician()
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $tool = Tool::first();

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => 'Quality Control',
            'pic' => 'Budi Santoso',
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(3)->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'tool_id' => $tool->id,
            'qty' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('user.return', $peminjaman->id));
        $response->assertSessionHas('success');

        $peminjaman->refresh();
        $this->assertEquals('menunggu_cek_teknisi', $peminjaman->status);
    }

    public function test_user_can_add_tools_to_active_loan_within_loan_period_with_separated_notes()
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $tools = Tool::take(2)->get();
        $tool1 = $tools[0];
        $tool2 = $tools[1];

        // 1. Peminjaman Pertama dengan Catatan Pertama
        $response = $this->actingAs($user)->post(route('user.pinjam.store'), [
            'department' => 'Quality Control',
            'pic' => 'Budi Santoso',
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(4)->format('Y-m-d'),
            'catatan_pertama' => 'Catatan Utama Proyek Tooling Batch 1',
            'tool_id' => [$tool1->id],
            'qty' => [1],
        ]);

        $response->assertSessionHasNoErrors();
        $peminjaman = Peminjaman::where('user_id', $user->id)->latest()->first();
        $this->assertNotNull($peminjaman);
        $this->assertTrue($peminjaman->isMasihDalamWaktuPinjam());
        $this->assertEquals('Catatan Utama Proyek Tooling Batch 1', $peminjaman->getCatatanPertama());

        // 2. Tambahan Peminjaman dengan Catatan Tambahan Terpisah
        $responseTambahan = $this->actingAs($user)->post(route('user.pinjam.store'), [
            'peminjaman_id' => $peminjaman->id,
            'catatan_tambahan' => 'Catatan Tambahan untuk Verifikasi Toleransi',
            'tool_id' => [$tool2->id],
            'qty' => [2],
        ]);

        $responseTambahan->assertSessionHasNoErrors();
        $peminjaman->refresh();
        $this->assertTrue($peminjaman->hasTambahan());
        $this->assertEquals(1, $peminjaman->totalQtyPertama());
        $this->assertEquals(2, $peminjaman->totalQtyTambahan());
        $this->assertEquals(3, $peminjaman->totalQtyAll());
        $this->assertContains('Catatan Tambahan untuk Verifikasi Toleransi', $peminjaman->getCatatanTambahan());

        // 3. Pastikan di Dashboard data Peminjaman Pertama dan Tambahan ditampilkan terpisah
        $dashboardResponse = $this->actingAs($user)->get(route('user.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Peminjaman Pertama');
        $dashboardResponse->assertSee('Tambahan Peminjaman');
        $dashboardResponse->assertSee('Catatan Utama Proyek Tooling Batch 1');
        $dashboardResponse->assertSee('Catatan Tambahan untuk Verifikasi Toleransi');
    }

    public function test_user_cannot_add_tools_if_loan_period_has_expired()
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $tools = Tool::take(2)->get();
        $tool1 = $tools[0];
        $tool2 = $tools[1];

        // Buat peminjaman yang tanggal kembalinya sudah lewat (terlambat)
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => 'Quality Control',
            'pic' => 'Budi Santoso',
            'tanggal_pinjam' => now()->subDays(5)->format('Y-m-d'),
            'tanggal_kembali' => now()->subDays(1)->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'tool_id' => $tool1->id,
            'qty' => 1,
            'is_tambahan' => false,
            'tahap' => 1,
            'catatan' => 'Peminjaman sudah lewat waktu',
        ]);

        $this->assertFalse($peminjaman->isMasihDalamWaktuPinjam());
        $this->assertTrue($peminjaman->isTerlambat());

        // Coba menambah alat ke transaksi yang sudah expired
        $response = $this->actingAs($user)->post(route('user.pinjam.store'), [
            'peminjaman_id' => $peminjaman->id,
            'catatan_tambahan' => 'Mencoba tambah alat terlambat',
            'tool_id' => [$tool2->id],
            'qty' => [1],
        ]);

        // Harus ditolak dengan pesan error
        $response->assertSessionHas('error');
        $this->assertEquals(0, DetailPeminjaman::where('peminjaman_id', $peminjaman->id)->where('is_tambahan', true)->count());
    }

    public function test_due_today_reminder_is_displayed_on_dashboard_and_can_be_returned(): void
    {
        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'Budi Santoso',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role' => 'user',
                'department' => 'QC',
                'pic' => 'Budi',
            ]);
        }

        $tool = Tool::first();

        // Buat peminjaman yang jatuh tempo hari ini
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => 'Quality Control',
            'pic' => 'Budi Santoso',
            'tanggal_pinjam' => now()->subDays(2)->format('Y-m-d'),
            'tanggal_kembali' => now()->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'tool_id' => $tool->id,
            'qty' => 1,
            'is_tambahan' => false,
            'tahap' => 1,
            'catatan' => 'Uji kalibrasi harian',
        ]);

        // Verifikasi method model
        $this->assertTrue($peminjaman->isJatuhTempoHariIni());
        $this->assertTrue($peminjaman->isPerluDikembalikanSegera());
        $this->assertFalse($peminjaman->isTerlambat());

        // Buka dashboard user
        $response = $this->actingAs($user)->get(route('user.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Tangga Pengingat: Jadwal Pengembalian Alat Hari Ini');
        $response->assertSee('Tangga 1');
        $response->assertSee('Tangga 2');
        $response->assertSee('Tangga 3');
        $response->assertSee('Jatuh Tempo Hari Ini');

        // Lakukan pengembalian alat langsung
        $returnResponse = $this->actingAs($user)->post(route('user.return', $peminjaman->id));
        $returnResponse->assertSessionHas('success');

        $this->assertEquals('menunggu_cek_teknisi', $peminjaman->fresh()->status);
        $this->assertFalse($peminjaman->fresh()->isJatuhTempoHariIni());
    }

    public function test_user_can_request_extension_with_note(): void
    {
        $user = User::where('role', 'user')->first();
        $tool = Tool::first();

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => $user->department,
            'pic' => $user->pic,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'dipinjam',
        ]);

        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'tool_id' => $tool->id,
            'qty' => 1,
            'is_tambahan' => false,
            'tahap' => 1,
        ]);

        $newDate = now()->addDays(5)->format('Y-m-d');
        $alasan = 'Proses inspeksi toleransi poros bertingkat membutuhkan pengamatan dimensi tambahan.';

        $response = $this->actingAs($user)->post(route('user.pinjam.perpanjang', $peminjaman->id), [
            'tanggal_kembali_baru' => $newDate,
            'alasan' => $alasan,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('perpanjangans', [
            'peminjaman_id' => $peminjaman->id,
            'alasan' => $alasan,
            'status' => 'menunggu',
        ]);

        $this->assertEquals($newDate, $peminjaman->fresh()->pendingPerpanjangan->tanggal_kembali_baru->format('Y-m-d'));
        $this->assertTrue($peminjaman->fresh()->hasPendingPerpanjangan());
        $this->assertFalse($peminjaman->fresh()->bisaDiajukanPerpanjangan());
    }

    public function test_admin_can_approve_extension_and_update_return_date(): void
    {
        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();

        $originalReturnDate = now()->addDays(2)->format('Y-m-d');
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => $user->department,
            'pic' => $user->pic,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => $originalReturnDate,
            'status' => 'dipinjam',
        ]);

        $newReturnDate = now()->addDays(6)->format('Y-m-d');
        $perpanjangan = Perpanjangan::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali_lama' => $originalReturnDate,
            'tanggal_kembali_baru' => $newReturnDate,
            'alasan' => 'Pengujian geometri komponen batch 2 mundur 4 hari.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.perpanjangan.setujui', $perpanjangan->id), [
            'catatan_admin' => 'Disetujui. Pastikan alat dikembalikan tepat waktu.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $perpanjangan->refresh();
        $this->assertEquals('disetujui', $perpanjangan->status);
        $this->assertEquals($admin->id, $perpanjangan->admin_id);
        $this->assertEquals('Disetujui. Pastikan alat dikembalikan tepat waktu.', $perpanjangan->catatan_admin);
        $this->assertNotNull($perpanjangan->confirmed_at);

        // Verifikasi tanggal_kembali di tabel peminjaman terupdate
        $this->assertEquals($newReturnDate, $peminjaman->fresh()->tanggal_kembali->format('Y-m-d'));
    }

    public function test_admin_can_reject_extension_with_note(): void
    {
        $user = User::where('role', 'user')->first();
        $admin = User::where('role', 'admin')->first();

        $originalReturnDate = now()->addDays(2)->format('Y-m-d');
        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => $user->department,
            'pic' => $user->pic,
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => $originalReturnDate,
            'status' => 'dipinjam',
        ]);

        $newReturnDate = now()->addDays(6)->format('Y-m-d');
        $perpanjangan = Perpanjangan::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali_lama' => $originalReturnDate,
            'tanggal_kembali_baru' => $newReturnDate,
            'alasan' => 'Permintaan perpanjangan tambahan waktu.',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.perpanjangan.tolak', $perpanjangan->id), [
            'catatan_admin' => 'Ditolak, alat sudah dijadwalkan untuk kalibrasi rutin tahunan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $perpanjangan->refresh();
        $this->assertEquals('ditolak', $perpanjangan->status);
        $this->assertEquals($admin->id, $perpanjangan->admin_id);
        $this->assertEquals('Ditolak, alat sudah dijadwalkan untuk kalibrasi rutin tahunan.', $perpanjangan->catatan_admin);

        // Verifikasi tanggal_kembali di tabel peminjaman TIDAK berubah
        $this->assertEquals($originalReturnDate, $peminjaman->fresh()->tanggal_kembali->format('Y-m-d'));
    }
}
