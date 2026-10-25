<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\DashboardController;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardControllerTest extends BaseTugasTest
{
    private DashboardController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(DashboardController::class);
    }

    private function request(array $params = []): Request
    {
        return Request::create('/', 'GET', $params);
    }

    public function test_dashboard_bulan_ini_menampilkan_statistik(): void
    {
        Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 1000]);

        $view = $this->controller->index($this->request());

        $this->assertSame('dashboard', $view->name());
        $this->assertSame(1, $view->getData()['totalTransaksiHariIni']);
        $this->assertSame(1000, $view->getData()['totalPenjualanHariIni']);
    }

    public function test_dashboard_filter_tiga_bulan_dan_satu_tahun(): void
    {
        $three = $this->controller->index($this->request(['filter' => '3_bulan']));
        $year = $this->controller->index($this->request(['filter' => '1_tahun']));

        $this->assertSame('3_bulan', $three->getData()['filter']);
        $this->assertSame('1_tahun', $year->getData()['filter']);
    }

    public function test_dashboard_bulan_custom(): void
    {
        $view = $this->controller->index($this->request(['bulan' => 1, 'tahun' => 2026]));

        $this->assertSame('custom', $view->getData()['filter']);
        $this->assertSame(1, $view->getData()['bulan']);
        $this->assertSame(2026, $view->getData()['tahun']);
    }

    public function test_dashboard_database_kosong(): void
    {
        $view = $this->controller->index($this->request());

        $this->assertSame(0, $view->getData()['totalTransaksiHariIni']);
        $this->assertSame(0, $view->getData()['totalPenjualanHariIni']);
        $this->assertSame(0, $view->getData()['produkTerjualHariIni']);
        $this->assertSame(0, $view->getData()['jumlahStokMenipis']);
    }

    public function test_dashboard_bulan_zero_masih_diproses_sebagai_custom(): void
    {
        $view = $this->controller->index($this->request(['bulan' => 0, 'tahun' => 2026]));

        $this->assertSame('custom', $view->getData()['filter']);
    }

    public function test_dashboard_kasir_role_diizinkan(): void
    {
        $this->actingAs($this->kasir);

        $view = $this->controller->index($this->request());

        $this->assertSame('dashboard', $view->name());
    }
}
