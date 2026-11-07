<?php

declare(strict_types=1);

use App\Domain\Organization\Models\Organization;
use App\Domain\Settings\Mail\TestEmail;
use App\Domain\Settings\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function makeSettingsUser(string $slug): User
{
    $organization = Organization::create([
        'name' => 'Org '.$slug,
        'slug' => 'org-'.$slug,
    ]);
    Session::put('organization_id', $organization->id);

    return User::factory()->create(['organization_id' => $organization->id]);
}

function settingsManager(): User
{
    $user = makeSettingsUser('pengaturan');
    $user->givePermissionTo(Permission::firstOrCreate([
        'name' => 'manage settings',
        'guard_name' => 'web',
    ]));

    return $user;
}

it('shows the settings page to an authorized admin', function () {
    $this->actingAs(settingsManager())
        ->get(route('admin.settings.edit'))
        ->assertOk()
        ->assertSee('Google Analytics')
        ->assertSee('Search Console')
        ->assertSee('Facebook Pixel')
        ->assertSee('SMTP');
});

it('stores tracking and smtp settings', function () {
    $this->actingAs(settingsManager())
        ->put(route('admin.settings.update'), [
            'google_analytics_id' => 'G-ABC1234567',
            'google_search_console' => 'gsc-verification-token',
            'facebook_pixel_id' => '123456789012345',
            'mail_host' => 'smtp.example.com',
            'mail_port' => '587',
            'mail_username' => 'mailer@example.com',
            'mail_password' => 'secret-api-key',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'halo@example.com',
            'mail_from_name' => 'NextUs',
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('success');

    expect(Setting::get('google_analytics_id'))->toBe('G-ABC1234567')
        ->and(Setting::get('google_search_console'))->toBe('gsc-verification-token')
        ->and(Setting::get('facebook_pixel_id'))->toBe('123456789012345')
        ->and(Setting::get('mail_host'))->toBe('smtp.example.com')
        ->and(Setting::get('mail_encryption'))->toBe('tls')
        ->and(Setting::getSecret('mail_password'))->toBe('secret-api-key');

    $storedPassword = DB::table('settings')->where('key', 'mail_password')->value('value');
    expect($storedPassword)->not->toBe('secret-api-key');
});

it('renders tracking scripts on public pages', function () {
    Setting::put('google_analytics_id', 'G-ABC1234567');
    Setting::put('google_search_console', 'gsc-verification-token');
    Setting::put('facebook_pixel_id', '123456789012345');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('google-site-verification', false)
        ->assertSee('gsc-verification-token', false)
        ->assertSee('G-ABC1234567', false)
        ->assertSee('fbevents.js', false)
        ->assertSee('123456789012345', false);
});

it('can clear the stored smtp password', function () {
    Setting::putSecret('mail_password', 'secret-api-key');

    $this->actingAs(settingsManager())
        ->put(route('admin.settings.update'), [
            'mail_host' => 'smtp.example.com',
            'clear_mail_password' => '1',
        ])
        ->assertRedirect(route('admin.settings.edit'));

    expect(Setting::get('mail_password'))->toBeNull();
});

it('forbids settings management without permission', function () {
    $this->actingAs(makeSettingsUser('biasa'))
        ->get(route('admin.settings.edit'))
        ->assertForbidden();
});

it('sends a test email to the requested address', function () {
    Mail::fake();

    $this->actingAs(settingsManager())
        ->post(route('admin.settings.test-email'), ['email' => 'uji@example.com'])
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(TestEmail::class, function (TestEmail $mail): bool {
        return $mail->hasTo('uji@example.com');
    });
});
