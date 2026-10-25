<?php

namespace Tests\Unit\TugasKelompok;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthControllerTest extends BaseTugasTest
{
    private function request(array $params = [], string $method = 'POST'): Request
    {
        $request = Request::create('/', $method, $params);
        $request->setLaravelSession(app('session')->driver());
        $request->setUserResolver(fn () => auth()->user());
        return $request;
    }

    public function test_authenticated_session_create(): void
    {
        $view = app(AuthenticatedSessionController::class)->create();
        $this->assertSame('auth.login', $view->name());
    }

    public function test_authenticated_session_store_invalid_credentials(): void
    {
        $request = \App\Http\Requests\Auth\LoginRequest::create('/', 'POST', ['email' => 'missing@example.com', 'password' => 'wrong']);
        $request->setLaravelSession(app('session')->driver());
        $request->setUserResolver(fn () => null);
        $this->expectException(ValidationException::class);
        app(AuthenticatedSessionController::class)->store($request);
    }

    public function test_authenticated_session_store_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'login-unit@example.com', 'password' => Hash::make('password')]);
        $request = \App\Http\Requests\Auth\LoginRequest::create('/', 'POST', ['email' => $user->email, 'password' => 'password']);
        $request->setLaravelSession(app('session')->driver());
        $request->setUserResolver(fn () => null);

        $response = app(AuthenticatedSessionController::class)->store($request);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertAuthenticatedAs($user);
    }

    public function test_authenticated_session_destroy(): void
    {
        $this->actingAs($this->pemilik);
        $response = app(AuthenticatedSessionController::class)->destroy($this->request());
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertGuest();
    }

    public function test_password_reset_link_create(): void
    {
        $view = app(PasswordResetLinkController::class)->create();
        $this->assertSame('auth.forgot-password', $view->name());
    }

    public function test_password_reset_link_store_missing_email(): void
    {
        $this->expectException(ValidationException::class);
        app(PasswordResetLinkController::class)->store($this->request([]));
    }

    public function test_password_reset_link_store_unknown_email(): void
    {
        $response = app(PasswordResetLinkController::class)->store($this->request(['email' => 'missing@example.com']));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_confirmable_password_show(): void
    {
        $view = app(ConfirmablePasswordController::class)->show();
        $this->assertSame('auth.confirm-password', $view->name());
    }

    public function test_confirmable_password_store_wrong_password(): void
    {
        $this->actingAs($this->pemilik);
        $this->expectException(ValidationException::class);
        app(ConfirmablePasswordController::class)->store($this->request(['password' => 'wrong']));
    }

    public function test_confirmable_password_store_correct_password(): void
    {
        $this->actingAs($this->pemilik);
        $response = app(ConfirmablePasswordController::class)->store($this->request(['password' => 'password']));
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_password_update_wrong_current_password(): void
    {
        $this->actingAs($this->pemilik);
        $request = $this->request(['current_password' => 'wrong', 'password' => 'newpassword', 'password_confirmation' => 'newpassword'], 'PUT');
        $this->expectException(ValidationException::class);
        app(PasswordController::class)->update($request);
    }

    public function test_new_password_create(): void
    {
        $view = app(NewPasswordController::class)->create($this->request(['token' => 'token']));
        $this->assertSame('auth.reset-password', $view->name());
    }

    public function test_new_password_store_invalid_token(): void
    {
        User::factory()->create(['email' => 'reset-unit@example.com']);
        $request = $this->request(['token' => 'wrong', 'email' => 'reset-unit@example.com', 'password' => 'password123', 'password_confirmation' => 'password123']);
        $response = app(NewPasswordController::class)->store($request);
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }
}
