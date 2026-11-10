<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Settings\Models\Setting;
use App\Domain\Settings\Models\Sponsor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL; // 1. Tambahkan import URL facade
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Midtrans\Config;
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
        // 2. Paksa HTTPS jika aplikasi berjalan di environment production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::before(function ($user) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('enroll', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        $this->configureMailFromSettings();
        $this->configureMidtransFromSettings();

        View::composer('components.layouts.public', function ($view): void {
            try {
                $view->with([
                    'googleAnalyticsId' => Setting::get('google_analytics_id'),
                    'googleSearchConsole' => Setting::get('google_search_console'),
                    'facebookPixelId' => Setting::get('facebook_pixel_id'),
                    'favicon' => Setting::get('favicon'),
                ]);
            } catch (Throwable) {
                $view->with([
                    'googleAnalyticsId' => null,
                    'googleSearchConsole' => null,
                    'facebookPixelId' => null,
                    'favicon' => null,
                ]);
            }
        });

        try {
            View::share('sponsors', Sponsor::active()->get());
        } catch (Throwable) {
            View::share('sponsors', collect());
        }

        View::addLocation(base_path('docs'));
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

    /**
     * Terapkan konfigurasi Midtrans dari tabel settings bila tersedia.
     */
    private function configureMidtransFromSettings(): void
    {
        try {
            $serverKey = Setting::getSecret('midtrans_server_key');

            if ($serverKey === null || $serverKey === '') {
                return;
            }

            Config::$serverKey = $serverKey;
            Config::$isProduction = Setting::get('midtrans_is_production') === '1';
            Config::$isSanitized = true;
            Config::$is3ds = true;
        } catch (Throwable) {
            // Tabel settings belum tersedia.
        }
    }
}
