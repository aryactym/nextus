<?php

declare(strict_types=1);

use App\Domain\Auth\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

/**
 * Sesi CAPTCHA aktif seolah-olah gambar baru dimuat pengguna.
 *
 * @param  string  $code  kode CAPTCHA yang tersimpan di session
 * @return array<string, int|string> data session CAPTCHA
 */
function captchaSession(string $code): array
{
    return [
        'captcha_code' => $code,
        'captcha_expires_at' => now()->addMinutes(10)->getTimestamp(),
    ];
}

it('renders a gd captcha image and stores the code in session', function () {
    $response = $this->get(route('captcha.image'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/png');
    expect(substr($response->getContent(), 0, 4))->toBe("\x89PNG");
    expect(session('captcha_code'))->toBeString();
    expect(session('captcha_code'))->toHaveLength(6);
    expect(session('captcha_expires_at'))->toBeGreaterThan(now()->getTimestamp());
});

it('rejects login with a wrong captcha code', function () {
    $user = User::factory()->create();

    $this->session(captchaSession('ABC123'));

    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => 'ZZZ999',
    ])->assertSessionHasErrors('captcha');

    expect(auth()->check())->toBeFalse();
});

it('logs in with a correct captcha code regardless of letter case', function () {
    $user = User::factory()->create();

    $this->session(captchaSession('ABC123'));

    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => 'abc123',
    ])->assertRedirect(route('admin.dashboard'));

    expect(auth()->check())->toBeTrue();
    // Kode hanya berlaku sekali pakai.
    expect(session('captcha_code'))->toBeNull();
});

it('rejects an expired captcha code', function () {
    $user = User::factory()->create();

    $this->session([
        'captcha_code' => 'ABC123',
        'captcha_expires_at' => now()->subMinute()->getTimestamp(),
    ]);

    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => 'ABC123',
    ])->assertSessionHasErrors('captcha');

    expect(auth()->check())->toBeFalse();
});

it('rejects a reused captcha code', function () {
    $user = User::factory()->create();

    $this->session(captchaSession('ABC123'));

    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'wrong-password',
        'captcha' => 'ABC123',
    ])->assertSessionHasErrors('email');

    // Kode terhapus setelah percobaan pertama meskipun kredensial salah.
    $this->post(route('login.post'), [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => 'ABC123',
    ])->assertSessionHasErrors('captcha');

    expect(auth()->check())->toBeFalse();
});

it('rejects registration with a wrong captcha code', function () {
    Mail::fake();

    $this->session(captchaSession('ABC123'));

    $this->post(route('register.post'), [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
        'captcha' => 'ZZZ999',
        'terms' => '1',
    ])->assertSessionHasErrors('captcha')->assertSessionHasInput('name', 'Budi Santoso');

    $this->assertDatabaseMissing('users', ['email' => 'budi@example.com']);
    Mail::assertNothingSent();
});

it('registers a new user with a correct captcha code', function () {
    Mail::fake();

    $this->session(captchaSession('ABC123'));

    $this->post(route('register.post'), [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
        'captcha' => 'abc123',
        'terms' => '1',
    ])->assertRedirect(route('home'));

    $this->assertDatabaseHas('users', ['email' => 'budi@example.com']);

    // Tanpa global scope: user publik tidak punya organization_id.
    $user = User::withoutGlobalScopes()->where('email', 'budi@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('Learner'))->toBeTrue();
    expect($user->hasRole('Learner'))->toBeTrue();

    Mail::assertSent(WelcomeEmail::class);
    expect(auth()->check())->toBeTrue();
});
