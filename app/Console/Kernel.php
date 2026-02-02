<?php

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
        // 每天 00:00 同步上架商品到 Elasticsearch
        $schedule->command('es:sync-items')
            ->dailyAt('00:00')
            // ->everyMinute()   // 測試用每分鐘
            ->withoutOverlapping()  // 避免重複執行
            ->onOneServer()         // 多伺服器環境下只在一台執行
            ->runInBackground();    // 背景執行
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
