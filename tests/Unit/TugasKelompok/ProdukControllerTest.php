<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\ProdukController;
use App\Models\DetailTransaksi;
use App\Models\Diskon;
use App\Models\DiskonProduk;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;

class ProdukControllerTest extends BaseTugasTest
{
    private ProdukController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(ProdukController::class);
    }

    private function request(string $method = 'POST', array $params = []): Request
    {
        return Request::create('/', $method, $params);
    }

    public function test_index_menampilkan_semua_produk(): void
    {
        $kategori = $this->createKategori();
        $this->createProduk($kategori);
        $this->createProduk($kategori);

        $view = $this->controller->index($this->request('GET'));

        $this->assertSame('produk.index', $view->name());
        $this->assertCount(2, $view->getData()['produk']);
    }

    public function test_index_filter_kategori(): void
    {
        $kat1 = $this->createKategori();
        $kat2 = Kategori::create(['nama_kategori' => 'Kategori Lain']);
        $this->createProduk($kat1);
        $this->createProduk($kat2);

        $view = $this->controller->index($this->request('GET', ['kategori' => $kat1->id_kategori]));

        $this->assertCount(1, $view->getData()['produk']);
        $this->assertSame($kat1->id_kategori, $view->getData()['produk']->first()->id_kategori);
    }

    public function test_index_search_nama_produk(): void
    {
        $kategori = $this->createKategori();
        $this->createProduk($kategori)->update(['nama_produk' => 'Beras Premium']);
        $this->createProduk($kategori)->update(['nama_produk' => 'Minyak Goreng']);

        $view = $this->controller->index($this->request('GET', ['search' => 'Beras']));

        $this->assertCount(1, $view->getData()['produk']);
        $this->assertStringContainsString('Beras', $view->getData()['produk']->first()->nama_produk);
    }

    public function test_index_sort_stok_toko_asc(): void
    {
        $kategori = $this->createKategori();
        $this->createProduk($kategori)->update(['stok_toko' => 50, 'nama_produk' => 'Stok Banyak']);
        $this->createProduk($kategori)->update(['stok_toko' => 5, 'nama_produk' => 'Stok Sedikit']);

        $view = $this->controller->index($this->request('GET', ['sort' => 'stok_toko_asc']));

        $this->assertSame('Stok Sedikit', $view->getData()['produk']->first()->nama_produk);
    }

    public function test_index_sort_menipis(): void
    {
        $kategori = $this->createKategori();
        $this->createProduk($kategori)->update(['stok_toko' => 50, 'stok_gudang' => 100]);
        $this->createProduk($kategori)->update(['stok_toko' => 8, 'stok_gudang' => 20]);
        $this->createProduk($kategori)->update(['stok_toko' => 15, 'stok_gudang' => 3]);

        $view = $this->controller->index($this->request('GET', ['sort' => 'menipis']));

        $this->assertCount(2, $view->getData()['produk']);
    }

    public function test_index_kategori_aktif_saja_ditampilkan(): void
    {
        $katAktif = Kategori::create(['nama_kategori' => 'Aktif', 'status' => 'active']);
        $katNonAktif = Kategori::create(['nama_kategori' => 'NonAktif', 'status' => 'inactive']);

        $this->createProduk($katAktif);
        $this->createProduk($katNonAktif);

        $view = $this->controller->index($this->request('GET'));

        $this->assertCount(1, $view->getData()['kategori']);
        $this->assertSame('Aktif', $view->getData()['kategori']->first()->nama_kategori);
    }

    public function test_create_menampilkan_view_dengan_kategori_aktif(): void
    {
        Kategori::create(['nama_kategori' => 'Aktif', 'status' => 'active']);
        Kategori::create(['nama_kategori' => 'NonAktif', 'status' => 'inactive']);

        $view = $this->controller->create();

        $this->assertSame('produk.create', $view->name());
        $this->assertCount(1, $view->getData()['kategori']);
    }

    public function test_store_produk_berhasil(): void
    {
        $kategori = $this->createKategori();

        $response = $this->controller->store($this->request('POST', [
            'nama_produk' => 'Produk Baru Test',
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 15000,
            'harga_beli' => 10000,
            'harga_grosir' => 13000,
            'minimal_grosir' => 10,
            'stok_gudang' => 100,
            'stok_toko' => 20,
            'isi_per_dus' => 12,
            'satuan' => 'pcs',
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);

        $produk = Produk::latest('id_produk')->first();
        $this->assertSame('Produk Baru Test', $produk->nama_produk);
        $this->assertSame($kategori->id_kategori, $produk->id_kategori);
        $this->assertSame(15000, $produk->harga_satuan);
        $this->assertSame(13000, $produk->harga_grosir);
        $this->assertStringStartsWith('PRD', $produk->kode_produk);
    }

    public function test_store_produk_nama_duplikat_error(): void
    {
        $kategori = $this->createKategori();
        $this->createProduk($kategori)->update(['nama_produk' => 'Nama Sama']);

        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_produk' => 'Nama Sama',
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 10000,
            'stok_gudang' => 10,
            'stok_toko' => 5,
            'isi_per_dus' => 1,
        ]));
    }

    public function test_store_produk_harga_grosir_lebih_besar_error(): void
    {
        $kategori = $this->createKategori();

        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_produk' => 'Produk Grosir Error',
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 10000,
            'harga_grosir' => 12000,
            'stok_gudang' => 10,
            'stok_toko' => 5,
            'isi_per_dus' => 1,
        ]));
    }

    public function test_store_produk_validasi_required(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', []));
    }

    public function test_store_produk_satuan_validasi(): void
    {
        $kategori = $this->createKategori();

        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_produk' => 'Produk Satuan',
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 10000,
            'stok_gudang' => 10,
            'stok_toko' => 5,
            'isi_per_dus' => 1,
            'satuan' => 'invalid',
        ]));
    }

    public function test_edit_menampilkan_view_dengan_data_produk(): void
    {
        $kategori = $this->createKategori();
        $produk = $this->createProduk($kategori);

        $view = $this->controller->edit($produk->id_produk);

        $this->assertSame('produk.edit', $view->name());
        $this->assertTrue($view->getData()['produk']->is($produk));
        $this->assertCount(1, $view->getData()['kategori']);
    }

    public function test_edit_produk_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->edit(999999);
    }

    public function test_update_produk_berhasil(): void
    {
        $kategori = $this->createKategori();
        $produk = $this->createProduk($kategori);
        $produk->update(['harga_satuan' => 10000, 'stok_toko' => 10, 'stok_gudang' => 50]);

        $response = $this->controller->update($this->request('PUT', [
            'nama_produk' => 'Produk Diupdate',
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 12000,
            'harga_beli' => 8000,
            'harga_grosir' => 11000,
            'minimal_grosir' => 5,
            'stok_gudang' => 60,
            'stok_toko' => 15,
            'isi_per_dus' => 12,
            'satuan' => 'box',
        ]), $produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $produk->refresh();
        $this->assertSame('Produk Diupdate', $produk->nama_produk);
        $this->assertSame(12000, $produk->harga_satuan);
        $this->assertSame('box', $produk->satuan);
    }

    public function test_update_produk_nama_duplikat_error(): void
    {
        $kategori = $this->createKategori();
        $produk1 = $this->createProduk($kategori);
        $produk2 = $this->createProduk($kategori);

        $this->expectException(ValidationException::class);

        $this->controller->update($this->request('PUT', [
            'nama_produk' => $produk1->nama_produk,
            'id_kategori' => $kategori->id_kategori,
            'harga_satuan' => 10000,
            'stok_gudang' => 10,
            'stok_toko' => 5,
            'isi_per_dus' => 1,
        ]), $produk2->id_produk);
    }

    public function test_destroy_produk_berhasil_soft_delete(): void
    {
        $produk = $this->createProduk();

        $response = $this->controller->destroy($produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSoftDeleted('produk', ['id_produk' => $produk->id_produk]);
    }

    public function test_destroy_produk_gagal_jika_punya_detail_transaksi(): void
    {
        $produk = $this->createProduk();
        $transaksi = Transaksi::create([
            'jenis' => 'penjualan',
            'tanggal' => now()->toDateString(),
            'id_user' => $this->pemilik->id,
            'total' => 10000,
        ]);
        DetailTransaksi::create([
            'id_transaksi' => $transaksi->id_transaksi,
            'id_produk' => $produk->id_produk,
            'jumlah' => 1,
            'subtotal' => 10000,
            'tipe_stok' => 'toko',
        ]);

        $response = $this->controller->destroy($produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('produk', ['id_produk' => $produk->id_produk, 'deleted_at' => null]);
    }

    public function test_destroy_produk_gagal_jika_punya_diskon(): void
    {
        $produk = $this->createProduk();
        $diskon = Diskon::create([
            'kode_diskon' => 'DISC1',
            'nama_diskon' => 'Diskon Test',
            'besar_diskon' => 10,
            'is_aktif' => true,
            'mulai_tgl' => now()->subDay()->toDateString(),
            'selesai_tgl' => now()->addDay()->toDateString(),
            'lokasi_berlaku' => 'semua',
        ]);
        DiskonProduk::create([
            'id_diskon' => $diskon->id_diskon,
            'id_produk' => $produk->id_produk,
        ]);

        $response = $this->controller->destroy($produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('produk', ['id_produk' => $produk->id_produk, 'deleted_at' => null]);
    }

    public function test_sampah_menampilkan_produk_terhapus(): void
    {
        $produk = $this->createProduk();
        $produk->delete();

        $view = $this->controller->sampah();

        $this->assertSame('produk.sampah', $view->name());
        $this->assertCount(1, $view->getData()['sampah']);
    }

    public function test_pulihkan_produk_berhasil(): void
    {
        $produk = $this->createProduk();
        $produk->delete();

        $response = $this->controller->pulihkan($produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('produk', ['id_produk' => $produk->id_produk, 'deleted_at' => null]);
    }

    public function test_hapus_permanen_produk_berhasil(): void
    {
        $produk = $this->createProduk();
        $produk->delete();

        $response = $this->controller->hapusPermanen($produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseMissing('produk', ['id_produk' => $produk->id_produk]);
    }

    public static function transferBoundaryProvider(): array
    {
        return [
            'exact stock' => [10, 10, 12, 0, 125],
            'one below stock' => [10, 9, 12, 1, 113],
        ];
    }

    #[DataProvider('transferBoundaryProvider')]
    public function test_transfer_stok_boundary_dengan_data_provider(int $stokGudang, int $jumlahDus, int $isiDus, int $expectedGudang, int $expectedToko): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_gudang' => $stokGudang, 'stok_toko' => 5, 'isi_per_dus' => $isiDus]);

        $response = $this->controller->transferStok($this->request('POST', ['jumlah_dus' => $jumlahDus]), $produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $produk->refresh();
        $this->assertSame($expectedGudang, $produk->stok_gudang);
        $this->assertSame($expectedToko, $produk->stok_toko);
    }

    public function test_transfer_stok_berhasil(): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_gudang' => 20, 'stok_toko' => 5, 'isi_per_dus' => 12]);

        $response = $this->controller->transferStok($this->request('POST', ['jumlah_dus' => 1]), $produk->id_produk);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $produk->refresh();
        $this->assertSame(19, $produk->stok_gudang);
        $this->assertSame(17, $produk->stok_toko);
    }

    public function test_transfer_stok_stok_tidak_cukup_error(): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_gudang' => 0, 'isi_per_dus' => 12]);

        $response = $this->controller->transferStok($this->request('POST', ['jumlah_dus' => 1]), $produk->id_produk);

        // Controller pakai back()->withErrors, bukan exception.
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $produk->refresh();
        $this->assertSame(0, $produk->stok_gudang);
    }

    public function test_transfer_stok_validasi_jumlah_dus(): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_gudang' => 10, 'isi_per_dus' => 12]);

        $this->expectException(ValidationException::class);

        $this->controller->transferStok($this->request('POST', ['jumlah_dus' => 0]), $produk->id_produk);
    }

    public function test_transfer_stok_produk_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->transferStok($this->request('POST', ['jumlah_dus' => 1]), 999999);
    }
}
