<?php 

namespace App\Console;

use App\Console\Commands\TransferFiles;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        TransferFiles::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Menjadwalkan perintah files:transfer untuk dijalankan setiap hari pada jam 3 pagi
        $schedule->command('files:transfer')->dailyAt('03:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}