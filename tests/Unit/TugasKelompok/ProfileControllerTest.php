<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\ProfileController;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileControllerTest extends BaseTugasTest
{
    private ProfileController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(ProfileController::class);
    }

    private function request(array $params = []): Request
    {
        $request = Request::create('/', 'PATCH', $params);
        $request->setUserResolver(fn () => $this->pemilik);
        $request->setLaravelSession(app('session')->driver());
        return $request;
    }

    public function test_edit_profile_menampilkan_user(): void
    {
        $view = $this->controller->edit($this->request());

        $this->assertSame('profile.edit', $view->name());
        $this->assertTrue($view->getData()['user']->is($this->pemilik));
    }

    public function test_update_profile_dan_reset_verifikasi_email(): void
    {
        $this->pemilik->forceFill(['email_verified_at' => now()])->save();
        $request = $this->request(['name' => 'Nama Update', 'email' => 'profileupdate@example.com']);
        $request->setMethod('PATCH');

        $request = ProfileUpdateRequest::createFrom($request);
        $request->setUserResolver(fn () => $this->pemilik);
        $request->setLaravelSession(app('session')->driver());
        $request->setContainer(app());
        $request->validateResolved();

        $this->controller->update($request);

        $this->assertDatabaseHas('users', [
            'id' => $this->pemilik->id,
            'name' => 'Nama Update',
            'email_verified_at' => null,
        ]);
    }

    public function test_destroy_profile_password_salah_ditolak(): void
    {
        $request = Request::create('/', 'DELETE', ['password' => 'wrong-password']);
        $request->setUserResolver(fn () => $this->pemilik);
        $request->setLaravelSession(app('session')->driver());

        $this->expectException(ValidationException::class);
        $this->controller->destroy($request);
    }

    public function test_destroy_profile_password_benar_menghapus_akun(): void
    {
        $request = Request::create('/', 'DELETE', ['password' => 'password']);
        $request->setUserResolver(fn () => $this->pemilik);
        $request->setLaravelSession(app('session')->driver());
        $id = $this->pemilik->id;

        $this->controller->destroy($request);

        $this->assertGuest();
        $this->assertSoftDeleted('users', ['id' => $id]);
    }
}
