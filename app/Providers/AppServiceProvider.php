<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        // Đảm bảo toàn bộ asset (ảnh logo, css, js) sinh ra chuẩn HTTPS khi chạy trên Production/Hosting
        if (config('app.env') === 'production' ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
