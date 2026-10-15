<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\TransaksiController;
use App\Models\DetailTransaksi;
use App\Models\Diskon;
use App\Models\Produk;
use App\Models\Suplier;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;

class TransaksiControllerTest extends BaseTugasTest
{
    private TransaksiController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(TransaksiController::class);
    }

    private function request(array $params = []): Request
    {
        $request = Request::create('/', 'POST', $params);
        $request->setUserResolver(fn () => auth()->user());
        $request->setLaravelSession(app('session')->driver());
        return $request;
    }

    private function salePayload(Produk $produk, int $jumlah = 1, string $tipe = 'eceran', int $bayar = 10000): array
    {
        return [
            'keranjang' => [['id_produk' => $produk->id_produk, 'jumlah' => $jumlah, 'tipe' => $tipe]],
            'metode_pembayaran' => 'tunai',
            'bayar' => $bayar,
        ];
    }

    public function test_index_penjualan_menampilkan_transaksi_user_aktif(): void
    {
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 25000]);
        Transaksi::create(['jenis' => 'pembelian', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 30000]);

        $view = $this->controller->index();
        $this->assertSame('transaksi.index', $view->name());
        $this->assertTrue($view->getData()['transaksi']->contains($sale));
    }

    public function test_create_menampilkan_data(): void
    {
        $view = $this->controller->create();
        $this->assertSame('transaksi.create', $view->name());
        $this->assertArrayHasKey('produk', $view->getData());
        $this->assertArrayHasKey('kategori', $view->getData());
        $this->assertArrayHasKey('pelanggan', $view->getData());
    }

    public function test_stok_terkini_mengembalikan_produk_aktif_dan_terhapus(): void
    {
        $active = $this->createProduk();
        $deleted = $this->createProduk();
        $deleted->delete();

        $response = $this->controller->stokTerkini();
        $data = $response->getData(true);

        $this->assertFalse($data[$active->id_produk]['deleted']);
        $this->assertTrue($data[$deleted->id_produk]['deleted']);
    }

    public static function tipeStokProvider(): array
    {
        return ['eceran' => ['eceran', 'stok_toko', 10000], 'grosir' => ['grosir', 'stok_gudang', 9000]];
    }

    #[DataProvider('tipeStokProvider')]
    public function test_store_penjualan_tipe_stok_valid(string $tipe, string $stokColumn, int $harga): void
    {
        $produk = $this->createProduk();
        $produk->update([$stokColumn => 10, 'harga_satuan' => 10000, 'harga_grosir' => $harga]);

        $this->controller->store($this->request($this->salePayload($produk, 1, $tipe, $harga)));

        $this->assertDatabaseHas('detail_transaksi', ['id_produk' => $produk->id_produk, 'tipe' => $tipe, 'jumlah' => 1]);
    }

    public function test_store_penjualan_validasi_keranjang_kosong(): void
    {
        $this->expectException(ValidationException::class);
        $this->controller->store($this->request(['keranjang' => [], 'metode_pembayaran' => 'tunai', 'bayar' => 1]));
    }

    public function test_store_penjualan_tipe_invalid(): void
    {
        $produk = $this->createProduk();
        $this->expectException(ValidationException::class);
        $this->controller->store($this->request($this->salePayload($produk, 1, 'invalid')));
    }

    public function test_store_penjualan_stok_tidak_cukup(): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_toko' => 0]);
        $response = $this->controller->store($this->request($this->salePayload($produk, 1, 'eceran')));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(0, $produk->fresh()->stok_toko);
    }

    public function test_store_penjualan_produk_terhapus(): void
    {
        $produk = $this->createProduk();
        $produk->delete();
        $response = $this->controller->store($this->request($this->salePayload($produk)));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_store_penjualan_bayar_kurang(): void
    {
        $produk = $this->createProduk();
        $produk->update(['harga_satuan' => 10000, 'stok_toko' => 10]);
        $response = $this->controller->store($this->request($this->salePayload($produk, 2, 'eceran', 1)));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(0, Transaksi::count());
    }

    public function test_store_penjualan_diskon_aktif_minimum_terpenuhi(): void
    {
        $produk = $this->createProduk();
        $produk->update(['harga_satuan' => 10000, 'stok_toko' => 10]);
        $diskon = Diskon::create(['kode_diskon' => 'D' . uniqid(), 'nama_diskon' => 'Diskon', 'besar_diskon' => 10, 'minimal_beli' => 1, 'is_aktif' => true, 'mulai_tgl' => now()->subDay(), 'selesai_tgl' => now()->addDay(), 'lokasi_berlaku' => 'semua']);
        $produk->diskon()->attach($diskon->id_diskon);

        $this->controller->store($this->request($this->salePayload($produk, 2, 'eceran', 18000)));

        $this->assertDatabaseHas('transaksi', ['total_diskon' => 2000, 'total' => 18000]);
    }

    public function test_store_penjualan_multi_items(): void
    {
        $one = $this->createProduk();
        $two = $this->createProduk();
        $one->update(['harga_satuan' => 10000, 'stok_toko' => 10]);
        $two->update(['harga_satuan' => 5000, 'stok_toko' => 10]);
        $request = $this->request(['keranjang' => [
            ['id_produk' => $one->id_produk, 'jumlah' => 1, 'tipe' => 'eceran'],
            ['id_produk' => $two->id_produk, 'jumlah' => 1, 'tipe' => 'eceran'],
        ], 'metode_pembayaran' => 'tunai', 'bayar' => 15000]);

        $this->controller->store($request);

        $this->assertDatabaseHas('transaksi', ['total' => 15000]);
        $this->assertSame(9, $one->fresh()->stok_toko);
        $this->assertSame(9, $two->fresh()->stok_toko);
    }

    public function test_show_penjualan_dan_missing(): void
    {
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        $view = $this->controller->show($sale->id_transaksi);
        $this->assertSame('transaksi.show', $view->name());

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller->show(999999);
    }

    public function test_destroy_penjualan_mengembalikan_stok_dan_soft_delete(): void
    {
        $produk = $this->createProduk();
        $produk->update(['stok_toko' => 10]);
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        DetailTransaksi::create(['id_transaksi' => $sale->id_transaksi, 'id_produk' => $produk->id_produk, 'jumlah' => 5, 'harga' => 20, 'subtotal' => 100, 'tipe_stok' => 'toko']);

        $this->controller->destroy($sale->id_transaksi);

        $this->assertSoftDeleted('transaksi', ['id_transaksi' => $sale->id_transaksi]);
        $this->assertSame(15, $produk->fresh()->stok_toko);
    }

    public function test_sampah_pulihkan_hapus_permanen(): void
    {
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        $sale->delete();
        $view = $this->controller->sampah();
        $this->assertSame('transaksi.sampah', $view->name());
        $this->controller->pulihkan($sale->id_transaksi);
        $this->assertDatabaseHas('transaksi', ['id_transaksi' => $sale->id_transaksi, 'deleted_at' => null]);
        $sale->delete();
        $this->controller->hapusPermanen($sale->id_transaksi);
        $this->assertDatabaseMissing('transaksi', ['id_transaksi' => $sale->id_transaksi]);
    }

    public function test_hapus_permanen_ditolak_jika_punya_detail(): void
    {
        $produk = $this->createProduk();
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        DetailTransaksi::create(['id_transaksi' => $sale->id_transaksi, 'id_produk' => $produk->id_produk, 'jumlah' => 1, 'harga' => 100, 'subtotal' => 100, 'tipe_stok' => 'toko']);
        $sale->delete();
        $this->controller->hapusPermanen($sale->id_transaksi);
        $this->assertSoftDeleted('transaksi', ['id_transaksi' => $sale->id_transaksi]);
    }

    public function test_index_pembelian_dan_create_pembelian(): void
    {
        $view = $this->controller->indexPembelian();
        $create = $this->controller->createPembelian();
        $this->assertSame('pembelian.index', $view->name());
        $this->assertSame('pembelian.create', $create->name());
    }

    public function test_store_pembelian_valid_dan_invalid(): void
    {
        $produk = $this->createProduk();
        $this->controller->storePembelian($this->request([
            'tanggal' => now()->toDateString(), 'id_produk' => [$produk->id_produk], 'jumlah' => [5], 'harga_beli' => [7000],
        ]));
        $this->assertDatabaseHas('transaksi', ['jenis' => 'pembelian', 'total' => 35000]);
        $this->assertSame(105, $produk->fresh()->stok_gudang);

        $invalid = $this->controller->storePembelian($this->request(['tanggal' => 'invalid', 'id_produk' => [], 'jumlah' => [0], 'harga_beli' => [-1]]));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $invalid);
    }

    public function test_show_pembelian_dan_print_struk(): void
    {
        $purchase = Transaksi::create(['jenis' => 'pembelian', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        $sale = Transaksi::create(['jenis' => 'penjualan', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        $this->assertSame('pembelian.show', $this->controller->showPembelian($purchase->id_transaksi)->name());
        $this->assertSame('transaksi.print', $this->controller->printStruk($sale->id_transaksi)->name());
    }

    public function test_destroy_pembelian_mengembalikan_stok(): void
    {
        $produk = $this->createProduk();
        $purchase = Transaksi::create(['jenis' => 'pembelian', 'tanggal' => now()->toDateString(), 'id_user' => $this->pemilik->id, 'total' => 100]);
        DetailTransaksi::create(['id_transaksi' => $purchase->id_transaksi, 'id_produk' => $produk->id_produk, 'jumlah' => 5, 'harga_beli' => 20, 'subtotal' => 100, 'tipe_stok' => 'gudang']);
        $before = $produk->fresh()->stok_gudang;
        $this->controller->destroyPembelian($purchase->id_transaksi);
        $this->assertSame($before + 5, $produk->fresh()->stok_gudang);
        $this->assertSoftDeleted('transaksi', ['id_transaksi' => $purchase->id_transaksi]);
    }
}
