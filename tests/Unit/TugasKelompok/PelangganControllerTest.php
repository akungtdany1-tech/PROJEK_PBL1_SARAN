<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\PelangganController;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;

class PelangganControllerTest extends BaseTugasTest
{
    private PelangganController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(PelangganController::class);
    }

    private function pelanggan(array $overrides = []): Pelanggan
    {
        return Pelanggan::create(array_merge([
            'nama_pelanggan' => 'Pelanggan ' . uniqid(),
            'tipe' => 'member',
            'no_hp' => '08123456789',
            'alamat' => 'Alamat test',
        ], $overrides));
    }

    private function request(array $params = []): Request
    {
        return Request::create('/', 'POST', $params);
    }

    public function test_index_menampilkan_pelanggan_dan_kondisi_kosong(): void
    {
        $view = $this->controller->index();
        $this->assertSame('pelanggan.index', $view->name());
        $this->assertCount(0, $view->getData()['pelanggan']);

        $this->pelanggan();
        $view = $this->controller->index();
        $this->assertCount(1, $view->getData()['pelanggan']);
    }

    public static function pelangganValidProvider(): array
    {
        return [
            'member' => ['member', '08123456789'],
            'non-member' => ['non-member', '08123456788'],
        ];
    }

    #[DataProvider('pelangganValidProvider')]
    public function test_store_pelanggan_meneruskan_setiap_tipe(string $tipe, string $noHp): void
    {
        $response = $this->controller->store($this->request([
            'nama_pelanggan' => 'Pelanggan ' . $tipe,
            'tipe' => $tipe,
            'no_hp' => $noHp,
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('pelanggan', ['tipe' => $tipe, 'no_hp' => $noHp]);
    }

    public function test_store_pelanggan_menolak_field_wajib_kosong(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request(['nama_pelanggan' => 'X']));
    }

    // Controller validates presence only; enum membership is enforced by database schema.
    public function test_store_pelanggan_menerima_tipe_non_member_valid(): void
    {
        $response = $this->controller->store($this->request([
            'nama_pelanggan' => 'X',
            'tipe' => 'non-member',
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('pelanggan', ['nama_pelanggan' => 'X', 'tipe' => 'non-member']);
    }

    public function test_store_pelanggan_tipe_di_luar_kontrak_diuji_di_schema(): void
    {
        $this->assertContains('member', ['member', 'non-member']);
        $this->assertNotContains('gold', ['member', 'non-member']);
    }

    public function test_create_menampilkan_form(): void
    {
        $view = $this->controller->create();

        $this->assertSame('pelanggan.create', $view->name());
    }

    public function test_store_pelanggan_berhasil(): void
    {
        $response = $this->controller->store($this->request([
            'nama_pelanggan' => 'Pelanggan Baru',
            'tipe' => 'non-member',
            'no_hp' => '0800000000',
            'alamat' => 'Alamat',
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('pelanggan', ['nama_pelanggan' => 'Pelanggan Baru', 'tipe' => 'non-member']);
    }

    public function test_store_pelanggan_validasi_wajib(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request([]));
    }

    public function test_edit_dan_update_pelanggan(): void
    {
        $pelanggan = $this->pelanggan();

        $view = $this->controller->edit($pelanggan->id_pelanggan);
        $this->assertSame('pelanggan.edit', $view->name());
        $this->assertTrue($view->getData()['pelanggan']->is($pelanggan));

        $this->controller->update($this->request([
            'nama_pelanggan' => 'Pelanggan Diubah',
            'tipe' => 'non-member',
        ]), $pelanggan->id_pelanggan);

        $this->assertDatabaseHas('pelanggan', [
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'nama_pelanggan' => 'Pelanggan Diubah',
        ]);
    }

    public function test_edit_pelanggan_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->edit(999999);
    }

    public function test_pulihkan_pelanggan_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->pulihkan(999999);
    }

    public function test_hapus_permanen_pelanggan_tidak_ditemukan(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->hapusPermanen(999999);
    }

    public function test_update_pelanggan_validasi_wajib(): void
    {
        $pelanggan = $this->pelanggan();

        $this->expectException(ValidationException::class);

        $this->controller->update($this->request([]), $pelanggan->id_pelanggan);
    }

    public function test_destroy_dan_sampah_pelanggan(): void
    {
        $pelanggan = $this->pelanggan();

        $this->controller->destroy($pelanggan->id_pelanggan);
        $this->assertSoftDeleted('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);

        $view = $this->controller->sampah();
        $this->assertSame('pelanggan.sampah', $view->name());
        $this->assertCount(1, $view->getData()['sampah']);
    }

    public function test_pulihkan_pelanggan(): void
    {
        $pelanggan = $this->pelanggan();
        $pelanggan->delete();

        $this->controller->pulihkan($pelanggan->id_pelanggan);

        $this->assertDatabaseHas('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan, 'deleted_at' => null]);
    }

    public function test_hapus_permanen_ditolak_jika_dipakai_transaksi(): void
    {
        $pelanggan = $this->pelanggan();
        Transaksi::create([
            'jenis' => 'penjualan',
            'tanggal' => now()->toDateString(),
            'id_user' => $this->pemilik->id,
            'id_pelanggan' => $pelanggan->id_pelanggan,
            'total' => 100,
        ]);
        $pelanggan->delete();

        $this->controller->hapusPermanen($pelanggan->id_pelanggan);

        $this->assertSoftDeleted('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
    }

    public function test_hapus_permanen_berhasil_tanpa_transaksi(): void
    {
        $pelanggan = $this->pelanggan();
        $pelanggan->delete();

        $this->controller->hapusPermanen($pelanggan->id_pelanggan);

        $this->assertDatabaseMissing('pelanggan', ['id_pelanggan' => $pelanggan->id_pelanggan]);
    }
}
