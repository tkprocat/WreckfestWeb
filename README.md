# Wreckfest Server Admin Panel

A Laravel + Filament admin panel for managing your Wreckfest game server via API.

## Features

### Public Homepage
- **Real-time Server Status** - Public-facing page showing:
  - Live server status (online/offline)
  - Current player count and list
  - Server information
  - Auto-refreshes every 30 seconds
  - Direct link to admin login
  - Beautiful gradient UI with Tailwind CSS

### Admin Panel

- **Server Configuration** - Manage all server settings including:
  - Basic server information (name, password, max players)
  - Network settings (ports, LAN mode)
  - Game settings (session mode, bots, AI difficulty)
  - Track and race settings with intelligent filtering
  - Advanced settings (admin controls, restrictions)

- **Track Browser** - Powerful track discovery tool:
  - Search tracks by name, variant, or ID
  - Filter by game mode compatibility
  - Filter by track type (Derby/Racing)
  - Filter by weather support
  - Sortable columns (location, variant, variant ID, derby flag)
  - Live search with debouncing
  - View all 122+ track variants across 47 locations

- **Track Rotation** - Configure your server's track rotation with:
  - Track and gamemode selection with smart compatibility
  - Laps, bots, and team configuration
  - Car restrictions and weather settings
  - Reorderable track list
  - Save/load track collections
  - Randomize track order
  - Deploy directly to server

- **Server Control** - Manage your server:
  - View real-time server status
  - Start, stop, and restart server
  - One-click controls with status updates

- **Server Logs** - Monitor your server:
  - View server log files
  - Configurable number of lines
  - Real-time refresh

- **Current Players** - See who's playing:
  - List of currently connected players
  - Bot detection with visual indicators
  - Auto-refresh functionality

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Wreckfest server with API running on `https://localhost:5101`

### Setup Steps

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Configure Environment**

   Update the `.env` file with your Wreckfest API URL:
   ```env
   WRECKFEST_API_URL=https://localhost:5101/api
   ```

3. **Generate Application Key** (if not already done)
   ```bash
   php artisan key:generate
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Create Admin User**
   ```bash
   php artisan make:filament-user
   ```

   Follow the prompts to create your admin user account.

6. **Start the Development Server**
   ```bash
   php artisan serve
   ```

7. **Access the Admin Panel**

   Open your browser and navigate to:
   ```
   http://localhost:8000/admin
   ```

   Login with the credentials you created in step 5.

## Configuration

### API URL

The Wreckfest API URL can be configured in the `.env` file:

```env
WRECKFEST_API_URL=https://localhost:5101/api
```

If your Wreckfest API is running on a different host or port, update this value accordingly.

### SSL Certificate Verification

The API client is configured to skip SSL verification for localhost development. If you're using this in production with a valid SSL certificate, you should modify the `WreckfestApiClient` class to enable SSL verification.

## Usage

### Public Homepage

Visit the homepage to see:
- Real-time server status
- Current players online with their information
- Player count (e.g., 5/24 players)
- Auto-refresh every 30 seconds
- "Admin Login" button to access the admin panel

**URL:** `https://wreckfestweb.test/` or `http://localhost:8000/`

### Server Configuration

1. Navigate to **Server Config** in the admin panel
2. Update any settings you want to change
3. Click **Save Configuration**
4. The changes will be sent to your Wreckfest server API

### Track Rotation

1. Navigate to **Track Rotation** in the admin panel
2. Add tracks using the **Add Track** button
3. Configure each track's settings (gamemode, laps, bots, etc.)
4. Reorder tracks by dragging them
5. Click **Save Track Rotation**

### Server Control

1. Navigate to **Server Control** in the admin panel
2. View the current server status
3. Use the control buttons to:
   - Start the server
   - Stop the server
   - Restart the server
4. Click **Refresh Status** to update the status display

### Monitoring

- **Server Logs**: View recent server log entries, adjust the number of lines displayed
- **Current Players**: See who's currently connected to your server

## Troubleshooting

### Can't connect to API

- Ensure your Wreckfest API is running at the configured URL
- Check the `WRECKFEST_API_URL` in your `.env` file
- Verify network connectivity to the API server

### Authentication Issues

- Make sure you've created an admin user using `php artisan make:filament-user`
- Clear your browser cache and cookies
- Try logging out and back in

### Configuration Not Saving

- Check the Laravel logs at `storage/logs/laravel.log`
- Verify the API is accepting PUT/POST requests
- Ensure the API returns successful responses

## Development

### File Structure

```
app/
├── Filament/
│   ├── Pages/              # Filament admin pages
│   │   ├── ServerConfig.php
│   │   ├── TrackRotation.php
│   │   ├── TrackBrowser.php
│   │   ├── ServerControl.php
│   │   ├── ServerLogs.php
│   │   └── CurrentPlayers.php
│   └── Widgets/
│       └── CurrentPlayersWidget.php
├── Helpers/
│   └── TrackHelper.php     # Track compatibility utilities
└── Services/
    └── WreckfestApiClient.php  # API client for Wreckfest server

config/
└── wreckfest.php           # Game configuration (tracks, modes, etc.)

resources/
└── views/
    └── filament/
        └── pages/          # Blade views for Filament pages
```

### Track Compatibility System

The application includes an intelligent track compatibility system:

- **Hierarchical Track Structure**: Tracks are organized by location with multiple variants
- **Derby/Racing Separation**: Automatic filtering of tracks based on game mode
- **Weather Restrictions**: Tracks have specific weather support (e.g., Sandstone Raceway only supports clear weather)
- **Smart Filtering**: Track selectors automatically show only compatible options
- **Helper Methods**: `TrackHelper` class provides utilities for track/gamemode/weather compatibility checks

### Adding New Features

To add new features:

1. Update `WreckfestApiClient` with new API methods
2. Create a new Filament page in `app/Filament/Pages/`
3. Create the corresponding view in `resources/views/filament/pages/`

## License

This project is open-source software.

## Credits

Built with:
- [Laravel](https://laravel.com/) - PHP Framework
- [Filament](https://filamentphp.com/) - Admin Panel
- [Livewire](https://livewire.laravel.com/) - Dynamic Components
