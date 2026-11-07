<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Settings\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        $this->configureMailFromSettings();

        View::composer('components.layouts.public', function ($view): void {
            try {
                $view->with([
                    'googleAnalyticsId' => Setting::get('google_analytics_id'),
                    'googleSearchConsole' => Setting::get('google_search_console'),
                    'facebookPixelId' => Setting::get('facebook_pixel_id'),
                ]);
            } catch (Throwable) {
                $view->with([
                    'googleAnalyticsId' => null,
                    'googleSearchConsole' => null,
                    'facebookPixelId' => null,
                ]);
            }
        });
    }

    /**
     * Terapkan konfigurasi SMTP dari tabel settings bila tersedia.
     */
    private function configureMailFromSettings(): void
    {
        try {
            $host = Setting::get('mail_host');

            if ($host === null || $host === '') {
                return;
            }

            $scheme = Setting::get('mail_encryption', 'tls') === 'ssl' ? 'smtps' : 'smtp';

            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.scheme' => $scheme,
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => (int) Setting::get('mail_port', (string) config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.username' => Setting::get('mail_username'),
                'mail.mailers.smtp.password' => Setting::getSecret('mail_password'),
                'mail.from.address' => Setting::get('mail_from_address', (string) config('mail.from.address')),
                'mail.from.name' => Setting::get('mail_from_name', (string) config('mail.from.name')),
            ]);
        } catch (Throwable) {
            // Tabel settings belum tersedia (mis. saat migrasi awal).
        }
    }
}
