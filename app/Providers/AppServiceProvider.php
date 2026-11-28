<?php

namespace App\Providers;

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
        // Admin uploads (e.g. the home page video) can be up to 100 MB and take up to 15 minutes
        // on a slow connection. Each upload field still sets its own smaller limit and file types.
        config([
            'livewire.temporary_file_upload.rules' => ['required', 'file', 'max:102400'],
            'livewire.temporary_file_upload.max_upload_time' => 15,
        ]);

        // Phone, WhatsApp, email and address as set in the admin (Site settings).
        \App\Support\ContactDetails::apply();
    }
}
