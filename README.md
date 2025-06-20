# Download Manager Backend

A Laravel-based backend service for managing downloads in Clarion. This package provides comprehensive torrent management capabilities with support for multiple torrent client implementations.

## Overview

The Download Manager Backend is a Clarion app component that enables users to:
- Manage torrent downloads across multiple servers
- Track download status and completion
- Support multiple torrent client types (currently Transmission)

## Features

- **Multi-Server Support**: Manage torrents across multiple torrent servers and client types
- **Torrent Lifecycle Management**: Add, monitor, and remove torrents with automatic status tracking
- **RESTful API**: Complete CRUD operations for torrents and torrent servers
- **Extensible Client Architecture**: Plugin-based system for supporting different torrent clients
- **User Association**: Link torrents to specific users with authentication support
- **Automatic Processing**: Background commands for checking torrent status

## Architecture

### Models

#### Torrent
Represents individual torrent downloads with the following attributes:
- `local_node`: Node identifier for distributed tracking
- `server_id`: Reference to the torrent server handling the download
- `user_id`: Associated user (when authenticated)
- `magnetURI`: Magnet link for the torrent
- `hash_string`: Unique torrent hash identifier
- `name`: Display name (auto-extracted from magnet URI)
- `completed_at`: Timestamp when download completed

#### TorrentServer
Represents torrent client servers with:
- `local_node`: Node identifier
- `address`: Server connection address
- `type`: Client type (e.g., "Transmission")

### Controllers

#### TorrentController
- `index()`: List all torrents
- `store()`: Add new torrent from magnet URI
- `show()`: Get specific torrent details
- `update()`: Modify torrent properties
- `markIncomplete()`: Reset completion status
- `destroy()`: Remove torrent

#### TorrentServerController
- Standard CRUD operations for torrent servers
- `getClientTypes()`: List supported client types

### Torrent Clients

#### TransmissionClient
Implementation for Transmission torrent client with:
- RPC communication via HTTP
- Session management with X-Transmission-Session-Id headers
- Methods for adding, checking status, and removing torrents

#### TorrentClientBase
Abstract base class for implementing additional torrent clients

### Commands

#### CheckTorrent (`torrent:check`)
Background command that:
- Scans for incomplete torrents
- Checks status with respective torrent clients
- Updates completion timestamps
- Processes torrent lifecycle events

## API Endpoints

All endpoints require authentication (`auth:api` middleware) and use the configured route prefix.

### Torrent Servers
- `GET /torrent-servers/client-types` - Get supported client types
- `GET /torrent-servers` - List all servers
- `POST /torrent-servers` - Create new server
- `GET /torrent-servers/{id}` - Get server details
- `PUT /torrent-servers/{id}` - Update server
- `DELETE /torrent-servers/{id}` - Remove server

### Torrents
- `GET /torrents` - List all torrents
- `POST /torrents` - Add new torrent
- `GET /torrents/{id}` - Get torrent details
- `PUT /torrents/{id}` - Update torrent
- `DELETE /torrents/{id}` - Remove torrent
- `PATCH /torrents/{id}/mark-incomplete` - Mark as incomplete

## Database Schema

### Torrents Table
```sql
- id (UUID, primary key)
- local_node (string)
- server_id (UUID, foreign key)
- user_id (UUID, nullable)
- magnetURI (text)
- hash_string (string, nullable)
- name (string, nullable)
- completed_at (timestamp, nullable)
- created_at/updated_at (timestamps)
- deleted_at (soft deletes)
```

### Torrent Servers Table
```sql
- id (UUID, primary key)
- local_node (string)
- address (string)
- type (string)
- created_at/updated_at (timestamps)
- deleted_at (soft deletes)
```

## Usage Examples

### Adding a Torrent
```php
POST /api/torrents
{
    "magnetURI": "magnet:?xt=urn:btih:...",
    "server_id": "uuid-of-server" // optional, uses default if not specified
}
```

### Checking Torrent Status
The system automatically processes torrents via the `torrent:check` command, which can be run manually or scheduled:

```bash
php artisan torrent:check
```

### Adding a New Torrent Server
```php
POST /api/torrent-servers
{
    "address": "192.168.1.100:9091",
    "type": "Transmission"
}
```

## Extending Client Support

To add support for new torrent clients:

1. Create a new class extending `TorrentClientBase`
2. Implement required methods: `add()`, `check()`, `remove()`
3. Set the static `$type` property
4. Place in `src/TorrentClients/` directory

Example:
```php
class QBittorrentClient extends TorrentClientBase
{
    public static $type = "qBittorrent";
    
    public function add($torrent) { /* implementation */ }
    public function check($hashString) { /* implementation */ }
    public function remove($hashString) { /* implementation */ }
}
```