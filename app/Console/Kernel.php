<?php

namespace App\Console;

use App\Console\Commands\SyncInventoryLookupWarehouse;
use App\Console\Commands\UpdateInventoryStatus;
use App\Console\Commands\UpdateReceivingStatus;
use App\Console\Commands\UpdateWanrratyStatus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Đăng ký các lệnh Artisan của bạn.
     */
    protected $commands = [
        SyncInventoryLookupWarehouse::class,
        UpdateInventoryStatus::class,
        UpdateWanrratyStatus::class,
        UpdateReceivingStatus::class,
    ];

    /**
     * Định nghĩa lịch trình cho các lệnh Artisan.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inventory:update-storage')->hourly();
        $schedule->command('warranty:update-storage')->daily();
        $schedule->command('receiving:update-status')->hourly();
    }

    /**
     * Đăng ký các lệnh của ứng dụng.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
