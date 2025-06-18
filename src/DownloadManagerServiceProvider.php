<?php

namespace ClarionApp\DownloadManagerBackend;

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
    }

    public function register(): void
    {
        parent::register();
        $this->commands([
            CheckTorrent::class,
        ]);
    }
}
