<?php

namespace ClarionApp\DownloadManagerBackend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ClarionApp\DownloadManagerBackend\Models\TorrentServer;

class TorrentServerController extends Controller
{
    private function getClientClasses()
    {
        $skip = array(".", "..");
        $path = "/../TorrentClients";
        $files = $dir = scandir(__DIR__.$path);
        $clientClasses = [];

        foreach ($files as $file) {
            if(in_array($file, $skip)) continue;
            if(stripos($file, ".php") === false) continue;
            if(stripos($file, ".swp") !== false) continue;
            $file = str_replace(".php", "", $file);

            $className = "ClarionApp\\DownloadManagerBackend\\TorrentClients\\$file";
            

            if ($file !== 'TorrentClientBase') {
                $clientClasses[] = $className;
            }
        }

        return $clientClasses;
    }

    public function getClientTypes()
    {
        $types = array();
        $classes = $this->getClientClasses();
        foreach($classes as $className)
        {
            array_push($types, $className::getType());
        }
        return $types;
    }

    /**
     * Display a listing of torrent servers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $servers = TorrentServer::all();
        return response()->json($servers);
    }

    /**
     * Store a newly created torrent server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string',
            'type' => 'required|string',
        ]);
        
        // Set local_node from config
        $data['local_node'] = config('clarion.node_id');
        
        $server = TorrentServer::create($data);
        return response()->json($server, 201);
    }

    /**
     * Display the specified torrent server.
     *
     * @param  \ClarionApp\DownloadManager\Models\TorrentServer  $torrentServer
     * @return \Illuminate\Http\Response
     */
    public function show(TorrentServer $torrentServer)
    {
        return response()->json($torrentServer);
    }

    /**
     * Update the specified torrent server.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ClarionApp\DownloadManager\Models\TorrentServer  $torrentServer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TorrentServer $torrentServer)
    {
        $data = $request->validate([
            'address' => 'required|string',
            'type' => ['required','string'],
        ]);
        
        // local_node should not be updated, it remains from config
        $torrentServer->update($data);
        return response()->json($torrentServer);
    }

    /**
     * Remove the specified torrent server.
     *
     * @param  \ClarionApp\DownloadManager\Models\TorrentServer  $torrentServer
     * @return \Illuminate\Http\Response
     */
    public function destroy(TorrentServer $torrentServer)
    {
        $torrentServer->delete();
        return response()->json(null, 204);
    }

}
