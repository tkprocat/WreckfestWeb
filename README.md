# WreckfestWeb

> A modern Laravel-based admin panel and web interface for managing Wreckfest dedicated servers

WreckfestWeb provides a powerful, user-friendly interface to configure, monitor, and control your Wreckfest game server through a beautiful web application. Built with Laravel 12, Filament 4, and real-time WebSocket updates.

---

## 🎯 About

WreckfestWeb is a comprehensive management solution for Wreckfest dedicated servers. It communicates with [WreckfestController](https://github.com/tkprocat/WreckfestController), a C# service that manages the actual game server, providing:

- **Real-time Monitoring** - Live player counts, server status, and track rotation
- **Track Management** - Browse 122+ tracks, build custom rotations with drag-and-drop
- **Server Control** - Start, stop, restart your server with one click
- **Event Scheduling** - Schedule future server configurations and track rotations
- **Public Interface** - Share server status with your community
- **AI Assistant** - Get help building track collections with OpenAI integration

Perfect for community server administrators who want professional tools to manage their Wreckfest servers.

---

## ✨ Key Features

### 🌐 Public Homepage
- Real-time server status (online/offline)
- Live player list with bot detection
- Current track rotation display
- Upcoming scheduled events
- Auto-refreshes every 30 seconds

### 🎮 Admin Panel
- **Server Configuration** - Manage all server settings (name, password, max players, ports, game modes, etc.)
- **Track Browser** - Search and filter through all available tracks with weather compatibility
- **Track Rotation** - Build and save custom track rotations with drag-and-drop reordering
- **Track Collections** - Save and load preset track rotations
- **Event Scheduling** - Schedule future server configurations with recurring patterns
- **Server Control** - Start, stop, restart the server with live status updates
- **Server Logs** - View and monitor server logs in real-time
- **Current Players** - See who's playing with real-time updates via WebSockets
- **AI Assistant** - Interactive chat to help build track collections (optional)

### 🔄 Real-time Features
- WebSocket-powered live updates (via Laravel Reverb)
- Automatic player list refresh
- Live server status changes
- No page refresh needed

### 🎨 Modern UI
- Built with Filament 4 and Tailwind CSS
- Responsive design for desktop and mobile
- Dark mode support
- Intuitive drag-and-drop interfaces

---

## 📸 Screenshots

*(Add screenshots here once project is deployed)*

<!-- Example structure:
### Admin Dashboard
![Dashboard](docs/screenshots/dashboard.png)

### Track Rotation Builder
![Track Rotation](docs/screenshots/track-rotation.png)

### Public Server Status
![Public Page](docs/screenshots/public-page.png)
-->

---

## 🚀 Quick Start

Get up and running in minutes with the interactive installer:

```bash
# 1. Clone the repository
git clone https://github.com/tkprocat/WreckfestWeb.git
cd WreckfestWeb

# 2. Install dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Run the installer (interactive setup wizard)
php artisan wreckfest:install
```

The installer will guide you through:
- Database creation
- WebSocket configuration
- Admin user creation
- Asset building
- Optional features (AI assistant, SFTP backups)

**Then start your application:**
```bash
# Terminal 1: Start Laravel
php artisan serve

# Terminal 2: Start WebSocket server
php artisan reverb:start
```

Visit `http://localhost:8000` and click "Login" to access the admin panel!

---

## 📖 Documentation

- **[Full Installation Guide](INSTALL.md)** - Detailed installation instructions with manual setup options
- **[AI Development Guide](CLAUDE_GUIDE.md)** - For AI-assisted development with Claude Code
- **[Spatie Laravel Backup](https://spatie.be/docs/laravel-backup/v8/introduction)** - Backup configuration reference

---

## 🔧 Requirements

### Prerequisites
- **PHP 8.4+** with extensions: SQLite, PDO, mbstring, XML, cURL, OpenSSL
- **Composer 2.x**
- **Node.js 18+** and npm
- **SQLite** (bundled with PHP)

### External Dependencies
- **[WreckfestController](https://github.com/tkprocat/WreckfestController)** - C# service that interfaces with the game server
  - Must be running (default: `https://localhost:5101`)
  - Handles actual server management and API communication

---

## 🛠️ Tech Stack

- **[Laravel 12](https://laravel.com/)** - PHP framework
- **[Filament 4](https://filamentphp.com/)** - Admin panel framework
- **[Livewire 3](https://livewire.laravel.com/)** - Dynamic reactive components
- **[Laravel Reverb](https://reverb.laravel.com/)** - WebSocket server for real-time features
- **[Tailwind CSS](https://tailwindcss.com/)** - Utility-first CSS framework
- **[Alpine.js](https://alpinejs.dev/)** - Lightweight JavaScript framework
- **[SQLite](https://www.sqlite.org/)** - Embedded database
- **[Pest 3](https://pestphp.com/)** - Testing framework

### Optional Integrations
- **[OpenAI API](https://openai.com/)** - For AI track rotation assistant
- **[Spatie Laravel Backup](https://spatie.be/docs/laravel-backup)** - Automated SFTP backups

---

## 🎯 Project Structure

```
app/
├── Console/Commands/        # Artisan commands (including wreckfest:install)
├── Events/                  # Event classes
├── Filament/
│   ├── Pages/              # Admin panel pages
│   ├── Resources/          # Filament resources (Events, etc.)
│   └── Widgets/            # Dashboard widgets
├── Helpers/                # Utility classes
├── Http/Controllers/       # HTTP controllers
├── Jobs/                   # Background jobs
├── Livewire/              # Livewire components
├── Models/                # Eloquent models
├── Observers/             # Model observers
└── Services/              # Service classes (API client, etc.)

config/
└── wreckfest.php          # Game configuration (tracks, weather, etc.)

database/
├── factories/             # Model factories for testing
├── migrations/            # Database migrations
└── seeders/              # Database seeders

resources/
├── js/                   # JavaScript files
└── views/               # Blade templates

tests/
├── Feature/             # Feature tests
└── Unit/               # Unit tests
```

---

## 🤝 Related Projects

- **[WreckfestController](https://github.com/tkprocat/WreckfestController)** - C# service that manages the Wreckfest game server (required)

---

## 🧪 Testing

Run the test suite with Pest:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test --filter EventTest
```

---

## 📝 License

This project is open-source software.

---

## 🙏 Credits

Built with:
- [Laravel](https://laravel.com/) - The PHP Framework for Web Artisans
- [Filament](https://filamentphp.com/) - Beautiful Admin Panel Framework
- [Livewire](https://livewire.laravel.com/) - A full-stack framework for Laravel
- [Tailwind CSS](https://tailwindcss.com/) - A utility-first CSS framework
- [Alpine.js](https://alpinejs.dev/) - Your new, lightweight, JavaScript framework

---

## 💬 Support

For detailed installation instructions, troubleshooting, and configuration options, see the [Full Installation Guide](INSTALL.md).

For AI-assisted development, refer to [CLAUDE_GUIDE.md](CLAUDE_GUIDE.md).

---

**Made with ❤️ for the Wreckfest community**
