<?php

use Illuminate\Support\Facades\Route;
use ClarionApp\DownloadManager\Controllers\TorrentServerController;

Route::group(['middleware'=>['auth:api'], 'prefix'=>$this->routePrefix ], function () {
    Route::apiResource('torrent-servers', TorrentServerController::class);
});
