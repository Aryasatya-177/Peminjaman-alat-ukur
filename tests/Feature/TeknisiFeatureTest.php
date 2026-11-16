<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\ToolSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeknisiFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->seed(ToolSeeder::class);
    }

    public function test_teknisi_can_access_dashboard(): void
    {
        $teknisi = User::where('role', 'teknisi')->first();

        $response = $this->actingAs($teknisi)->get(route('teknisi.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Teknisi Kalibrasi & Inspeksi');
    }

    public function test_teknisi_can_access_antrian(): void
    {
        $teknisi = User::where('role', 'teknisi')->first();

        $response = $this->actingAs($teknisi)->get(route('teknisi.antrian'));

        $response->assertStatus(200);
        $response->assertSee('Antrian Pengecekan');
    }

    public function test_teknisi_can_access_history(): void
    {
        $teknisi = User::where('role', 'teknisi')->first();

        $response = $this->actingAs($teknisi)->get(route('teknisi.history'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Inspeksi');
    }

    public function test_teknisi_can_view_peminjaman_detail_without_forbidden(): void
    {
        $teknisi = User::where('role', 'teknisi')->first();
        $user = User::where('role', 'user')->first();

        $peminjaman = Peminjaman::create([
            'user_id' => $user->id,
            'department' => 'QC',
            'pic' => 'Budi',
            'tanggal_pinjam' => now()->format('Y-m-d'),
            'tanggal_kembali' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'menunggu_cek_teknisi',
        ]);

        $response = $this->actingAs($teknisi)->get(route('user.pinjam.show', $peminjaman->id));

        $response->assertStatus(200);
        $response->assertSee('BUKTI PEMINJAMAN ALAT UKUR PRESISI');
    }
}
