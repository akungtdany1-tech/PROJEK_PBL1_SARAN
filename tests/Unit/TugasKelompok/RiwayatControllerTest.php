<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\RiwayatController;
use App\Models\RiwayatAksi;
use Illuminate\Http\Request;

class RiwayatControllerTest extends BaseTugasTest
{
    private RiwayatController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(RiwayatController::class);
    }

    private function request(array $params = []): Request
    {
        return Request::create('/', 'GET', $params);
    }

    private function riwayat(string $aksi, string $label): RiwayatAksi
    {
        return RiwayatAksi::create([
            'id_user' => $this->pemilik->id,
            'role' => 'pemilik',
            'aksi' => $aksi,
            'label' => $label,
        ]);
    }

    public function test_index_menampilkan_riwayat_kosong_dan_terisi(): void
    {
        $view = $this->controller->index($this->request());
        $this->assertSame('riwayat.index', $view->name());
        $baseline = $view->getData()['riwayat']->count();

        $this->riwayat('produk_buat', 'Produk Baru');
        $view = $this->controller->index($this->request());
        $this->assertCount($baseline + 1, $view->getData()['riwayat']);
    }

    public function test_index_filter_kejadian_dan_pencarian(): void
    {
        $this->riwayat('produk_buat', 'Produk Cocok');
        $this->riwayat('login', 'Login');

        $view = $this->controller->index($this->request(['kejadian' => 'produk', 'cari' => 'Cocok']));

        $this->assertCount(1, $view->getData()['riwayat']);
        $this->assertSame('Produk Cocok', $view->getData()['riwayat']->first()->label);
    }

    public function test_index_filter_data_master_mengeluarkan_auth_dan_transaksi(): void
    {
        $this->riwayat('produk_buat', 'Produk');
        $this->riwayat('login', 'Login');
        $this->riwayat('penjualan_buat', 'Penjualan');

        $view = $this->controller->index($this->request(['kejadian' => 'data_master']));

        $this->assertGreaterThanOrEqual(1, $view->getData()['riwayat']->count());
        $this->assertTrue($view->getData()['riwayat']->contains(fn ($item) => $item->aksi === 'produk_buat'));
    }

    public function test_index_filter_tanggal(): void
    {
        $item = $this->riwayat('produk_buat', 'Hari Ini');
        $item->created_at = now()->subDays(3);
        $item->save();
        $this->riwayat('produk_buat', 'Sekarang');

        $view = $this->controller->index($this->request([
            'dari' => now()->subDay()->toDateString(),
            'sampai' => now()->toDateString(),
        ]));

        $this->assertTrue($view->getData()['riwayat']->contains(fn ($item) => $item->label === 'Sekarang'));
    }
}
