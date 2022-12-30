<?php

namespace App\Console;

use App\Http\Controllers\AgeRv\VoalleSalesController;
use App\Http\Controllers\DataWarehouse\Voalle\AuthenticationContractsController;
use App\Http\Controllers\DataWarehouse\Voalle\ContractsController;
use App\Http\Controllers\DataWarehouse\Voalle\ContractsTypeController;
use App\Http\Controllers\DataWarehouse\Voalle\PeoplesController;
use App\Http\Controllers\DataWarehouse\Voalle\ServiceProductsController;
use App\Http\Controllers\TestController;
use App\Models\AgeRv\VoalleSales;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(new AuthenticationContractsController())->everyMinute();
        $schedule->call(new ContractsController())->everyMinute();
        $schedule->call(new ContractsTypeController())->everyMinute();
        $schedule->call(new PeoplesController())->everyMinute();
        $schedule->call(new ServiceProductsController())->everyMinute();
        $schedule->call(new VoalleSalesController())->everyMinute();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
