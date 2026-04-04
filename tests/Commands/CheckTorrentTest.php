<?php

namespace ClarionApp\DownloadManagerBackend\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Event;
use ClarionApp\DownloadManagerBackend\Events\TorrentCompletedEvent;

class CheckTorrentTest extends TestCase
{
    /** @test */
    public function event_is_constructed_correctly_from_torrent_data()
    {
        // Simulates what CheckTorrent::handle() does when a torrent completes:
        // it constructs a TorrentCompletedEvent with torrent model properties
        $torrentId = 'torrent-uuid';
        $name = 'TestFile.mkv';
        $hashString = 'abc12345deadbeef';
        $completedAt = '2026-04-04 12:00:00';
        $userId = 'test-user-id';

        $event = new TorrentCompletedEvent(
            torrent_id: $torrentId,
            name: $name,
            hash_string: $hashString,
            completed_at: $completedAt,
            user_id: $userId
        );

        $this->assertEquals($torrentId, $event->torrent_id);
        $this->assertEquals($name, $event->name);
        $this->assertEquals($hashString, $event->hash_string);
        $this->assertEquals($completedAt, $event->completed_at);
        $this->assertEquals($userId, $event->user_id);
    }

    /** @test */
    public function event_carries_null_name_when_torrent_has_no_name()
    {
        $event = new TorrentCompletedEvent(
            torrent_id: 'torrent-uuid',
            name: null,
            hash_string: 'abc12345deadbeef',
            completed_at: '2026-04-04 12:00:00',
            user_id: 'test-user-id'
        );

        $this->assertNull($event->name);
        $this->assertEquals('abc12345deadbeef', $event->hash_string);
    }
}
