<?php

namespace App\Console;

use App\Services\JwtTokenService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Jadwalkan refresh token tepat menjelang expired (2 hari)
        $schedule->command('system:refresh-token')
                 ->dailyAt('00:00') 
                 ->when(function () {
                     // Periksa apakah token akan expire dalam 1-2 hari ke depan
                     $jwtTokenService = app(JwtTokenService::class);
                     return $jwtTokenService->isTokenNearExpiration();
                 });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}