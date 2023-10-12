<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('telescope:prune --hours=48')->daily();

        // Limpar os backups antigos
        $schedule->command('backup:clean --only-to-disk=backups')->daily()->at('01:00')
            ->environments(['production']);

        // backup diário do banco de dados
        $schedule->command('backup:run --only-to-disk=backups --only-db')->daily()->at('01:30')
            ->environments(['production']);
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
