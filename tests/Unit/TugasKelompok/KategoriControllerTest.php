<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\KategoriController;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KategoriControllerTest extends BaseTugasTest
{
    private KategoriController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = app(KategoriController::class);
    }

    private function request(string $method = 'GET', array $params = []): Request
    {
        $request = Request::create('/', $method, $params);
        $request->setUserResolver(fn () => $this->pemilik);
        return $request;
    }

    public function test_index_hanya_menampilkan_kategori_aktif(): void
    {
        Kategori::create(['nama_kategori' => 'Aktif', 'status' => 'active']);
        Kategori::create(['nama_kategori' => 'Nonaktif', 'status' => 'inactive']);

        $view = $this->controller->index();

        $this->assertSame('kategori.index', $view->name());
        $this->assertCount(1, $view->getData()['kategoris']);
    }

    public function test_store_membuat_kategori_baru(): void
    {
        $response = $this->controller->store($this->request('POST', ['nama_kategori' => 'Makanan']));

        $this->assertTrue($response->getTargetUrl() === url('/kategori') || str_contains($response->getTargetUrl(), 'kategori'));
        $this->assertDatabaseHas('kategori', ['nama_kategori' => 'Makanan', 'status' => 'active']);
    }

    public function test_store_menolak_nama_kategori_dengan_angka(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', ['nama_kategori' => 'Makanan 123']));
    }

    public function test_store_kategori_aktif_duplikat_ditolak(): void
    {
        Kategori::create(['nama_kategori' => 'Makanan', 'status' => 'active']);

        $response = $this->controller->store($this->request('POST', ['nama_kategori' => 'Makanan']));

        // store() mengembalikan `back()` tanpa memvalidasi duplikat aktif;
        // data tidak berubah jadi kategori baru.
        $this->assertDatabaseCount('kategori', 1);
    }

    public function test_store_kategori_nonaktif_duplikat_diaktifkan(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Makanan', 'status' => 'inactive']);

        $this->controller->store($this->request('POST', ['nama_kategori' => 'Makanan']));

        $this->assertDatabaseHas('kategori', [
            'id_kategori' => $kategori->id_kategori,
            'status' => 'active',
        ]);
    }

    public function test_edit_kategori(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Lama', 'status' => 'active']);

        $view = $this->controller->edit($kategori->id_kategori);

        $this->assertSame('kategori.edit', $view->name());
        $this->assertTrue($view->getData()['kategori']->is($kategori));
    }

    public function test_update_kategori(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Lama', 'status' => 'active']);

        $this->controller->update($this->request('PUT', ['nama_kategori' => 'Baru']), $kategori->id_kategori);

        $this->assertDatabaseHas('kategori', [
            'id_kategori' => $kategori->id_kategori,
            'nama_kategori' => 'Baru',
        ]);
    }

    public function test_destroy_sampah_pulihkan_dan_hapus_permanen_kategori(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Sampah', 'status' => 'active']);

        $this->controller->destroy($kategori->id_kategori);
        $this->assertSoftDeleted('kategori', ['id_kategori' => $kategori->id_kategori]);

        $this->controller->pulihkan($kategori->id_kategori);
        $this->assertDatabaseHas('kategori', ['id_kategori' => $kategori->id_kategori, 'deleted_at' => null]);

        $kategori->delete();
        $this->controller->hapusPermanen($kategori->id_kategori);
        $this->assertDatabaseMissing('kategori', ['id_kategori' => $kategori->id_kategori]);
    }

    public function test_sampah_menampilkan_hanya_kategori_terhapus(): void
    {
        $kategori = Kategori::create(['nama_kategori' => 'Lama']);
        $kategori->delete();

        $view = $this->controller->sampah();

        $this->assertSame('kategori.sampah', $view->name());
        $this->assertCount(1, $view->getData()['sampah']);
    }

    public function test_edit_kategori_tidak_ditemukan_melempar_not_found(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->edit(999999);
    }
}
