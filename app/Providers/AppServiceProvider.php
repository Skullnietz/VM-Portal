<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Fix for session_start(): ps_files_cleanup_dir permission error
        $path = storage_path('framework/sessions');
        if (!file_exists($path)) {
            @mkdir($path, 0755, true);
        }
        if (!app()->runningInConsole()) {
            ini_set('session.save_path', $path);
            ini_set('session.gc_probability', 0);
        }
    }
}
