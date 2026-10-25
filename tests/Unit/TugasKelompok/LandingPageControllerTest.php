<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\LandingPageController;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class LandingPageControllerTest extends BaseTugasTest
{
    private LandingPageController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->pemilik);
        $this->controller = app(LandingPageController::class);
    }

    private function request(array $params = [], array $files = []): Request
    {
        $request = Request::create('/', 'POST', $params, [], $files);
        $request->setUserResolver(fn () => $this->pemilik);
        return $request;
    }

    public function test_index_membuat_data_default_dan_menampilkan_view(): void
    {
        $view = $this->controller->index();

        $this->assertSame('Pengaturan.landing', $view->name());
        $this->assertNotNull($view->getData()['data']);
        $this->assertDatabaseHas('landing_pages', ['judul_h1' => 'Solusi Belanja']);
    }

    public function test_index_memakai_data_existing(): void
    {
        $page = LandingPage::create(['judul_h1' => 'Existing', 'judul_highlight' => 'Highlight']);

        $view = $this->controller->index();

        $this->assertTrue($view->getData()['data']->is($page));
        $this->assertSame(1, LandingPage::count());
    }

    public function test_update_landing_page_text(): void
    {
        LandingPage::create(['judul_h1' => 'Awal', 'judul_highlight' => 'Test']);

        $response = $this->controller->update($this->request([
            'login_title' => 'Judul Baru',
            'login_bg_color' => '#ffffff',
        ]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertDatabaseHas('landing_pages', ['login_title' => 'Judul Baru', 'login_bg_color' => '#ffffff']);
    }

    public function test_update_landing_page_file(): void
    {
        Storage::fake('public');
        LandingPage::create(['judul_h1' => 'Awal', 'judul_highlight' => 'Test']);

        $file = UploadedFile::fake()->create('logo.png', 100, 'image/png');
        $response = $this->controller->update($this->request([], ['login_logo' => $file]));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $path = LandingPage::first()->login_logo_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_update_menolak_color_terlalu_panjang(): void
    {
        $this->expectException(ValidationException::class);

        $this->controller->update($this->request(['login_bg_color' => str_repeat('x', 11)]));
    }
}
