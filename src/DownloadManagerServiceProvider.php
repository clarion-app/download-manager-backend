<?php

namespace ClarionApp\DownloadManagerBackend;

use Illuminate\Console\Scheduling\Schedule;
use ClarionApp\Backend\ClarionPackageServiceProvider;
use ClarionApp\DownloadManagerBackend\Commands\CheckTorrent;

class DownloadManagerServiceProvider extends ClarionPackageServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if(!$this->app->routesAreCached())
        {
            require __DIR__.'/../routes/api.php';
        }

        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->command('torrent:check')->everyMinute();
        });
    }

    public function register(): void
    {
        parent::register();
        $this->commands([
            CheckTorrent::class,
        ]);
    }
}
