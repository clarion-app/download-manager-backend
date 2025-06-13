<?php

namespace ClarionApp\DownloadManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ClarionApp\EloquentMultiChainBridge\EloquentMultiChainBridge;

class TorrentServer extends Model
{
    use EloquentMultiChainBridge, SoftDeletes;

    protected $table = 'torrent_servers';

    protected $fillable = [
        'local_node',
        'address',
        'type',
    ];
}
