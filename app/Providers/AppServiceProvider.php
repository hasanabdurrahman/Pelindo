<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;   


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
    /*
     |------------------------------------------------------------------
     | Paksa skema HTTPS bila aplikasi berjalan di domain ngrok
     |------------------------------------------------------------------
     */
    if (str_contains(config('app.url'), 'ngrok-free.app')) {
        URL::forceScheme('https');
    }

    // --- kode lama kamu tetap di bawah sini ---
    Paginator::useBootstrap();

    View::composer(['includes.navbar'], function ($view) {
        $latestNotifikasi = Notifikasi::latest()
            ->take(3)
            ->where('receiver', auth()->user()->id)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->get();

        $allNotifikasi = Notifikasi::where('receiver', auth()->user()->id)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->latest()
            ->get();

        $newNotifications = Notifikasi::where('receiver', auth()->user()->id)
            ->where('status_read', 0)
            ->get();

        $view->with([
            'latestNotifikasi' => $latestNotifikasi,
            'allNotifikasi'    => $allNotifikasi,
            'newNotifications' => $newNotifications,
        ]);
    });
}

    
}

