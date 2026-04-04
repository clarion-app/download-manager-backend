<?php

namespace ClarionApp\DownloadManagerBackend\Tests\Events;

use Tests\TestCase;
use ClarionApp\DownloadManagerBackend\Events\TorrentCompletedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class TorrentCompletedEventTest extends TestCase
{
    /** @test */
    public function it_implements_should_broadcast_now()
    {
        $event = new TorrentCompletedEvent(
            torrent_id: 'test-uuid',
            name: 'Test File.mkv',
            hash_string: 'abc12345',
            completed_at: '2026-04-04 12:00:00',
            user_id: 'user-uuid'
        );

        $this->assertInstanceOf(ShouldBroadcastNow::class, $event);
    }

    /** @test */
    public function it_broadcasts_on_private_user_channel()
    {
        $event = new TorrentCompletedEvent(
            torrent_id: 'test-uuid',
            name: 'Test File.mkv',
            hash_string: 'abc12345',
            completed_at: '2026-04-04 12:00:00',
            user_id: 'user-123'
        );

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertInstanceOf(PrivateChannel::class, $channels[0]);
        $this->assertEquals('private-User.user-123', $channels[0]->name);
    }

    /** @test */
    public function it_carries_correct_payload()
    {
        $event = new TorrentCompletedEvent(
            torrent_id: 'torrent-uuid',
            name: 'Movie.mp4',
            hash_string: 'deadbeef1234',
            completed_at: '2026-04-04 15:30:00',
            user_id: 'user-456'
        );

        $this->assertEquals('torrent-uuid', $event->torrent_id);
        $this->assertEquals('Movie.mp4', $event->name);
        $this->assertEquals('deadbeef1234', $event->hash_string);
        $this->assertEquals('2026-04-04 15:30:00', $event->completed_at);
    }

    /** @test */
    public function it_handles_null_name_and_hash()
    {
        $event = new TorrentCompletedEvent(
            torrent_id: 'torrent-uuid',
            name: null,
            hash_string: null,
            completed_at: '2026-04-04 15:30:00',
            user_id: 'user-789'
        );

        $this->assertNull($event->name);
        $this->assertNull($event->hash_string);
    }
}
