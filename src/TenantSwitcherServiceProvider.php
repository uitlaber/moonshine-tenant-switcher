<?php

namespace Atlon\MoonShineTenantSwitcher;

use Atlon\MoonShineTenantSwitcher\Http\Controllers\SwitchTenantController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TenantSwitcherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tenant-switcher.php', 'tenant-switcher');

        $this->app->singleton(TenantManager::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tenant-switcher');

        $this->publishes([
            __DIR__.'/../config/tenant-switcher.php' => config_path('tenant-switcher.php'),
        ], 'tenant-switcher-config');

        $this->registerRoutes();
    }

    /**
     * Роут переключения тенанта — под тем же prefix/domain/middleware и
     * auth-guard, что и админка MoonShine. Имя: moonshine.tenant-switch.
     */
    private function registerRoutes(): void
    {
        $group = array_filter([
            'domain' => config('moonshine.domain') ?: null,
            'prefix' => config('moonshine.prefix', 'admin'),
            'middleware' => array_merge(['moonshine'], (array) config('moonshine.auth.middleware', [])),
            'as' => 'moonshine.',
        ], static fn ($value) => $value !== null);

        Route::group($group, static function (): void {
            Route::post('tenant/switch', SwitchTenantController::class)->name('tenant-switch');
        });
    }
}
