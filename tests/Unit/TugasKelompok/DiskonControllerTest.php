<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\DiskonController;
use App\Models\Diskon;
use App\Models\Pelanggan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;

class DiskonControllerTest extends BaseTugasTest
{
    private DiskonController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(DiskonController::class);
    }

    private function request(array $params = []): Request
    {
        return Request::create('/', 'POST', $params);
    }

    private function payload(int $produk): array
    {
        return [
            'nama_diskon' => 'Diskon Test',
            'besar_diskon' => 10,
            'minimal_beli' => 1,
            'minimal_beli_grosir' => 1,
            'mulai_tgl' => now()->toDateString(),
            'selesai_tgl' => now()->addDays(5)->toDateString(),
            'lokasi_berlaku' => 'semua',
            'id_produk' => [$produk],
            'is_aktif' => '1',
        ];
    }

    public function test_index_dan_create_menampilkan_data(): void
    {
        $produk = $this->createProduk();

        $indexView = $this->controller->index();
        $createView = $this->controller->create();

        $this->assertSame('diskon.index', $indexView->name());
        $this->assertSame('diskon.create', $createView->name());
        $this->assertTrue($createView->getData()['produk']->contains($produk));
    }

    public function test_store_diskon_dan_relasi_produk(): void
    {
        $produk = $this->createProduk();

        $response = $this->controller->store($this->request($this->payload($produk->id_produk)));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $diskon = Diskon::latest('id_diskon')->first();
        $this->assertTrue($diskon->produk->contains($produk));
    }

    public function test_store_diskon_validasi_batas(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request(['besar_diskon' => 101]));
    }

    public function test_store_diskon_false_aktif_tanpa_checkbox(): void
    {
        $produk = $this->createProduk();
        $data = $this->payload($produk->id_produk);
        unset($data['is_aktif']);

        $this->controller->store($this->request($data));

        $this->assertDatabaseHas('diskon', ['is_aktif' => 0]);
    }

    public function test_store_diskon_pelanggan_spesifik_dan_minimum_boundary(): void
    {
        $produk = $this->createProduk();
        $pelanggan = $this->createPelanggan();
        $data = $this->payload($produk->id_produk);
        $data['id_pelanggan'] = $pelanggan->id_pelanggan;
        $data['minimal_beli'] = 5;

        $this->controller->store($this->request($data));

        $diskon = Diskon::latest('id_diskon')->first();
        $this->assertSame($pelanggan->id_pelanggan, $diskon->id_pelanggan);
        $this->assertSame(5, $diskon->minimal_beli);
    }

    public function test_update_diskon_menolak_tanggal_selesai_sebelum_mulai(): void
    {
        $produk = $this->createProduk();
        $this->controller->store($this->request($this->payload($produk->id_produk)));
        $diskon = Diskon::latest('id_diskon')->first();
        $data = $this->payload($produk->id_produk);
        $data['selesai_tgl'] = now()->subDay()->toDateString();

        $this->expectException(ValidationException::class);

        $this->controller->update($this->request($data), $diskon->id_diskon);
    }

    public function test_edit_diskon_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->edit(999999);
    }

    public function test_destroy_diskon_tidak_ditemukan(): void
    {
        // destroy() uses Diskon::destroy($id), missing ID is no-op.
        $response = $this->controller->destroy(999999);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public static function lokasiBerlakuProvider(): array
    {
        return ['semua' => ['semua'], 'gudang' => ['gudang'], 'toko' => ['toko']];
    }

    #[DataProvider('lokasiBerlakuProvider')]
    public function test_store_diskon_menerima_setiap_lokasi_berlaku(string $lokasi): void
    {
        $produk = $this->createProduk();
        $data = $this->payload($produk->id_produk);
        $data['lokasi_berlaku'] = $lokasi;

        $this->controller->store($this->request($data));

        $this->assertDatabaseHas('diskon', ['lokasi_berlaku' => $lokasi]);
    }

    public function test_store_diskon_menolak_produk_tidak_ditemukan(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request($this->payload(999999)));
    }

    public function test_store_diskon_menolak_pelanggan_tidak_ditemukan(): void
    {
        $produk = $this->createProduk();
        $data = $this->payload($produk->id_produk);
        $data['id_pelanggan'] = 999999;

        $this->expectException(ValidationException::class);

        $this->controller->store($this->request($data));
    }

    public function test_store_diskon_nilai_batas_minimum_dan_maksimum(): void
    {
        $produk = $this->createProduk();

        foreach ([1, 100] as $besar) {
            $data = $this->payload($produk->id_produk);
            $data['besar_diskon'] = $besar;
            $this->controller->store($this->request($data));
        }

        $this->assertDatabaseCount('diskon', 2);
    }

    public function test_store_diskon_menolak_nilai_diskon_di_luar_batas(): void
    {
        $produk = $this->createProduk();

        foreach ([0, 101] as $besar) {
            $data = $this->payload($produk->id_produk);
            $data['besar_diskon'] = $besar;

            try {
                $this->controller->store($this->request($data));
                $this->fail('Expected validation exception for besar_diskon ' . $besar);
            } catch (ValidationException $e) {
                $this->assertTrue(true);
            }
        }
    }

    public function test_edit_update_diskon(): void
    {
        $produk = $this->createProduk();
        $this->controller->store($this->request($this->payload($produk->id_produk)));
        $diskon = Diskon::latest('id_diskon')->first();

        $view = $this->controller->edit($diskon->id_diskon);
        $this->assertSame('diskon.edit', $view->name());
        $this->assertTrue($view->getData()['diskon']->is($diskon));

        $data = $this->payload($produk->id_produk);
        $data['nama_diskon'] = 'Diskon Updated';
        $this->controller->update($this->request($data), $diskon->id_diskon);

        $this->assertDatabaseHas('diskon', ['id_diskon' => $diskon->id_diskon, 'nama_diskon' => 'Diskon Updated']);
    }

    public function test_destroy_sampah_restore_dan_permanen(): void
    {
        $produk = $this->createProduk();
        $this->controller->store($this->request($this->payload($produk->id_produk)));
        $diskon = Diskon::latest('id_diskon')->first();

        $this->controller->destroy($diskon->id_diskon);
        $this->assertSoftDeleted('diskon', ['id_diskon' => $diskon->id_diskon]);

        $view = $this->controller->sampah();
        $this->assertSame('diskon.sampah', $view->name());

        $this->controller->pulihkan($diskon->id_diskon);
        $this->assertDatabaseHas('diskon', ['id_diskon' => $diskon->id_diskon, 'deleted_at' => null]);

        $diskon->delete();
        $this->controller->hapusPermanen($diskon->id_diskon);
        $this->assertDatabaseMissing('diskon', ['id_diskon' => $diskon->id_diskon]);
    }
}
