<?php
namespace YourVendor\YourPackage;

use Illuminate\Support\ServiceProvider;

class YourPackageNameServiceProvider extends ServiceProvider
{
    public function register()
    {
        // ثبت مسیرها، کانفیگ‌ها یا دستورات
        $this->mergeConfigFrom(__DIR__.'/../config/otp-arya.php', 'otp-arya');
    }

    public function boot()
    {
        // ثبت فایل‌های مایگریشن
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');

        // ثبت فایل‌های route
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // انتشار تنظیمات یا فایل‌ها
        $this->publishes([
            __DIR__.'/../config/otp-arya.php' => config_path('otp-arya.php'),
        ], 'config');
    }
}
