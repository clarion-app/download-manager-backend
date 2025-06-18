<?php

namespace ClarionApp\DownloadManagerBackend\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ClarionApp\EloquentMultiChainBridge\EloquentMultiChainBridge;

class Torrent extends Model
{
    use EloquentMultiChainBridge, SoftDeletes;

    protected $table = 'torrents';

    protected $fillable = [
        'local_node',
        'server_id',
        'user_id',
        'magnetURI',
        'hash_string',
        'completed_at',
        'name',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
