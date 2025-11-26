# WreckfestWeb Installation Guide

Complete installation guide for setting up WreckfestWeb, a Laravel-based admin panel and web interface for managing Wreckfest dedicated servers.

---

## Table of Contents

- [Prerequisites](#prerequisites)
- [System Requirements](#system-requirements)
- [Installation Steps](#installation-steps)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [Optional Features](#optional-features)
- [Troubleshooting](#troubleshooting)
- [Next Steps](#next-steps)

---

## Prerequisites

Before installing WreckfestWeb, ensure you have the following installed:

### Required Software

1. **PHP 8.4+**
   - Required extensions: SQLite, PDO, mbstring, XML, cURL, JSON, OpenSSL
   - Check with: `php -v` and `php -m`

2. **Composer 2.x**
   - Dependency manager for PHP
   - Download from: https://getcomposer.org/

3. **Node.js 18+ and npm**
   - For building frontend assets
   - Download from: https://nodejs.org/

4. **Git**
   - For cloning the repository
   - Download from: https://git-scm.com/

5. **SQLite**
   - Bundled with PHP, but verify PHP has SQLite support: `php -m | grep sqlite`

### External Dependencies

- **WreckfestController** (C# service)
  - This Laravel app communicates with a Wreckfest Controller service
  - Repository: https://github.com/tkprocat/WreckfestController
  - Must be running on `https://localhost:5101` (or configure a different URL)

---

## System Requirements

- **Operating System**: Windows 10+, Linux, or macOS
- **PHP**: 8.4 or higher
- **Memory**: 512MB minimum (1GB+ recommended)
- **Disk Space**: 500MB for application + database
- **Web Server**: Apache, Nginx, or Laravel's built-in server (for development)

---

## Installation Steps

WreckfestWeb offers two installation methods:

### Option A: Quick Installation (Recommended for Most Users)

Use the interactive installation wizard that guides you through the entire setup process.

```bash
# 1. Clone the repository
git clone https://github.com/tkprocat/WreckfestWeb.git WreckfestWeb
cd WreckfestWeb

# 2. Install PHP dependencies
composer install

# 3. Set up environment file
cp .env.example .env
php artisan key:generate

# 4. Run the interactive installer
php artisan wreckfest:install
```

The installer will:
- ✓ Check prerequisites (PHP version, extensions, etc.)
- ✓ Create the SQLite database
- ✓ Generate secure Reverb keys automatically
- ✓ Configure Wreckfest API connection
- ✓ Set up optional features (AI assistant, SFTP backups)
- ✓ Run database migrations and seeders
- ✓ Create your admin user
- ✓ Build frontend assets

**That's it!** Skip to [Running the Application](#running-the-application).

---

### Option B: Manual Installation (For Advanced Users)

Prefer to know exactly what's happening? Follow the detailed manual installation steps below.

This method gives you full control and understanding of each configuration step.

### 1. Clone the Repository

```bash
git clone https://github.com/tkprocat/WreckfestWeb.git WreckfestWeb
cd WreckfestWeb
```

### 2. Install PHP Dependencies

```bash
composer install
```

This will install all Laravel and PHP packages defined in `composer.json`.

### 3. Install JavaScript Dependencies

```bash
npm install
```

This will install Tailwind CSS, Alpine.js, Laravel Echo, and other frontend dependencies.

### 4. Configure Environment Variables

Copy the example environment file and generate an application key:

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Edit the `.env` File

Open `.env` in your text editor and configure the following:

#### **Required Configuration**

```env
# Application Settings
APP_NAME=WreckfestWeb
APP_ENV=production  # Use 'local' for development
APP_DEBUG=false     # Set to 'true' only in development
APP_URL=https://your-domain.com

# Database (SQLite - default)
DB_CONNECTION=sqlite
# The database file will be created in the next step

# Wreckfest API
WRECKFEST_API_URL=https://localhost:5101/api
# Point this to your WreckfestController service URL
```

#### **Broadcasting (Required for Real-time Features)**

```env
BROADCAST_CONNECTION=reverb

# Reverb WebSocket Server
REVERB_APP_ID=wreckfest-app
REVERB_APP_KEY=your-reverb-app-key
REVERB_APP_SECRET=your-reverb-app-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http  # Use 'https' in production with SSL

# Vite Configuration (for frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Generate secure keys for Reverb:**

Run these commands to generate random secure keys:
```bash
# Generate REVERB_APP_KEY (32 character string)
php -r "echo bin2hex(random_bytes(16)) . PHP_EOL;"

# Generate REVERB_APP_SECRET (64 character string)
php -r "echo bin2hex(random_bytes(32)) . PHP_EOL;"
```

Copy the output from these commands and paste them into your `.env` file:
- First output → `REVERB_APP_KEY`
- Second output → `REVERB_APP_SECRET`

Example:
```env
REVERB_APP_KEY=a1b2c3d4e5f6789012345678901234ab
REVERB_APP_SECRET=1a2b3c4d5e6f7890abcdef1234567890abcdef1234567890abcdef1234567890
```

#### **Optional: AI Assistant (OpenAI)**

If you want the AI track rotation assistant:

```env
# AI Configuration
WRECKFEST_AI_MODEL=gpt-4o-mini
WRECKFEST_AI_ASYNC=true
OPENAI_API_KEY=sk-proj-your-openai-api-key-here
```

### 6. Create the Database

```bash
# For Windows
type nul > database/database.sqlite

# For Linux/macOS
touch database/database.sqlite
```

### 7. Run Database Migrations

```bash
php artisan migrate --seed
```

This will:
- Create all necessary database tables
- Seed weather conditions
- Seed track data with weather relationships
- Seed track tags

### 8. Create an Admin User

```bash
php artisan make:filament-user
```

Follow the prompts to create your admin account:
- Name: Your name
- Email: Your email
- Password: Choose a strong password

### 9. Build Frontend Assets

```bash
# For production
npm run build

# For development (with hot reload)
npm run dev
```

### 10. Set Proper Permissions

Laravel needs write access to `storage/` and `bootstrap/cache/` directories.

#### **For Development (Local Machine)**

If you're running `php artisan serve`, no permission changes are needed - PHP runs as your user.

#### **For Production (Linux/Ubuntu with Nginx/Apache)**

```bash
# Make directories writable
chmod -R 775 storage bootstrap/cache

# Set ownership to web server user
# For Ubuntu/Debian with Nginx:
sudo chown -R www-data:www-data storage bootstrap/cache

# For Ubuntu/Debian with Apache:
sudo chown -R www-data:www-data storage bootstrap/cache

# For CentOS/RHEL with Nginx:
sudo chown -R nginx:nginx storage bootstrap/cache

# For CentOS/RHEL with Apache:
sudo chown -R apache:apache storage bootstrap/cache
```

**Not sure which user?** Check with:
```bash
ps aux | grep -E 'nginx|apache|httpd' | grep -v grep
```
Look for the user in the first column (usually `www-data`, `nginx`, or `apache`).

#### **For Windows (Development)**

No permission changes needed if using:
- Laravel Herd
- Laravel Valet for Windows
- `php artisan serve`

#### **For Windows (Production with IIS)**

1. Right-click `storage` and `bootstrap/cache` folders
2. Properties → Security → Edit
3. Add `IIS_IUSRS` group with Modify permissions
4. Click Apply

---

## Configuration

### Database Session Table

Since the app uses database sessions, create the sessions table:

```bash
php artisan session:table
php artisan migrate
```

### Queue and Cache Tables

For background jobs and caching:

```bash
php artisan queue:table
php artisan cache:table
php artisan migrate
```

### Storage Link

Create a symbolic link for public file access:

```bash
php artisan storage:link
```

---

## Running the Application

### Development Mode

**Option 1: Using Laravel's built-in server**

```bash
# Terminal 1: Run Laravel
php artisan serve

# Terminal 2: Run Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Build assets (watch mode)
npm run dev
```

Access the application at: `http://localhost:8000`

**Option 2: Using Laravel Herd (Recommended for Windows)**

If using [Laravel Herd](https://herd.laravel.com/):
1. Add the project to Herd
2. Access via `https://wreckfestweb.test`
3. Reverb will automatically use Herd's SSL certificates

### Production Mode

**Using Nginx or Apache:**

1. Point your web server document root to the `public/` directory
2. Configure SSL certificates
3. Set up a process manager for Reverb:

**Reverb as a Service (systemd example):**

```ini
# /etc/systemd/system/reverb.service
[Unit]
Description=Laravel Reverb WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/WreckfestWeb
ExecStart=/usr/bin/php artisan reverb:start
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start:
```bash
sudo systemctl enable reverb
sudo systemctl start reverb
```

**Queue Worker (for background jobs):**

```bash
php artisan queue:work --daemon
```

---

## Optional Features

### 1. Backups (SFTP)

WreckfestWeb uses [Spatie Laravel Backup](https://spatie.be/docs/laravel-backup/v8/introduction) for automated backups.

Configure automatic backups to a remote SFTP server:

```env
BACKUP_SFTP_ENABLED=true
BACKUP_SFTP_HOST=backup-server.example.com
BACKUP_SFTP_PORT=22
BACKUP_SFTP_USERNAME=backup-user
BACKUP_SFTP_ROOT=/backups/wreckfest

# Use either password or private key authentication
BACKUP_SFTP_PASSWORD=your-secure-password
# OR
# BACKUP_SFTP_PRIVATE_KEY=/path/to/private/key
# BACKUP_SFTP_PASSPHRASE=passphrase-if-needed
```

Schedule backups in your cron:
```bash
# Run Laravel's scheduler every minute
* * * * * cd /path/to/WreckfestWeb && php artisan schedule:run >> /dev/null 2>&1
```

**For more backup options** (email notifications, multiple destinations, cleanup strategies, etc.), see the [Spatie Laravel Backup documentation](https://spatie.be/docs/laravel-backup/v8/introduction).

### 2. Email Notifications

Configure mail settings for notifications:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS="noreply@wreckfest.example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Troubleshooting

**Note:** If you used the installer (`php artisan wreckfest:install`) and encountered issues, you can:
- Run it again with the `--force` flag: `php artisan wreckfest:install --force`
- Or follow the manual installation steps above for more control

---

### Issue: "Class 'SQLite3' not found"

**Solution:** Enable SQLite extension in `php.ini`:
```ini
extension=sqlite3
extension=pdo_sqlite
```

Restart your web server/PHP-FPM.

### Issue: WebSocket connection fails

**Solution:**
1. Verify Reverb is running: `php artisan reverb:start`
2. Check `.env` has correct `REVERB_*` settings
3. Ensure firewall allows connections on `REVERB_PORT`
4. Check browser console for connection errors

### Issue: "Connection refused" to WreckfestController

**Solution:**
1. Ensure WreckfestController C# service is running
2. Verify `WRECKFEST_API_URL` in `.env` matches controller URL
3. Check SSL certificates (or use `http://` for local dev)

### Issue: CSS/JS assets not loading

**Solution:**
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan optimize:clear

# Regenerate manifest
php artisan config:cache
```

### Issue: Permission denied errors

**Solution:**
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Clear cached config/routes/views
php artisan optimize:clear
```

### Issue: Session not persisting

**Solution:**
Ensure sessions table exists:
```bash
php artisan session:table
php artisan migrate
```

---

## Next Steps

### 1. Access the Admin Panel

Visit `https://your-domain.com` (or `http://localhost:8000` if using `php artisan serve`).

Click the **"Login"** button in the top right corner and log in with the credentials you created earlier.

### 2. Configure Server Settings

- Go to **Server Config** to set server name, max players, etc.
- Go to **Track Rotation** to build your track rotation
- Go to **Server Control** to start/stop the server

### 3. Create Events

- Navigate to **Events** to schedule future server configurations
- Deploy event schedules to the WreckfestController

### 4. Explore Features

- **Track Browser**: Browse all available tracks with filtering
- **Manage Collections**: Save and load track rotation presets
- **AI Assistant**: Use the AI chat on Track Rotation page (if OpenAI configured)
- **Dashboard**: Monitor current players in real-time

### 5. Public Page

The public homepage displays:
- Server status
- Current players
- Current track rotation
- Upcoming events

Share `https://your-domain.com` with your community!

---

## Additional Resources

- **Project Documentation**: See [CLAUDE_GUIDE.md](CLAUDE_GUIDE.md) for full project details
- **Laravel Documentation**: https://laravel.com/docs/12.x
- **Filament Documentation**: https://filamentphp.com/docs
- **Issue Tracker**: Report bugs and issues in your repository's issue tracker

---

## Support

For issues or questions:
1. Check the [Troubleshooting](#troubleshooting) section
2. Review the [CLAUDE_GUIDE.md](CLAUDE_GUIDE.md)
3. Check application logs: `storage/logs/laravel.log`
4. Open an issue in the repository

---

**Installation complete!** 🎉

You now have WreckfestWeb installed and ready to manage your Wreckfest dedicated server.
