<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Ajoute ici les événements si besoin
    ];

    public function boot(): void
    {
        //
    }
}
