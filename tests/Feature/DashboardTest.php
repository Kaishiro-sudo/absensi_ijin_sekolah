<?php

namespace Tests\Feature;

use App\Models\Perizinan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_default_dashboard_redirects_a_guru_to_the_guru_dashboard(): void
    {
        $guru = User::factory()->create(['role' => 'guru_bk']);

        $this->actingAs($guru)
            ->get('/dashboard')
            ->assertRedirect(route('guru.dashboard'));

        $this->actingAs($guru)
            ->get(route('guru.dashboard'))
            ->assertOk();
    }

    public function test_the_satpam_can_record_a_student_leaving_and_returning(): void
    {
        $satpam = User::factory()->create(['role' => 'satpam']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $perizinan = Perizinan::create([
            'user_id' => $siswa->id,
            'alasan' => 'Keperluan sekolah',
            'jam_keluar' => now(),
            'status' => 'disetujui',
        ]);

        $this->actingAs($satpam)
            ->get(route('satpam.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Satpam')
            ->assertSee('Catat Keluar');

        $this->actingAs($satpam)
            ->post(route('satpam.perizinan.masuk', $perizinan))
            ->assertSessionHasErrors('perizinan');

        $this->actingAs($satpam)
            ->post(route('satpam.perizinan.keluar', $perizinan))
            ->assertSessionHasNoErrors();

        $this->actingAs($satpam)
            ->post(route('satpam.perizinan.masuk', $perizinan))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('log_gerbangs', 2);
        $this->assertDatabaseHas('log_gerbangs', [
            'perizinan_id' => $perizinan->id,
            'satpam_id' => $satpam->id,
            'tipe_scan' => 'keluar',
        ]);
        $this->assertDatabaseHas('log_gerbangs', [
            'perizinan_id' => $perizinan->id,
            'satpam_id' => $satpam->id,
            'tipe_scan' => 'masuk',
        ]);
        $this->assertDatabaseHas('perizinans', [
            'id' => $perizinan->id,
            'status' => 'selesai',
        ]);
    }
}
