<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\PengaturanController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PengaturanControllerTest extends BaseTugasTest
{
    private PengaturanController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(PengaturanController::class);
    }

    private function request(array $params = []): Request
    {
        $request = Request::create('/', 'POST', $params);
        $request->setUserResolver(fn () => auth()->user());
        $request->setLaravelSession(app('session')->driver());
        return $request;
    }

    public function test_pemilik_index(): void
    {
        $view = $this->controller->pemilikIndex();
        $this->assertSame('Pengaturan.pemilik', $view->name());
        $this->assertTrue($view->getData()['user']->is($this->pemilik));
    }

    public function test_pemilik_update_berhasil_dan_password_opsional(): void
    {
        $request = $this->request([
            'name' => 'Pemilik Baru',
            'email' => 'ownernew@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response = $this->controller->pemilikUpdate($request);

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('users', ['id' => $this->pemilik->id, 'name' => 'Pemilik Baru', 'email' => 'ownernew@gmail.com']);
        $this->assertTrue(Hash::check('password123', $this->pemilik->fresh()->password));
    }

    public function test_pemilik_update_validasi_email_dan_name(): void
    {
        $this->expectException(ValidationException::class);
        $this->controller->pemilikUpdate($this->request(['name' => '', 'email' => 'bad@gmail.com']));
    }

    public function test_kasir_list_store_edit_update_destroy(): void
    {
        $view = $this->controller->kasir();
        $this->assertSame('Pengaturan.kasir', $view->name());

        $this->controller->kasirStore($this->request([
            'name' => 'Kasir Baru',
            'email' => 'cashiernew@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]));
        $kasir = User::where('email', 'cashiernew@gmail.com')->firstOrFail();

        $edit = $this->controller->kasirEdit($kasir->id);
        $this->assertSame('Pengaturan.kasir-edit', $edit->name());

        $this->controller->kasirUpdate($this->request(['name' => 'Kasir Update', 'email' => 'cashierupdate@gmail.com']), $kasir->id);
        $this->assertDatabaseHas('users', ['id' => $kasir->id, 'name' => 'Kasir Update']);

        $this->controller->kasirDestroy($kasir->id);
        $this->assertSoftDeleted('users', ['id' => $kasir->id]);
    }

    public function test_kasir_store_validasi(): void
    {
        $this->expectException(ValidationException::class);
        $this->controller->kasirStore($this->request([]));
    }

    public function test_kasir_edit_missing_ditolak(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller->kasirEdit(999999);
    }

    public function test_kasir_update_missing_ditolak(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller->kasirUpdate($this->request(['name' => 'X', 'email' => 'x@gmail.com']), 999999);
    }

    public function test_kasir_destroy_missing_ditolak(): void
    {
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->controller->kasirDestroy(999999);
    }
}
