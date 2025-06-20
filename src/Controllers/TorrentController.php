<?php

namespace ClarionApp\DownloadManagerBackend\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use ClarionApp\DownloadManagerBackend\Models\Torrent;
use ClarionApp\DownloadManagerBackend\Models\TorrentServer;
use Auth;

class TorrentController extends Controller
{
    /**
     * Display a listing of torrents.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $torrents = Torrent::all();
        return response()->json($torrents);
    }

    /**
     * Store a newly created torrent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'server_id' => 'nullable|uuid',
            'magnetURI' => 'required|string',
            'hash_string' => 'nullable|string',
            'name' => 'nullable|string',
        ]);
        
        // Set local_node from config
        $data['local_node'] = config('clarion.node_id');
        $data['user_id'] = Auth::user() ? Auth::user()->id : null;
        if(!isset($data['server_id'])) {
            $data['server_id'] = TorrentServer::where('local_node', $data['local_node'])->first()->id;
        }
        
        if(substr($data['magnetURI'], 0, 6) === "magnet")
        {
            $temp = explode("?", $data['magnetURI']);
            $vars = explode("&", $temp[1]);
            foreach($vars as $var)
            {
                $parts = explode("=", $var);
                if($parts[0] == "dn") $data['name'] = urldecode($parts[1]);
            }
        }

        $torrent = Torrent::create($data);
        \Artisan::call('torrent:check');
        return response()->json($torrent, 201);
    }

    /**
     * Display the specified torrent.
     *
     * @param  \ClarionApp\DownloadManager\Models\Torrent  $torrent
     * @return \Illuminate\Http\Response
     */
    public function show(Torrent $torrent)
    {
        return response()->json($torrent);
    }

    /**
     * Update the specified torrent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \ClarionApp\DownloadManager\Models\Torrent  $torrent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Torrent $torrent)
    {
        $data = $request->validate([
            'server_id' => 'required|uuid',
            'user_id' => 'nullable|uuid',
            'magnetURI' => 'required|string',
            'hash_string' => 'nullable|string',
            'name' => 'nullable|string',
            'completed_at' => 'nullable|date',
        ]);
        
        // local_node should not be updated
        $torrent->update($data);
        return response()->json($torrent);
    }

    /**
     * Mark the specified torrent as incomplete.
     *
     * @param  \ClarionApp\DownloadManager\Models\Torrent  $torrent
     * @return \Illuminate\Http\Response
     */
    public function markIncomplete($id)
    {
        $torrent = Torrent::findOrFail($id);
        $torrent->update(['completed_at' => null, 'hash_string' => null]);
        return response()->json($torrent);
    }

    /**
     * Remove the specified torrent.
     *
     * @param  \ClarionApp\DownloadManager\Models\Torrent  $torrent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $torrent = Torrent::findOrFail($id);
        $torrent->delete();
        return response()->json(null, 204);
    }
}
