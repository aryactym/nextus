<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class SupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        require_once __DIR__.'/../Support/Helpers/helpers.php';
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $view->with('appName', config('app.name'));
            $view->with('appLocale', app()->getLocale());
        });

        Blade::directive('money', function ($expression) {
            return "<?php echo currency_idr((float) {$expression}); ?>";
        });
    }
}
