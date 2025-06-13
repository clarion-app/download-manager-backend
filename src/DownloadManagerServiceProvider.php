<?php

namespace ClarionApp\DownloadManager;

use ClarionApp\Backend\ClarionPackageServiceProvider;

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
    }
}
