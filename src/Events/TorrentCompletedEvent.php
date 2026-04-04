<?php

namespace ClarionApp\DownloadManagerBackend\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TorrentCompletedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $torrent_id;
    public ?string $name;
    public ?string $hash_string;
    public string $completed_at;
    public string $user_id;

    public function __construct(
        string $torrent_id,
        ?string $name,
        ?string $hash_string,
        string $completed_at,
        string $user_id
    ) {
        $this->torrent_id = $torrent_id;
        $this->name = $name;
        $this->hash_string = $hash_string;
        $this->completed_at = $completed_at;
        $this->user_id = $user_id;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("User.{$this->user_id}")
        ];
    }
}
