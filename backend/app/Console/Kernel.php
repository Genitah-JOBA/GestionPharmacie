<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CorrigerMotsDePasse;

class Kernel extends ConsoleKernel
{
    /**
     * Les commandes Artisan personnalisées de l'application.
     *
     * @var array
     */
    protected $commands = [
        CorrigerMotsDePasse::class,
        \App\Console\Commands\VerifierMedicaments::class,
    ];
    
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('verifier:medicaments')->everyMinute();
        
        $schedule->call(function () {
            \Log::info('✅ La tâche CRON fonctionne bien à ' . now());
        })->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
