<?php

namespace ClarionApp\DownloadManagerBackend\Commands;

use Illuminate\Console\Command;
use ClarionApp\DownloadManagerBackend\Models\Torrent;
use ClarionApp\DownloadManagerBackend\Models\TorrentServer;

class CheckTorrent extends Command
{
    private $skip = array(".", "..", "TorrentClientBase.php");

    private function getClasses()
    {
        $classes = array();
        $dir = scandir(__DIR__."/../TorrentClients");
        foreach($dir as $file)
        {
            if(in_array($file, $this->skip)) continue;
            if(stripos($file, ".php") === false) continue;
            if(stripos($file, ".swp") !== false) continue;

            $classname = "ClarionApp\\DownloadManagerBackend\\TorrentClients\\".str_replace(".php", "", $file);
            array_push($classes, $classname);
        }
        return $classes;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'torrent:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process waiting torrent jobs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $torrents = Torrent::whereNull('completed_at')->get();
        if(count($torrents) == 0) return;

        $local_node = config('clarion.node_id');
        $daemons = TorrentServer::where('local_node', $local_node)->get();
        $daemon = $daemons[rand(0, count($daemons) - 1)];

        $torrentd = null;

        foreach($this->getClasses() as $classname)
        {
            if($classname::getType() == $daemon->type)
            {
                $torrentd = new $classname($daemon->address);
                break;
            }
        }

        if($torrentd == null) throw new \Exception('Could not find an interface for: '.$daemon->type);

        foreach($torrents as $torrent)
        {
            if($torrent->hash_string)
            {
                // Already added
                $check = $torrentd->check($torrent->hash_string);
                if($check)
                {
                    $torrentd->remove($torrent->hash_string);
                    $torrent->completed_at = date("Y-m-d H:i:s");
                    $torrent->save();
                }
            }
            else
            {
                $torrent->hash_string = $torrentd->add($torrent->magnetURI);
                $torrent->save();
            }
        }
    }
}
