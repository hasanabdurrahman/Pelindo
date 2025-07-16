<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrap();
        // Using Closure based composers...
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
            $view->with(['latestNotifikasi' => $latestNotifikasi, 'allNotifikasi' => $allNotifikasi, 'newNotifications' => $newNotifications]);
        });
        
    }
}
