<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\LaporanController;
use App\Models\LaporanPembelian;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanControllerTest extends BaseTugasTest
{
    private LaporanController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(LaporanController::class);
    }

    private function request(array $params = []): Request
    {
        return Request::create('/', 'GET', $params);
    }

    public function test_index_laporan_penjualan_dan_pembelian(): void
    {
        $penjualan = $this->controller->index($this->request());
        $pembelian = $this->controller->index($this->request(['tab' => 'pembelian']));

        $this->assertSame('laporan.index', $penjualan->name());
        $this->assertSame('laporan.index', $pembelian->name());
        $this->assertSame('penjualan', $penjualan->getData()['tab']);
        $this->assertSame('pembelian', $pembelian->getData()['tab']);
    }

    public function test_index_menghitung_statistik_penjualan(): void
    {
        $hari = now()->toDateString();
        Transaksi::create(['jenis' => 'penjualan', 'tanggal' => $hari, 'id_user' => $this->pemilik->id, 'total' => 500]);

        $view = $this->controller->index($this->request(['dari' => $hari, 'sampai' => $hari]));

        $this->assertSame(500, $view->getData()['totalPenjualan']);
        $this->assertSame(1, $view->getData()['totalTransaksi']);
    }

    public function test_index_kosong_di_luar_range(): void
    {
        Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->addYear()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 500]);

        $view = $this->controller->index($this->request([
            'dari' => now()->subYear()->toDateString(),
            'sampai' => now()->toDateString(),
        ]));

        $this->assertSame(0, $view->getData()['totalPenjualan']);
        $this->assertSame(0, $view->getData()['totalTransaksi']);
    }

    public function test_index_pembelian_pemilik_menghitung_total(): void
    {
        $hari = now()->toDateString();
        LaporanPembelian::create(['id_pembelian' => 0, 'tanggal' => $hari, 'total' => 100, 'id_user' => $this->pemilik->id]);

        $view = $this->controller->index($this->request(['tab' => 'pembelian', 'dari' => $hari, 'sampai' => $hari]));

        $this->assertSame(100, $view->getData()['totalPembelian']);
    }

    public function test_index_kasir_tidak_menghitung_total_pembelian(): void
    {
        $this->actingAs($this->kasir);
        $view = $this->controller->index($this->request(['tab' => 'pembelian']));

        $this->assertSame(0, $view->getData()['totalPembelian']);
    }

    public function test_export_penjualan_csv_mengembalikan_stream(): void
    {
        $response = $this->controller->exportExcel($this->request(['dari' => '2026-01-01', 'sampai' => '2026-12-31']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('laporan_penjualan_2026-01-01_sd_2026-12-31.csv', $response->headers->get('Content-Disposition'));
    }

    public function test_export_pembelian_csv_mengembalikan_stream(): void
    {
        $response = $this->controller->exportExcel($this->request(['tab' => 'pembelian']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('laporan_pembelian_', $response->headers->get('Content-Disposition'));
    }

    public function test_export_tanpa_data_tetap_mengembalikan_header(): void
    {
        $response = $this->controller->exportExcel($this->request(['dari' => '2000-01-01', 'sampai' => '2000-01-31']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
    }
}
