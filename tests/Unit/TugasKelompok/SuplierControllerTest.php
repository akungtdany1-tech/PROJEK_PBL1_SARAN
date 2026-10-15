<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\SuplierController;
use App\Models\Suplier;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;

class SuplierControllerTest extends BaseTugasTest
{
    private SuplierController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(SuplierController::class);
    }

    private function suplier(array $overrides = []): Suplier
    {
        return Suplier::create(array_merge([
            'nama_suplier' => 'Suplier ' . uniqid(),
            'no_hp' => '08123456789',
            'alamat' => 'Alamat',
        ], $overrides));
    }

    private function request(string $method = 'POST', array $params = []): Request
    {
        return Request::create('/', $method, $params);
    }

    public function test_index_menampilkan_data_dan_kosong(): void
    {
        $view = $this->controller->index();
        $this->assertCount(0, $view->getData()['supliers']);

        $this->suplier();
        $view = $this->controller->index();
        $this->assertCount(1, $view->getData()['supliers']);
    }

    public static function phoneProvider(): array
    {
        return [
            'zero prefix' => ['08123456780'],
            'country prefix' => ['6281234567890'],
        ];
    }

    #[DataProvider('phoneProvider')]
    public function test_store_menerima_format_nomor_valid(string $phone): void
    {
        $response = $this->controller->store($this->request('POST', [
            'nama_suplier' => 'Vendor',
            'no_hp' => $phone,
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('suplier', ['no_hp' => $phone]);
    }

    public function test_store_menolak_plus_country_prefix_karena_digits_rule(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_suplier' => 'Vendor',
            'no_hp' => '+6281234567890',
        ]));
    }

    public function test_store_validasi_wajib_format_duplicate_dan_batas(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', []));
    }

    public function test_store_validasi_format_nomor_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_suplier' => 'Vendor',
            'no_hp' => '123',
        ]));
    }

    public function test_store_validasi_nama_terlalu_panjang(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_suplier' => str_repeat('A', 256),
            'no_hp' => '08123456780',
        ]));
    }

    public function test_store_menolak_nomor_duplicate(): void
    {
        $this->suplier(['no_hp' => '08123456781']);

        $this->expectException(ValidationException::class);

        $this->controller->store($this->request('POST', [
            'nama_suplier' => 'Duplikat',
            'no_hp' => '08123456781',
        ]));
    }

    public function test_edit_update_valid_dan_missing(): void
    {
        $suplier = $this->suplier();

        $view = $this->controller->edit($suplier->id_suplier);
        $this->assertSame('Pengaturan.suplier-edit', $view->name());
        $this->assertTrue($view->getData()['suplier']->is($suplier));

        $this->controller->update($this->request('PUT', [
            'nama_suplier' => 'Updated',
            'no_hp' => '08123456788',
            'alamat' => 'Baru',
        ]), $suplier->id_suplier);

        $this->assertDatabaseHas('suplier', [
            'id_suplier' => $suplier->id_suplier,
            'nama_suplier' => 'Updated',
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller->edit(999999);
    }

    public function test_update_validasi_duplicate_format_required_dan_self_allowed(): void
    {
        $one = $this->suplier(['no_hp' => '08123456786']);
        $two = $this->suplier(['no_hp' => '08123456785']);

        $this->controller->update($this->request('PUT', [
            'nama_suplier' => 'X',
            'no_hp' => $two->no_hp,
        ]), $two->id_suplier);

        $this->assertDatabaseHas('suplier', [
            'id_suplier' => $two->id_suplier,
            'no_hp' => $two->no_hp,
        ]);
    }

    public function test_update_menolak_nomor_duplicate_record_lain(): void
    {
        $one = $this->suplier(['no_hp' => '08123456784']);
        $two = $this->suplier(['no_hp' => '08123456783']);

        $this->expectException(ValidationException::class);

        $this->controller->update($this->request('PUT', [
            'nama_suplier' => 'X',
            'no_hp' => $one->no_hp,
        ]), $two->id_suplier);
    }

    public function test_update_menolak_format_nomor_salah(): void
    {
        $suplier = $this->suplier();

        $this->expectException(ValidationException::class);

        $this->controller->update($this->request('PUT', [
            'nama_suplier' => 'X',
            'no_hp' => 'bad',
        ]), $suplier->id_suplier);
    }

    public function test_destroy_sampah_restore_dan_force_delete(): void
    {
        $suplier = $this->suplier();

        $this->controller->destroy($suplier->id_suplier);
        $this->assertSoftDeleted('suplier', ['id_suplier' => $suplier->id_suplier]);

        $view = $this->controller->sampah();
        $this->assertSame('Pengaturan.suplier-sampah', $view->name());
        $this->assertCount(1, $view->getData()['sampah']);

        $this->controller->pulihkan($suplier->id_suplier);
        $this->assertDatabaseHas('suplier', ['id_suplier' => $suplier->id_suplier, 'deleted_at' => null]);

        $suplier->delete();
        $this->controller->hapusPermanen($suplier->id_suplier);
        $this->assertDatabaseMissing('suplier', ['id_suplier' => $suplier->id_suplier]);
    }

    public function test_hapus_permanen_ditolak_jika_dipakai_transaksi(): void
    {
        $suplier = $this->suplier();
        Transaksi::create([
            'jenis' => 'pembelian',
            'tanggal' => now()->toDateString(),
            'id_user' => $this->pemilik->id,
            'id_suplier' => $suplier->id_suplier,
            'total' => 100,
        ]);
        $suplier->delete();

        $response = $this->controller->hapusPermanen($suplier->id_suplier);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSoftDeleted('suplier', ['id_suplier' => $suplier->id_suplier]);
    }

    public function test_missing_delete_restore_force_dan_guest_role_denied(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->destroy(999999);
    }

    public function test_restore_missing_melempar_model_not_found(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->pulihkan(999999);
    }

    public function test_hapus_permanen_missing_melempar_model_not_found(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->controller->hapusPermanen(999999);
    }

    public function test_sampah_only_contains_deleted_records(): void
    {
        $deleted = $this->suplier();
        $deleted->delete();
        $this->suplier(['no_hp' => '08123456782']);

        $view = $this->controller->sampah();

        $this->assertCount(1, $view->getData()['sampah']);
        $this->assertSame($deleted->id_suplier, $view->getData()['sampah']->first()->id_suplier);
    }

    public function test_soft_delete_model_contract(): void
    {
        $suplier = $this->suplier();
        $id = $suplier->id_suplier;

        $suplier->delete();
        $this->assertNull(Suplier::find($id));
        $this->assertNotNull(Suplier::withTrashed()->find($id));

        $suplier->restore();
        $this->assertNotNull(Suplier::find($id));

        $suplier->forceDelete();
        $this->assertNull(Suplier::withTrashed()->find($id));
    }

    public function test_model_schema_contract(): void
    {
        $model = new Suplier;

        $this->assertSame('suplier', $model->getTable());
        $this->assertSame('id_suplier', $model->getKeyName());
        $this->assertEqualsCanonicalizing(['nama_suplier', 'no_hp', 'alamat'], $model->getFillable());
        $this->assertTrue($model->usesTimestamps());
    }
}
