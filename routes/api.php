<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use ClarionApp\DownloadManagerBackend\Controllers\TorrentServerController;
use ClarionApp\DownloadManagerBackend\Controllers\TorrentController;

Route::group(['middleware'=>['auth:api'], 'prefix'=>$this->routePrefix ], function () {
    Route::get('torrent-servers/client-types', [TorrentServerController::class, 'getClientTypes']);
    Route::apiResource('torrent-servers', TorrentServerController::class);
    
    // Torrent routes except store
    Route::apiResource('torrents', TorrentController::class); //->except(['store']);
    
    // Additional torrent actions
    Route::patch('torrents/{torrent}/mark-incomplete', [TorrentController::class, 'markIncomplete']);
});

Broadcast::channel('User.{id}', function ($user, $id) {
    return $user->id === $id;
});