<?php

namespace ClarionApp\DownloadManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ClarionApp\DownloadManager\Models\TorrentServer;

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

            $className = "ClarionApp\\DownloadManager\\TorrentClients\\$file";
            

            if ($file !== 'TorrentClientBase') {
                $clientClasses[] = $className;
            }
        }

        return $clientClasses;
    }

    private function getClientTypes()
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
        $clientTypes = $this->getClientTypes();
        return response()->json([
            "servers" => $servers,
            "clientTypes"=>$clientTypes
        ]);
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
            'local_node' => 'required|string',
            'address' => 'required|string',
            'type' => 'required|string',
        ]);
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
            'local_node' => 'required|string',
            'address' => 'required|string',
            'type' => ['required','string'],
        ]);
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
