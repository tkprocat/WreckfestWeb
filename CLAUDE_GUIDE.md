# WreckfestWeb Project Guide

Complete reference for the WreckfestWeb Laravel application - for Claude Code and developers.

## Project Overview

**WreckfestWeb** is a Laravel-based admin panel and public-facing web interface for managing a Wreckfest game server. It integrates with a C# WreckfestController service that manages the actual game server process.

**Locations:**
- Laravel App: `F:\Projects\Web\WreckfestWeb` (this project)
- C# Controller: `F:\Projects\C#\WreckfestController`
- Shared Claude Guides: `F:\claude\` (for cross-project documentation)

---

## Tech Stack

### Core Framework
- **PHP**: 8.4.13
- **Laravel**: 12.34.0
- **Database**: SQLite

### Major Packages & Versions

| Package | Version | Purpose | Guide |
|---------|---------|---------|-------|
| **Filament** | 4.1.9 | Admin panel framework | [FILAMENT4_GUIDE.md](F:\claude\FILAMENT4_GUIDE.md) |
| **Livewire** | 3.6.4 | Real-time frontend components | - |
| **Laravel Reverb** | 1.6.0 | WebSocket server for broadcasting | - |
| **Laravel Sanctum** | 4.2.0 | API authentication | - |
| **Laravel MCP** | 0.3.2 | Model Context Protocol integration | [LARAVEL_BOOST_GUIDE.md](F:\claude\LARAVEL_BOOST_GUIDE.md) |
| **Pest** | 3.8.4 | Testing framework | [PEST4_GUIDE.md](F:\claude\PEST4_GUIDE.md) |
| **PHPUnit** | 11.5.33 | Testing foundation | - |
| **Laravel Pint** | 1.25.1 | Code style fixer | - |
| **Rector** | 2.2.3 | PHP upgrader | - |
| **Tailwind CSS** | 4.1.14 | CSS framework | - |
| **Laravel Echo** | 2.2.4 | WebSocket client | - |

### Frontend Stack
- **Blade** templates for public pages
- **Livewire** for reactive components
- **Alpine.js** (via Filament/Livewire)
- **Tailwind CSS** for styling
- **Laravel Echo** + **Pusher/Reverb** for real-time updates

---

## Architecture

### System Flow

```
┌─────────────────────┐
│   Laravel Web App   │
│  (WreckfestWeb)     │
│                     │
│  - Filament Admin   │
│  - Livewire UI      │
│  - API Endpoints    │
└──────────┬──────────┘
           │
           │ HTTP REST API
           │
┌──────────▼──────────┐
│  C# Controller      │
│ (WreckfestController)│
│                     │
│  - Server Manager   │
│  - Config Handler   │
│  - Event Scheduler  │
└──────────┬──────────┘
           │
           │ Process Management
           │
┌──────────▼──────────┐
│   Wreckfest         │
│   Game Server       │
└─────────────────────┘

           │
           │ Webhooks
           │
┌──────────▼──────────┐
│   Laravel App       │
│  (Real-time events) │
└──────────┬──────────┘
           │
           │ WebSockets (Reverb)
           │
┌──────────▼──────────┐
│   Frontend Clients  │
│   (Browser)         │
└─────────────────────┘
```

### Key Integration Points

1. **Laravel → C# API**
   - `WreckfestApiClient` service
   - Base URL: `https://localhost:5101/api`
   - Endpoints: Server control, Config management, Track rotation, Events

2. **C# → Laravel Webhooks**
   - Player updates: `POST /api/webhooks/players-updated`
   - Track changes: `POST /api/webhooks/track-changed`
   - Event activation: `POST /api/webhooks/event-activated`

3. **Laravel → Frontend**
   - Laravel Echo broadcasts to `server-updates` channel
   - Events: `players.updated`, `track.changed`, `event.activated`

---

## Database Models

### Core Models

- **User** - Admin users with Filament access
- **Invitation** - Invitation system for new users
- **Event** - Scheduled server events *(NEW)*
- **TrackCollection** - Saved track rotation configurations
- **Track** - Track locations (e.g., "Madman Stadium")
- **TrackVariant** - Specific track layouts (e.g., "Figure 8")
- **Tag** - Track categorization (Oval, Tarmac, Stadium, etc.)
- **TrackMetadata** - Legacy metadata storage (being phased out)

### Relationships

```
User
├── hasMany: Events (created)
└── belongsTo: Invitation (invited_by)

Event
├── belongsTo: TrackCollection
└── belongsTo: User (creator)

TrackCollection
└── hasMany: Events

Track
└── hasMany: TrackVariants

TrackVariant
├── belongsTo: Track
└── belongsToMany: Tags

Tag
└── belongsToMany: TrackVariants
```

---

## Features

### Admin Panel (Filament)

**Server Management:**
- Server Config - Set server name, ports, game settings
- Track Rotation - Build and deploy track rotations
- Server Control - Start/stop/restart server
- Track Browser - Browse available tracks with filtering
- Manage Collections - Save/load track rotation presets
- **Events** - Schedule future server configurations *(NEW)*

**Content Management:**
- Tags - Manage track categorization
- Users - User management (hidden from nav by default)

**Real-time Monitoring:**
- Dashboard with current players widget
- Server status indicators
- Live player list

### Public Pages

- **Home Page** - Server status, current players, track rotation
- Real-time updates via WebSockets
- Responsive design

### API

**Public Webhooks** (from C# Controller):

**Player & Game State:**
- `/api/webhooks/players-updated` - Player list changes
- `/api/webhooks/track-changed` - Track change notifications
- `/api/webhooks/event-activated` - Event activation

**Server Lifecycle (NEW):**
- `/api/webhooks/server-started` - Server successfully started
- `/api/webhooks/server-stopped` - Server stopped (graceful or force)
- `/api/webhooks/server-restarted` - Server restarted (command or full)
- `/api/webhooks/server-attached` - Controller attached to running server
- `/api/webhooks/server-restart-pending` - Smart restart countdown in progress

**Webhook Details:**

All webhooks broadcast events via Laravel Reverb to the `server-updates` channel for real-time UI updates.

**Server Lifecycle Webhook Payloads:**

```php
// server-started
{
    "processId": 12345,
    "processName": "Wreckfest_x64",
    "startTime": "2025-01-15T20:30:00Z",
    "timestamp": "2025-01-15T20:30:02Z"
}
// Broadcasts: server.started

// server-stopped
{
    "processId": 12345,
    "stopMethod": "Graceful", // or "Force"
    "timestamp": "2025-01-15T21:00:00Z"
}
// Broadcasts: server.stopped

// server-restarted
{
    "oldProcessId": 12345,
    "newProcessId": 12567,
    "restartMethod": "Command", // or "Full"
    "timestamp": "2025-01-15T21:05:00Z"
}
// Broadcasts: server.restarted

// server-attached
{
    "processId": 15234,
    "processName": "Wreckfest_x64",
    "startTime": "2025-01-15T18:00:00Z",
    "timestamp": "2025-01-15T20:45:00Z"
}
// Broadcasts: server.attached

// server-restart-pending
{
    "minutesRemaining": 3,
    "eventName": "Weekend Derby Event",
    "eventId": 42,
    "scheduledRestartTime": "2025-01-15T21:10:00Z",
    "timestamp": "2025-01-15T21:07:00Z"
}
// Broadcasts: server.restart-pending
```

---

## Configuration

### Environment Variables

Key `.env` settings:

```bash
# Application
APP_NAME=WreckfestWeb
APP_ENV=production
APP_URL=https://yoursite.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/database.sqlite

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Wreckfest API
WRECKFEST_API_URL=https://localhost:5101/api
```

### Services

**Laravel Reverb (WebSocket Server):**
```bash
php artisan reverb:start
```

**Queue Worker** (if using queues):
```bash
php artisan queue:work
```

---

## Development Workflow

### Getting Started

```bash
# Clone and install
git clone <repo>
cd WreckfestWeb
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate

# Create admin user
php artisan make:filament-user

# Build assets
npm run build

# Start services
php artisan serve
php artisan reverb:start
```

### Running Tests

```bash
# Run all tests
./vendor/bin/pest

# Run with coverage
./vendor/bin/pest --coverage

# Run specific test
./vendor/bin/pest tests/Feature/EventTest.php

# See F:\claude\PEST4_GUIDE.md for more options
```

### Code Style

```bash
# Fix code style
./vendor/bin/pint

# Upgrade PHP code
./vendor/bin/rector process
```

---

## MCP Integration

This project uses **Laravel Boost MCP** for enhanced development tools.

### Available MCP Tools

- `application-info` - Get versions, packages, models
- `database-schema` - View database structure
- `database-query` - Run read-only queries
- `list-routes` - View all routes
- `list-artisan-commands` - View Artisan commands
- `search-docs` - Search Laravel ecosystem documentation
- `tinker` - Execute PHP code in Laravel context
- `read-log-entries` - View Laravel logs
- `browser-logs` - View frontend console logs
- `last-error` - Get last backend error
- `get-config` - Get config values
- `get-absolute-url` - Generate URLs

See [F:\claude\LARAVEL_BOOST_GUIDE.md](F:\claude\LARAVEL_BOOST_GUIDE.md) for usage details.

### Wreckfest MCP Tools

Additional MCP server for track management:

- `list-tracks` - Get all tracks with metadata
- `list-tags` - Get all track tags
- `filter-tracks-by-tags` - Find tracks by tags
- `get-track-collections` - View collections
- `create-track-collection` - Create new collection
- `update-track-collection` - Update collection tracks
- And many more...

---

## Common Tasks

### Adding a New Model

```bash
# Create model with migration
php artisan make:model Product -m

# Create Filament resource
php artisan make:filament-resource Product

# Run migration
php artisan migrate
```

### Adding a New Livewire Component

```bash
# Create component
php artisan make:livewire ComponentName

# Use in Blade
<livewire:component-name />
```

### Deploying Track Rotation

```php
use App\Services\WreckfestApiClient;

$api = app(WreckfestApiClient::class);
$api->updateTracks($tracks, $collectionName);
```

### Creating an Event *(NEW)*

Via Filament:
1. Navigate to Events in admin panel
2. Click "Create"
3. Fill in event details, select track rotation
4. Set start time and server config
5. Save - automatically pushed to C# controller

---

## Troubleshooting

### WebSocket Connection Issues

```bash
# Restart Reverb
php artisan reverb:restart

# Check REVERB_ env vars in .env
# Check frontend Echo configuration
```

### C# API Not Responding

- Ensure C# WreckfestController service is running
- Check `WRECKFEST_API_URL` in `.env`
- Verify SSL certificate (using `withoutVerifying()` in dev)

### Filament Not Loading

```bash
# Clear Filament cache
php artisan filament:clear-cache

# Rebuild assets
npm run build

# Clear all caches
php artisan optimize:clear
```

### Database Issues

```bash
# Reset database
php artisan migrate:fresh --seed

# Check migrations
php artisan migrate:status
```

---

## File Structure

```
WreckfestWeb/
├── app/
│   ├── Filament/
│   │   ├── Pages/          # Custom Filament pages
│   │   │   ├── ServerConfig.php
│   │   │   ├── ServerControl.php
│   │   │   ├── TrackRotation.php
│   │   │   └── TrackBrowser.php
│   │   └── Resources/      # Filament resources
│   │       ├── EventResource.php
│   │       ├── UserResource.php
│   │       └── TagResource.php
│   ├── Events/             # Laravel events
│   │   ├── EventActivated.php
│   │   ├── PlayersUpdated.php
│   │   ├── ServerAttached.php
│   │   ├── ServerRestartPending.php
│   │   ├── ServerRestarted.php
│   │   ├── ServerStarted.php
│   │   ├── ServerStopped.php
│   │   └── TrackChanged.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   └── WebhookController.php
│   │   └── Livewire/       # Livewire components
│   │       ├── ServerName.php
│   │       ├── ServerStatus.php
│   │       ├── PlayerList.php
│   │       └── TrackRotation.php
│   ├── Models/             # Eloquent models
│   ├── Observers/          # Model observers
│   │   └── EventObserver.php
│   └── Services/           # Business logic
│       ├── EventService.php
│       └── WreckfestApiClient.php
├── database/
│   ├── migrations/
│   └── database.sqlite
├── resources/
│   ├── views/
│   │   ├── home.blade.php
│   │   ├── livewire/       # Livewire views
│   │   └── filament/       # Filament views
│   └── js/
│       └── app.js          # Echo configuration
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes (webhooks)
│   └── channels.php        # Broadcasting channels
└── tests/
    ├── Feature/
    └── Unit/
```

---

## External Resources

### Official Documentation
- **Laravel**: https://laravel.com/docs/12.x
- **Filament**: https://filamentphp.com/docs
- **Livewire**: https://livewire.laravel.com
- **Pest**: https://pestphp.com

### Project Guides (F:\claude)
- **Filament 4 Guide**: [F:\claude\FILAMENT4_GUIDE.md](F:\claude\FILAMENT4_GUIDE.md)
- **Pest 4 Guide**: [F:\claude\PEST4_GUIDE.md](F:\claude\PEST4_GUIDE.md)
- **Laravel Boost MCP Guide**: [F:\claude\LARAVEL_BOOST_GUIDE.md](F:\claude\LARAVEL_BOOST_GUIDE.md)

---

**Last Updated**: January 2025
**Project Version**: 1.0.0

**Recent Features**:
- ✅ Events system for scheduling server configurations
- ✅ Smart restart with player-friendly timing
- ✅ Event activation notifications
- ✅ Recurring event support
- ✅ Server lifecycle webhooks (started, stopped, restarted, attached, restart-pending)
