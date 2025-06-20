<?php

namespace ClarionApp\DownloadManagerBackend\TorrentClients;

abstract class TorrentClientBase
{
    abstract public function add($torrent);
    abstract public function check($hashString);
    abstract public function remove($hashString);

    public static function getType()
    {
        return static::$type;
    }
}
