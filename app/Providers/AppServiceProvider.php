<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Pengaturan;

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
    public function boot()
    {
         view()->composer('*', function ($view) {
            $pengaturan = Pengaturan::first();
            $view->with('namaAplikasi', $pengaturan->nama_aplikasi ?? config('app.name'));
            $view->with('logoAplikasi', $pengaturan->logo ?? null);
        });
    }
}
