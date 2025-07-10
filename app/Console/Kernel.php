<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\StorageLinkCommand::class,
        \App\Console\Commands\TestEmailCommand::class,
        \App\Console\Commands\TestPasswordChangeCommand::class,
        \App\Console\Commands\TestLoginCommand::class,
        \App\Console\Commands\ResetPasswordStatusCommand::class,
        \App\Console\Commands\ShowUserInfoCommand::class,
        \App\Console\Commands\GeneratePasswordResetUrlCommand::class,
        \App\Console\Commands\TestPostulanteEmailCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
